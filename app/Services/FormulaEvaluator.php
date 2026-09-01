<?php

namespace App\Services;

use RuntimeException;

/**
 * Interprète les formules JetFormBuilder "calculated field" telles qu'exportées
 * du site WordPress d'origine, sans les retranscrire à la main.
 *
 * Grammaire supportée (syntaxe JS-like utilisée par JetFormBuilder) :
 *   ternaire   := or ('?' ternaire ':' ternaire)?
 *   or         := and ('||' and)*
 *   and        := equality ('&&' equality)*
 *   equality   := relational (('==' | '!=') relational)*
 *   relational := additive (('<=' | '>=' | '<' | '>') additive)*
 *   additive   := multiplicative (('+' | '-') multiplicative)*
 *   multiplicative := unary (('*' | '/') unary)*
 *   unary      := ('!' | '-')? primary
 *   primary    := NUMBER | STRING | PLACEHOLDER | '(' ternaire ')'
 *
 * Sémantique volontairement alignée sur JS (pas PHP) pour || et && :
 * `a || b` renvoie a si a est "truthy", sinon b (comme en JS), et pas un booléen
 * comme le ferait le || natif de PHP. C'est cette différence qui rendait risquée
 * une traduction manuelle formule par formule.
 */
class FormulaEvaluator
{
    private array $tokens = [];
    private int $pos = 0;

    /**
     * @param  array<string, mixed>  $data  Valeurs résolues des champs, indexées par nom de champ.
     */
    public function evaluate(string $formula, array $data): mixed
    {
        $this->tokens = $this->tokenize($formula);
        $this->pos = 0;

        $result = $this->parseTernary($data);

        if ($this->pos < count($this->tokens)) {
            throw new RuntimeException('Jetons inattendus en fin de formule : '.json_encode(array_slice($this->tokens, $this->pos)));
        }

        return $result;
    }

    /**
     * @return array<int, array{type: string, value: mixed}>
     */
    private function tokenize(string $formula): array
    {
        $tokens = [];
        $len = strlen($formula);
        $i = 0;

        while ($i < $len) {
            $c = $formula[$i];

            if (ctype_space($c)) {
                $i++;

                continue;
            }

            // Placeholder %nom_du_champ%
            if ($c === '%') {
                $j = $i + 1;
                while ($j < $len && $formula[$j] !== '%') {
                    $j++;
                }
                if ($j >= $len) {
                    throw new RuntimeException('Placeholder %...% non fermé dans la formule.');
                }
                $tokens[] = ['type' => 'placeholder', 'value' => substr($formula, $i + 1, $j - $i - 1)];
                $i = $j + 1;

                continue;
            }

            // Chaîne "..."
            if ($c === '"') {
                $j = $i + 1;
                $buf = '';
                while ($j < $len && $formula[$j] !== '"') {
                    $buf .= $formula[$j];
                    $j++;
                }
                if ($j >= $len) {
                    throw new RuntimeException('Chaîne "..." non fermée dans la formule.');
                }
                $tokens[] = ['type' => 'string', 'value' => $buf];
                $i = $j + 1;

                continue;
            }

            // Nombre (entier ou décimal)
            if (ctype_digit($c) || ($c === '.' && $i + 1 < $len && ctype_digit($formula[$i + 1]))) {
                $j = $i;
                while ($j < $len && (ctype_digit($formula[$j]) || $formula[$j] === '.')) {
                    $j++;
                }
                $tokens[] = ['type' => 'number', 'value' => (float) substr($formula, $i, $j - $i)];
                $i = $j;

                continue;
            }

            // Opérateurs à deux caractères
            $two = substr($formula, $i, 2);
            if (in_array($two, ['||', '&&', '==', '!=', '<=', '>='], true)) {
                $tokens[] = ['type' => 'op', 'value' => $two];
                $i += 2;

                continue;
            }

            // Opérateurs à un caractère
            if (in_array($c, ['(', ')', '?', ':', '+', '-', '*', '/', '<', '>', '!'], true)) {
                $tokens[] = ['type' => 'op', 'value' => $c];
                $i++;

                continue;
            }

            throw new RuntimeException("Caractère inattendu dans la formule : '{$c}' (position {$i}).");
        }

        return $tokens;
    }

    private function peek(): ?array
    {
        return $this->tokens[$this->pos] ?? null;
    }

    private function consumeOp(string $value): bool
    {
        $t = $this->peek();
        if ($t !== null && $t['type'] === 'op' && $t['value'] === $value) {
            $this->pos++;

            return true;
        }

        return false;
    }

    private function isTruthy(mixed $v): bool
    {
        if (is_string($v)) {
            return $v !== '';
        }

        return (bool) $v;
    }

    private function parseTernary(array $data): mixed
    {
        $cond = $this->parseOr($data);

        if ($this->consumeOp('?')) {
            $then = $this->parseTernary($data);
            if (! $this->consumeOp(':')) {
                throw new RuntimeException("':' attendu dans l'expression ternaire.");
            }
            $else = $this->parseTernary($data);

            return $this->isTruthy($cond) ? $then : $else;
        }

        return $cond;
    }

    private function parseOr(array $data): mixed
    {
        $left = $this->parseAnd($data);

        while ($this->consumeOp('||')) {
            if ($this->isTruthy($left)) {
                // court-circuit : on doit quand même avancer le curseur au-delà du membre droit
                $this->skipAnd($data);

                continue;
            }
            $left = $this->parseAnd($data);
        }

        return $left;
    }

    private function skipAnd(array $data): void
    {
        // évalue le membre droit (sans effet de bord côté formule) pour avancer le curseur correctement
        $this->parseAnd($data);
    }

    private function parseAnd(array $data): mixed
    {
        $left = $this->parseEquality($data);

        while ($this->consumeOp('&&')) {
            $right = $this->parseEquality($data);
            $left = $this->isTruthy($left) ? $right : $left;
        }

        return $left;
    }

    private function parseEquality(array $data): mixed
    {
        $left = $this->parseRelational($data);

        while (true) {
            if ($this->consumeOp('==')) {
                $left = $this->looseEquals($left, $this->parseRelational($data));
            } elseif ($this->consumeOp('!=')) {
                $left = ! $this->looseEquals($left, $this->parseRelational($data));
            } else {
                break;
            }
        }

        return $left;
    }

    private function looseEquals(mixed $a, mixed $b): bool
    {
        if (is_numeric($a) && is_numeric($b)) {
            return (float) $a === (float) $b;
        }

        return $a == $b;
    }

    private function parseRelational(array $data): mixed
    {
        $left = $this->parseAdditive($data);

        while (true) {
            if ($this->consumeOp('<=')) {
                $left = (float) $left <= (float) $this->parseAdditive($data);
            } elseif ($this->consumeOp('>=')) {
                $left = (float) $left >= (float) $this->parseAdditive($data);
            } elseif ($this->consumeOp('<')) {
                $left = (float) $left < (float) $this->parseAdditive($data);
            } elseif ($this->consumeOp('>')) {
                $left = (float) $left > (float) $this->parseAdditive($data);
            } else {
                break;
            }
        }

        return $left;
    }

    private function parseAdditive(array $data): mixed
    {
        $left = $this->parseMultiplicative($data);

        while (true) {
            if ($this->consumeOp('+')) {
                $left = (float) $left + (float) $this->parseMultiplicative($data);
            } elseif ($this->consumeOp('-')) {
                $left = (float) $left - (float) $this->parseMultiplicative($data);
            } else {
                break;
            }
        }

        return $left;
    }

    private function parseMultiplicative(array $data): mixed
    {
        $left = $this->parseUnary($data);

        while (true) {
            if ($this->consumeOp('*')) {
                $left = (float) $left * (float) $this->parseUnary($data);
            } elseif ($this->consumeOp('/')) {
                $right = (float) $this->parseUnary($data);
                $left = $right == 0.0 ? 0.0 : (float) $left / $right;
            } else {
                break;
            }
        }

        return $left;
    }

    private function parseUnary(array $data): mixed
    {
        if ($this->consumeOp('!')) {
            return ! $this->isTruthy($this->parseUnary($data));
        }
        if ($this->consumeOp('-')) {
            return -1 * (float) $this->parseUnary($data);
        }

        return $this->parsePrimary($data);
    }

    private function parsePrimary(array $data): mixed
    {
        $t = $this->peek();

        if ($t === null) {
            throw new RuntimeException('Fin de formule inattendue.');
        }

        if ($t['type'] === 'number') {
            $this->pos++;

            return $t['value'];
        }

        if ($t['type'] === 'string') {
            $this->pos++;

            return $t['value'];
        }

        if ($t['type'] === 'placeholder') {
            $this->pos++;
            $value = $data[$t['value']] ?? 0;

            return is_numeric($value) ? (float) $value : $value;
        }

        if ($t['type'] === 'op' && $t['value'] === '(') {
            $this->pos++;
            $value = $this->parseTernary($data);
            if (! $this->consumeOp(')')) {
                throw new RuntimeException("')' attendu.");
            }

            return $value;
        }

        throw new RuntimeException('Jeton inattendu : '.json_encode($t));
    }
}
