<p>Bonjour {{ $apporteur->name }},</p>

<p>
    Le virement de vos commissions sur les contrats suivants vient d'être validé :
</p>

<ul>
    @foreach ($commissions as $commission)
        <li>
            {{ $commission->client?->prenom }} {{ $commission->client?->nom }}
            — {{ $commission->libelle_mission }}
            — {{ number_format((float) $commission->montant_commission, 2, ',', ' ') }} €
        </li>
    @endforeach
</ul>

<p>
    Total : {{ number_format((float) $total, 2, ',', ' ') }} €
</p>
