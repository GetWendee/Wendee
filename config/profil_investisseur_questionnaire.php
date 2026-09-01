<?php

/**
 * Structure ordonnée du questionnaire Profil investisseur : sections, champs,
 * options, et conditions de visibilité. Généré automatiquement à partir de
 * l'export JetFormBuilder d'origine, ne pas éditer à la main.
 *
 * Chaque condition de visibilité : ['field' => nom_du_champ_conditionnant, 'operator' => 'equal', 'value' => valeur_attendue].
 * Un champ avec plusieurs conditions doit les satisfaire toutes (ET).
 */

return [
    [
        'titre' => 'Expérience',
        'champs' => [
            [
                'type' => 'checkbox-field',
                'name' => 'produits_detenus_profil_investisseur',
                'label' => 'Quels produits avez-vous détenus ces 12 derniers mois ?',
                'desc' => 'Cochez toutes les réponses qui vous concernent.',
                'options' => [
                    ['label' => 'Livrets et comptes d’épargne (Livret A, LDDS, PEL…)', 'value' => 'comptes_et_livrets_epargne_profil_investisseur'],
                    ['label' => 'Assurance-vie / capitalisation', 'value' => 'contrats_assurance-vie_ou_capitalisation_profil_investisseur'],
                    ['label' => 'Compte-titres / PEA', 'value' => 'comptes_titres_profil_investisseur'],
                    ['label' => 'Épargne retraite (PER, PERP, Madelin…)', 'value' => 'per_perp_madelin_profil_investisseur'],
                    ['label' => 'Épargne salariale (PEE, PEI)', 'value' => 'produit_epargne_salariale_profil_investisseur'],
                    ['label' => 'Investissements / SCPI (FIP, FCPI, SCPI)', 'value' => 'capital_investissement_profil_investisseur'],
                    ['label' => 'Je préfère ne pas répondre.', 'value' => 'non_reponse_produits_detenus_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'modes_de_gestion_profil_investisseur',
                'label' => 'Comment vos placements ont-ils été gérés ?',
                'desc' => 'Cochez la ou les réponses qui vous correspondent',
                'options' => [
                    ['label' => 'Gestion directe – Je gère moi-même', 'value' => 'gestion_directe_profil_investisseur'],
                    ['label' => 'Gestion conseillée – Avec un conseiller', 'value' => 'gestion_conseillee_profil_investisseur'],
                    ['label' => 'Gestion sous mandat – Gestion déléguée', 'value' => 'gestion_sous_mandat_profil_investisseur'],
                    ['label' => 'Je préfère ne pas répondre.', 'value' => 'non_reponse_type_gestion_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'assurance_vie_et_capitalisation_profil_investisseur',
                'label' => 'Assurance-vie et capitalisation',
                'desc' => 'Connaissez-vous ce type de produit ?',
                'options' => [
                    ['label' => 'Oui', 'value' => 'oui_ass_vie_capitalisation_profil_investisseur'],
                    ['label' => 'Non', 'value' => 'non_ass_vie_capitalisation_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_assurance_vie_et_capitalisation_profil_investisseur',
                'label' => 'Quelle affirmation est correcte selon vous ?',
                'options' => [
                    ['label' => 'La clause bénéficiaire sert à désigner les bénéficiaires en cas de décès.', 'value' => 'deces'],
                    ['label' => 'La clause bénéficiaire s’applique en cas de rachat du contrat.', 'value' => 'rachat'],
                    ['label' => 'La clause bénéficiaire sert à fixer les règles de succession.', 'value' => 'conditions_heritiers'],
                    ['label' => 'Je ne sais pas.', 'value' => 'ass_vie_rep1_ne_sais_pas'],
                ],
                'conditions' => [
                    ['field' => 'assurance_vie_et_capitalisation_profil_investisseur', 'operator' => 'equal', 'value' => 'oui_ass_vie_capitalisation_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_assurance_vie_et_capitalisation_profil_investisseur',
                'label' => 'Selon vous, laquelle de ces affirmations est vraie ?',
                'options' => [
                    ['label' => 'Sur un contrat de capitalisation je désigne des bénéficiaires.', 'value' => 'sur_un_contrat_de_capitalisation_je_designe_des_beneficiaires'],
                    ['label' => 'Sur un contrat d\'assurance vie je désigne des bénéficiaires.', 'value' => 'sur_un_contrat_d_ass_vie_je_designe_des_beneficiaires'],
                    ['label' => 'Les bénéficiaires d’un contrat d’assurance-vie ou de capitalisation sont obligatoirement les héritiers.', 'value' => 'la_liste_des_beneficiaires_d_un_contrat_de_capitalisation_ou_d_un_contrat_d_assurance_vie_est_restreinte_a_la_liste_des_heritiers_de_l_assure'],
                    ['label' => 'Je ne sais pas', 'value' => 'ass_vie_rep2_ne_sais_pas'],
                ],
                'conditions' => [
                    ['field' => 'assurance_vie_et_capitalisation_profil_investisseur', 'operator' => 'equal', 'value' => 'oui_ass_vie_capitalisation_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'pea_et_comptes_titres_profil_investisseur',
                'label' => 'PEA et comptes titres',
                'options' => [
                    ['label' => 'Oui', 'value' => 'oui_pea_et_comptes_titres_profil_investisseur'],
                    ['label' => 'Non', 'value' => 'non_pea_et_comptes_titres_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_pea_et_comptes_titres_profil_investisseur',
                'label' => 'Sur quel support les arbitrages ne sont pas imposés ?',
                'options' => [
                    ['label' => 'Compte-titres', 'value' => 'sur_le_compte_titre_si_je_vends_une_action_pour_en_acheter_une_autre_je_ne_paye_pas_d_impot'],
                    ['label' => 'PEA après 5 ans (sans aucune taxe)', 'value' => 'apres_5_ans_les_dividendes_et_plus-values_degagees_par_le_pea_sont_exoneres_d_impot_et_des_prelevements_sociaux_contrairement_au_compte_titre'],
                    ['label' => 'PEA', 'value' => 'Sur_le_pea_si_je_vends_une_action_pour_en_acheter_une_autre_je_ne_paye_pas_d_impot'],
                    ['label' => 'Je ne sais pas', 'value' => 'pea_rep1_je_ne_sais_pas'],
                ],
                'conditions' => [
                    ['field' => 'pea_et_comptes_titres_profil_investisseur', 'operator' => 'equal', 'value' => 'oui_pea_et_comptes_titres_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_pea_et_comptes_titres_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Sur un PEA, je peux acheter tous types de placements (actions, obligations, immobilier…).', 'value' => 'sur_le_pea_je_peux_acheter_des_actions_obligations_immeubles'],
                    ['label' => 'Un compte-titres doit être investi à au moins 75 % en actions hors Union européenne.', 'value' => 'le_compte_titres_doit_etre_investi_à_75_pourcents_au_moins_en_actions_d_entreprises_cotees_en_dehors_de_l_union_europeenne'],
                    ['label' => 'Sur un PEA, je peux acheter des actions européennes.', 'value' => 'sur_le_pea_je_peux_acheter_des_actions_europeennes'],
                    ['label' => 'Je ne sais pas.', 'value' => 'pea_rep2_je_ne_sais_pas'],
                ],
                'conditions' => [
                    ['field' => 'pea_et_comptes_titres_profil_investisseur', 'operator' => 'equal', 'value' => 'oui_pea_et_comptes_titres_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'epargne_retraite_et_entreprise_profil_investisseur',
                'label' => 'Épargne retraite',
                'options' => [
                    ['label' => 'Oui', 'value' => 'oui_epargne_retraite_et_entreprise_profil_investisseur'],
                    ['label' => 'Non', 'value' => 'non_epargne_retraite_et_entreprise_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_epargne_retraite_et_entreprise_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Le Plan d’Épargne Retraite (PER) est en principe bloqué jusqu’au départ à la retraite.', 'value' => 'le_plan_d_epargne_retraite_est_un_placement_dont_les_sommes_investies_sont_normalement_bloquees_jusquau_depart_a_la_retraite'],
                    ['label' => 'Les sommes versées sur un PER peuvent être retirées à tout moment.', 'value' => 'le_plan_d_epargne_retraite_est_un_placement_dont_les_sommes_versees_peuvent_etre_retirees_a_tout_moment'],
                    ['label' => 'Le PER permet de percevoir un capital ou une rente sans aucune fiscalité à la retraite.', 'value' => 'le_plan_d_epargne_retraite_permet_de_recevoir_un_capital_ou_des_revenus_sans_aucune_fiscalite_au_depart_en_retraite'],
                    ['label' => 'Je ne sais pas.', 'value' => 'retraite_rep1_ne_sais_pas'],
                ],
                'conditions' => [
                    ['field' => 'epargne_retraite_et_entreprise_profil_investisseur', 'operator' => 'equal', 'value' => 'oui_epargne_retraite_et_entreprise_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'epargne_salariale_profil_investisseur',
                'label' => 'Épargne salariale',
                'options' => [
                    ['label' => 'Oui', 'value' => 'oui_epargne_salariale_profil_investisseur'],
                    ['label' => 'Non', 'value' => 'non_epargne_salariale_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_epargne_salariale_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Les sommes investies en épargne salariale sont disponibles à tout moment sans condition.', 'value' => 'epargne_salariale_disponible_tout_temps'],
                    ['label' => 'Les sommes versées sur un PEE sont en principe bloquées pendant 5 ans, sauf cas de déblocage anticipé prévus par la loi.', 'value' => 'epargne_salariale_bloquee_5_ans'],
                    ['label' => 'Les gains issus de l’épargne salariale sont systématiquement soumis à l’impôt sur le revenu.', 'value' => 'epargne_salariale_imposable_ir'],
                    ['label' => 'Je ne sais pas.', 'value' => 'salariale_rep1_ne_sais_pas'],
                ],
                'conditions' => [
                    ['field' => 'epargne_salariale_profil_investisseur', 'operator' => 'equal', 'value' => 'oui_epargne_salariale_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur',
                'label' => 'Fonds euros, produits monétaires, obligataires et actions',
                'options' => [
                    ['label' => 'Oui', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                    ['label' => 'Non', 'value' => 'non_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_fonds_euros_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'Fonds euros', 'value' => 'fonds_euros_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_fonds_euros_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'En cas de baisse des marchés financiers, les fonds en euros baissent comme les actions.', 'value' => 'en_cas_de_baisse_des_marches_financiers_votre_investissement_en_fonds_euros_va_subir_la_meme_evolution'],
                    ['label' => 'Les fonds en euros investissent surtout sur des placements prudents et protègent le capital.', 'value' => 'les_fonds_en_euros_sont_composes_essentiellement_d_investissements_obligataires_garantis_par_la_compagnie_vous_assurant_de_ne_pas_perdre_votre_capital'],
                    ['label' => 'Sur le long terme, les fonds en euros rapportent plus que les placements risqués.', 'value' => 'a_long_terme_les_rendements_des_fonds_euros_sont_plus_eleves_que_ceux_des_unites_de_compte'],
                    ['label' => 'Je ne sais pas.', 'value' => 'euros_rep1_ne_sais_pas'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                    ['field' => 'connaissance_fonds_euros_profil_investisseur', 'operator' => 'equal', 'value' => 'fonds_euros_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_fonds_euros_profil_investisseur',
                'label' => 'Combien d’opérations avez-vous réalisées ces 12 derniers mois ?',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'fond_euro_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'fond_euro_de_1_a_5'],
                    ['label' => 'Plus de 5', 'value' => 'fond_euro_plus_de_5'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                    ['field' => 'connaissance_fonds_euros_profil_investisseur', 'operator' => 'equal', 'value' => 'fonds_euros_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_produits_monetaires_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'Produits monétaires', 'value' => 'produits_monetaires_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_produits_monetaires_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Les fonds monétaires investissent sur des placements de très court terme.', 'value' => 'les_fonds_monetaires_sont_composes_principalement_de_titres_de_creances_negociables_tcn_de_bons_du_tresor_ainsi_que_d_obligations_à_court_terme'],
                    ['label' => 'Les fonds monétaires sont faits pour investir sur le long terme.', 'value' => 'l_investissement_sur_des_opc_monetaires_est_parfaitement_adapte_pour_un_investissement_de_long_terme'],
                    ['label' => 'Avec les fonds monétaires, le capital est garanti.', 'value' => 'en_investissant_sur_des_fonds_monetaires_le_capital_est_garanti'],
                    ['label' => 'Je ne sais pas.', 'value' => 'monetaire_rep1_ne_sais_pas'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                    ['field' => 'connaissance_produits_monetaires_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_monetaires_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_produits_monetaires_profil_investisseur',
                'label' => 'Combien d’opérations avez-vous réalisées ces 12 derniers mois ?',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'fond_monetaire_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'fond_monetaire_de_1_a_5'],
                    ['label' => 'Plus de 5', 'value' => 'fond_monetaire_plus_de_5'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                    ['field' => 'connaissance_produits_monetaires_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_monetaires_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_produits_obligataires_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'Produits obligataires', 'value' => 'produits_obligataires_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_produits_obligataires_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Les obligations ne présentent aucun risque de non-remboursement.', 'value' => 'les_obligations_sont_des_dettes_d_etat_ou_d_entreprise_pour_lesquelles_le_defaut_de_remboursement_des_organismes_emprunteurs_est_inexistant'],
                    ['label' => 'Un taux d’intérêt élevé signifie toujours un risque faible.', 'value' => 'pour_une_obligation,_un_taux_d_interet_eleve_indique_un_risque_faible'],
                    ['label' => 'La valeur des obligations évolue en fonction des taux d’intérêt.', 'value' => 'la_performance_d_un_fonds_obligataire_varie_avec_les_evolutions_des_taux_d_interet'],
                    ['label' => 'Je ne sais pas.', 'value' => 'obligation_rep1_ne_sais_pas'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                    ['field' => 'connaissance_produits_obligataires_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_obligataires_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_produits_obligataires_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'obligataire_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'obligataire_de_1_a_5'],
                    ['label' => 'Plus de 5', 'value' => 'obligataire_plus_de_5'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                    ['field' => 'connaissance_produits_obligataires_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_obligataires_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_produits_actions_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'Produits actions', 'value' => 'produits_actions_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_produits_actions_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Les actions sont des placements à court terme.', 'value' => 'Les_actions_repondent_a_un_investissement_a_court_terme'],
                    ['label' => 'Le prix d’une action dépend de la situation de l’entreprise et de l’économie.', 'value' => 'Les_variations_du_cours_de_l_action_dependent_de_la_sante_financiere_de_l_entreprise_et_de_son_environnement_economique'],
                    ['label' => 'Je ne sais pas.', 'value' => 'actions_rep1_ne_sais_pas'],
                    ['label' => 'Les entreprises versent toujours des dividendes aux actionnaires.', 'value' => 'Avec_des_actions_l_investisseur_beneficie_de_revenus_reguliers_car_les_entreprises_ont_l_obligation_de_verser_des_dividendes_aux_actionnaires'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                    ['field' => 'connaissance_produits_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_actions_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_produits_actions_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'produit_action_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'produit_action_de_1_a_5'],
                    ['label' => 'Plus de 5', 'value' => 'produit_action_plus_de_5'],
                ],
                'conditions' => [
                    ['field' => 'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur'],
                    ['field' => 'connaissance_produits_actions_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_actions_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur',
                'label' => 'Défiscalisation, immobilier et produits structurés',
                'options' => [
                    ['label' => 'Oui', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['label' => 'Non', 'value' => 'non_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_scpi_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'SCPI', 'value' => 'scpi_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_scpi_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Investir en SCPI permet de mutualiser le risque locatif entre plusieurs biens.', 'value' => 'scpi_oui'],
                    ['label' => 'En cas de revente, la société de gestion rachète toujours les parts rapidement et au prix du marché.', 'value' => 'scpi_non1'],
                    ['label' => 'Les revenus des SCPI sont garantis et le capital est protégé à tout moment.', 'value' => 'scpi_non2'],
                    ['label' => 'Je ne sais pas.', 'value' => 'scpi_nsp'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_scpi_profil_investisseur', 'operator' => 'equal', 'value' => 'scpi_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_scpi_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'scpi_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'scpi_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'scpi_plus5'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_scpi_profil_investisseur', 'operator' => 'equal', 'value' => 'scpi_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_ocpi_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'OCPI', 'value' => 'ocpi_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_ocpi_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Un OPCI est un placement immobilier de défiscalisation.', 'value' => 'ocpi_non1'],
                    ['label' => 'Les OPCI offrent un rendement garanti.', 'value' => 'ocpi_non2'],
                    ['label' => 'Un OPCI est en général plus liquide qu’une SCPI.', 'value' => 'ocpi_oui'],
                    ['label' => 'Je ne sais pas.', 'value' => 'ocpi_nsp'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_ocpi_profil_investisseur', 'operator' => 'equal', 'value' => 'ocpi_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_ocpi_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'ocpi_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'ocpi_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'ocpi_plus5'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_ocpi_profil_investisseur', 'operator' => 'equal', 'value' => 'ocpi_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => ' Capital investissement ou Private equity (FCPR, FCPI, FIP)', 'value' => 'capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Investir dans un FIP/FCPI est forcément un investissement gagnant grâce au gain fiscal.', 'value' => 'investir_dans_un_fip_fcpi_est_forcement_un_investissement_gagnant_grace_au_gain_fiscal'],
                    ['label' => 'Le capital-investissement consiste à acheter des actions cotées en bourse.', 'value' => 'investir_dans_le_capital_investissement_c_est_investir_dans_des_titres_cotes_en_bourse_avec_un_fort_potentiel_de_croissance'],
                    ['label' => 'Les FIP/FCPI sont des placements risqués à conserver plusieurs années.', 'value' => 'les_fip_fcpi_sont_des_placements_risques_qui_doivent_etre_conserves_pendant_6_a_8_ans'],
                    ['label' => 'Je ne sais pas.', 'value' => 'capital_ne_sais_pas'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur', 'operator' => 'equal', 'value' => 'capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'capital_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'capital_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'capital_plus5'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur', 'operator' => 'equal', 'value' => 'capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_produits_structures_titres_de_creance_structures_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'Produits structurés', 'value' => 'produits_structures_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_produits_structures_titres_de_creance_structures_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Les produits structurés peuvent offrir une garantie du capital.', 'value' => 'produit_oui'],
                    ['label' => 'Les coupons non versés avec un effet mémoire sont définitivement perdus.', 'value' => 'produit_non1'],
                    ['label' => 'Les produits structurés peuvent comporter un risque de perte en capital.', 'value' => 'produit_non2'],
                    ['label' => 'Je ne sais pas.', 'value' => 'produit_nsp'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_produits_structures_titres_de_creance_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_structures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_produits_structures_titres_de_creance_structures_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'structure_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'structure_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'structure_plus5'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_produits_structures_titres_de_creance_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_structures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_sofica_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'SOFICA', 'value' => 'sofica_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_sofica_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Les SOFICA offrent un rendement élevé garanti.', 'value' => 'sofica_non1'],
                    ['label' => 'Les SOFICA sont totalement liquides à tout moment.', 'value' => 'sofica_oui'],
                    ['label' => 'Les SOFICA servent à financer le cinéma et l’audiovisuel.', 'value' => 'sofica_non2'],
                    ['label' => 'Je ne sais pas.', 'value' => 'sofica_nsp'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_sofica_profil_investisseur', 'operator' => 'equal', 'value' => 'sofica_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_sofica_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'sofica_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'sofica_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'sofica_plus5'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_sofica_profil_investisseur', 'operator' => 'equal', 'value' => 'sofica_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_produits_obligataires_complexes_profil_investisseur',
                'label' => null,
                'desc' => '(qui comportent un instrument dérivé obligations convertibles, ORA, EMTN)',
                'options' => [
                    ['label' => 'Produits obligataires complexes', 'value' => 'produits_obligataires_complexes_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_produits_obligataires_complexes_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Une obligation convertible rapporte toujours plus qu’une action en période de hausse.', 'value' => 'poc_non2'],
                    ['label' => 'Ces produits peuvent comporter un risque de perte en capital.', 'value' => 'poc_oui'],
                    ['label' => 'Les obligations convertibles ne présentent aucun risque de défaut.', 'value' => 'poc_non1'],
                    ['label' => 'Je ne sais pas.', 'value' => 'poc_nsp'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_produits_obligataires_complexes_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_obligataires_complexes_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_produits_obligataires_complexes_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'poc_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'poc_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'poc_plus5'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_produits_obligataires_complexes_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_obligataires_complexes_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_produits_actions_complexes_profil_investisseur',
                'label' => null,
                'desc' => '(non cotés ou admis sur un marché non règlementé (Euronext Growth, Euronext Access ou un autre Multilateral Trading Facility))',
                'options' => [
                    ['label' => 'Produits actions complexes', 'value' => 'produits_actions_complexes_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_produits_actions_complexes_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Euronext Access est un marché réglementé avec peu de risques.', 'value' => 'pac_non1'],
                    ['label' => 'Les actions cotées hors marché réglementé peuvent être plus difficiles à revendre.', 'value' => 'pac_oui'],
                    ['label' => 'Les marchés non réglementés sont adaptés uniquement aux placements de court terme.', 'value' => 'pac_non2'],
                    ['label' => 'Je ne sais pas.', 'value' => 'pac_nsp'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_produits_actions_complexes_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_actions_complexes_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_produits_actions_complexes_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'pac_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'pac_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'pac_plus5'],
                ],
                'conditions' => [
                    ['field' => 'defiscalisation_immobilier_et_produits_structures_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur'],
                    ['field' => 'connaissance_produits_actions_complexes_profil_investisseur', 'operator' => 'equal', 'value' => 'produits_actions_complexes_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur',
                'label' => 'Produits à effet de levier et produits boursiers',
                'options' => [
                    ['label' => 'Oui', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['label' => 'Non', 'value' => 'non_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_tracker_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'Tracker', 'value' => 'tracker_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_tracker_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Un tracker suit la performance d’un indice, à la hausse comme à la baisse.', 'value' => 'trackers_oui'],
                    ['label' => 'Les trackers ne peuvent pas être achetés ou vendus en bourse.', 'value' => 'trackers_non1'],
                    ['label' => 'Les trackers sont émis par la Banque centrale européenne.', 'value' => 'trackers_non2'],
                    ['label' => 'Je ne sais pas.', 'value' => 'trackers_nsp'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_tracker_profil_investisseur', 'operator' => 'equal', 'value' => 'tracker_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_tracker_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'tracker_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'tracker_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'tracker_plus5'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_tracker_profil_investisseur', 'operator' => 'equal', 'value' => 'tracker_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_cfd_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'CFD (contrats sur la différence)', 'value' => 'cfd_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_cfd_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Un CFD fonctionne comme l’achat d’une action classique.', 'value' => 'cfd_non1'],
                    ['label' => 'Avec un CFD, le gain est garanti si le marché monte.', 'value' => 'cfd_non2'],
                    ['label' => 'Un CFD permet de gagner ou perdre de l’argent selon l’évolution d’un prix.', 'value' => 'cfd_oui'],
                    ['label' => 'Je ne sais pas.', 'value' => 'cfd_nsp'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_cfd_profil_investisseur', 'operator' => 'equal', 'value' => 'cfd_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_cfd_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'cfd_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'cfd_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'cfd_plus5'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_cfd_profil_investisseur', 'operator' => 'equal', 'value' => 'cfd_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_futures_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'Futures', 'value' => 'futures_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_futures_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Les futures se négocient uniquement de gré à gré entre deux investisseurs.', 'value' => 'futures_non1'],
                    ['label' => 'Les futures utilisent un effet de levier qui peut amplifier les gains comme les pertes.', 'value' => 'futures_oui'],
                    ['label' => 'L’effet de levier des futures augmente les gains sans augmenter les pertes.', 'value' => 'futures_non2'],
                    ['label' => 'Je ne sais pas.', 'value' => 'futures_nsp'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_futures_profil_investisseur', 'operator' => 'equal', 'value' => 'futures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_futures_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'futures_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'futures_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'futures_plus5'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_futures_profil_investisseur', 'operator' => 'equal', 'value' => 'futures_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_options_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'Options', 'value' => 'options_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_options_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Une option donne le droit d’acheter ou de vendre un actif à un prix fixé à l’avance.', 'value' => 'options_oui'],
                    ['label' => 'Un put est une option d’achat.', 'value' => 'options_non1'],
                    ['label' => 'Le prix d’une option dépend uniquement de l’offre et de la demande.', 'value' => 'options_non2'],
                    ['label' => 'Je ne sais pas.', 'value' => 'options_nsp'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_options_profil_investisseur', 'operator' => 'equal', 'value' => 'options_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_options_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'options_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'options_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'options_plus5'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_options_profil_investisseur', 'operator' => 'equal', 'value' => 'options_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_warrants_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'Warrants', 'value' => 'warrants_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_warrants_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Une baisse de la volatilité fait automatiquement augmenter la valeur d’un warrant.', 'value' => 'warrants_non1'],
                    ['label' => 'Un warrant permet de miser à la hausse ou à la baisse avec un effet de levier.', 'value' => 'warrants_oui'],
                    ['label' => 'Un warrant prend de la valeur automatiquement à l’approche de l’échéance.', 'value' => 'warrants_non2'],
                    ['label' => 'Je ne sais pas.', 'value' => 'warrants_nsp'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_warrants_profil_investisseur', 'operator' => 'equal', 'value' => 'warrants_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_warrants_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'warrants_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'warrants_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'warrants__plus5'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_warrants_profil_investisseur', 'operator' => 'equal', 'value' => 'warrants_profil_investisseur'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'connaissance_turbos_profil_investisseur',
                'label' => null,
                'desc' => '(qui comportent un instrument dérivé obligations convertibles, ORA, EMTN)',
                'options' => [
                    ['label' => 'Turbos', 'value' => 'turbos_profil_investisseur'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_1_turbos_profil_investisseur',
                'label' => 'Quelle affirmation est correcte ?',
                'options' => [
                    ['label' => 'Un turbo peut être désactivé avant son échéance, entraînant la perte de l’investissement.', 'value' => 'turbos_oui'],
                    ['label' => 'Les turbos répliquent simplement le rendement du sous-jacent sans risque.', 'value' => 'turbos_non1'],
                    ['label' => 'Si la barrière est atteinte, le capital investi est automatiquement récupéré.', 'value' => 'turbos_non2'],
                    ['label' => 'Je ne sais pas.', 'value' => 'turbos_nsp'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_turbos_profil_investisseur', 'operator' => 'equal', 'value' => 'turbos_profil_investisseur'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'reponse_2_turbos_profil_investisseur',
                'label' => 'Opérations réalisées au cours des 12 derniers mois :',
                'options' => [
                    ['label' => 'Aucune', 'value' => 'turbos_aucune'],
                    ['label' => 'De 1 à 5', 'value' => 'turbos_1_5'],
                    ['label' => 'Plus de 5', 'value' => 'turbos_plus5'],
                ],
                'conditions' => [
                    ['field' => 'produits_effet_de_levier_et_produits_boursiers_profil_investisseur', 'operator' => 'equal', 'value' => 'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur'],
                    ['field' => 'connaissance_turbos_profil_investisseur', 'operator' => 'equal', 'value' => 'turbos_profil_investisseur'],
                ],
            ],
        ],
    ],
    [
        'titre' => 'Profil de risque',
        'champs' => [
            [
                'type' => 'radio-field',
                'name' => 'aversion_1_profil_investisseur',
                'label' => 'Concernant vos placements, vous diriez plutôt que :',
                'options' => [
                    ['label' => 'Je préfère ne prendre aucun risque et privilégier uniquement des placements sûrs.', 'value' => 'il_ne_faut_pas_prendre_de_risque_on_doit_placer_toutes_ses_economies_dans_des_placements_surs'],
                    ['label' => 'Je peux accepter un peu de risque sur une petite partie de mon épargne.', 'value' => 'on_peut_placer_une_petite_partie_de_ses_economies_sur_des_placements_risques'],
                    ['label' => 'Je peux accepter un risque plus important si le potentiel de gain est plus élevé.', 'value' => 'on_peut_placer_une_part_importante_de_ses_economies_sur_des_actifs_risques_si_le_gain_en_vaut_la_peine'],
                    ['label' => 'Je suis prêt à prendre beaucoup de risques pour viser des gains très élevés.', 'value' => 'on_doit_placer_l_essentiel_de_ses_economies_sur_des_actifs_risques_des_qu_il_y_a_des_chances_de_gains_tres_importants'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'aversion_3_profil_investisseur',
                'label' => 'Quel montant total avez-vous investi ou déplacé ces 12 derniers mois ?',
                'options' => [
                    ['label' => 'Aucun', 'value' => 'aucun_transaction'],
                    ['label' => ' Inférieur ou égal à 3 000 euros ', 'value' => 'inferieur_ou_egal_a_3000_euros_transaction'],
                    ['label' => 'Entre 3 000 et 10 000 euros ', 'value' => 'entre_3 000_et_10000_euros_transaction'],
                    ['label' => 'Supérieur à 10 000 euros', 'value' => 'superieur_a_10000_euros_transaction'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'aversion_4_profil_investisseur',
                'label' => 'Avez-vous déjà subi des pertes sur vos placements financiers ?',
                'options' => [
                    ['label' => 'Oui', 'value' => 'oui_perte_sur_placement_financier'],
                    ['label' => 'Non ', 'value' => 'non_perte_sur_placement_financier'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'aversion_5_profil_investisseur',
                'label' => 'Comment avez-vous réagi face à cette situation ?',
                'options' => [
                    ['label' => 'Vous avez tout vendu.', 'value' => 'vous_avez_tout_vendu'],
                    ['label' => 'Vous avez patienté.', 'value' => 'vous_avez_patiente'],
                    ['label' => 'Vous avez réinvesti sur ces placements financiers.', 'value' => 'vous_avez_reinvesti_sur_ces_placements_financiers'],
                ],
                'conditions' => [
                    ['field' => 'aversion_4_profil_investisseur', 'operator' => 'equal', 'value' => 'oui_perte_sur_placement_financier'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'aversion_2_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'Hypothèse pessimiste', 'value' => 'hypothese_pessimiste'],
                    ['label' => 'Hypothèse moyenne', 'value' => 'hypothese_moyenne'],
                    ['label' => 'Hypothèse optimale', 'value' => 'hypothese_optimale'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'aversion_6_profil_investisseur',
                'label' => null,
                'options' => [
                    ['label' => 'Je conserve le placement actuel', 'value' => 'je_conserve_le_placement_actuel'],
                    ['label' => 'J\'accepte le nouveau placement', 'value' => 'j_accepte_le_nouveau_placement'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'aversion_7_profil_investisseur',
                'label' => 'Quelle option choisissez-vous ?',
                'options' => [
                    ['label' => 'Je conserve le placement actuel', 'value' => 'conserve_placement_actuel'],
                    ['label' => 'J\'accepte le nouveau placement', 'value' => 'accepte_nouveau_placement'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'aversion_8_profil_investisseur',
                'label' => 'Êtes-vous assuré au-delà du minimum obligatoire ?',
                'desc' => '(logement, voiture, vol, responsabilité civile…)',
                'options' => [
                    ['label' => 'Oui', 'value' => 'assure_minimum_oui'],
                    ['label' => 'Non', 'value' => 'assure_minimum_non'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'aversion_9_profil_investisseur',
                'label' => 'Quand vous prenez le train ou l’avion, vous arrivez plutôt :',
                'options' => [
                    ['label' => 'Bien à l\'avance', 'value' => 'bien_a_l_avance'],
                    ['label' => 'Un peu à l\'avance', 'value' => 'un_peu_a_l_avance'],
                    ['label' => 'Au dernier moment', 'value' => 'au_dernier_moment'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'aversion_10_profil_investisseur',
                'label' => 'Devenir propriétaire est une priorité pour sécuriser son logement, êtes-vous d’accord avec cette idée :',
                'options' => [
                    ['label' => 'Tout à fait d\'accord', 'value' => 'tout_a_fait_d_accord'],
                    ['label' => 'Plutôt d\'accord', 'value' => 'plutot_d_accord'],
                    ['label' => 'Pas du tout d\'accord', 'value' => 'pas_du_tou_d_accord'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'aversion_11_profil_investisseur',
                'label' => 'Un proche veut changer de carrière pour un projet plus risqué.*',
                'desc' => '*Quelle est votre réaction ?',
                'options' => [
                    ['label' => 'Non, j\'essaye de l\'en dissuader', 'value' => 'non_j_essaye_de_l_en_dissuader'],
                    ['label' => ' Oui, mais en émettant des réserves ou des conseils de prudence ', 'value' => 'oui_mais_en_emettant_des_reserves_ou_des_conseils_de_prudence'],
                    ['label' => 'Oui, assurément', 'value' => 'oui_assurement'],
                ],
            ],
        ],
    ],
    [
        'titre' => 'Préférences',
        'champs' => [
            [
                'type' => 'select-field',
                'name' => 'profil_investisseur_objetifs',
                'label' => 'Quels sont vos objectifs ?',
            ],
            [
                'type' => 'radio-field',
                'name' => 'preference_2_profil_investisseur',
                'label' => 'Concernant vos projets, quel est votre horizon de placement le plus long ?',
                'options' => [
                    ['label' => 'Moins d’1 an', 'value' => 'placement_tres_court_terme_inferieur_a_1_an'],
                    ['label' => 'Moins de 3 ans', 'value' => 'placement_court_terme_inferieur_a_3_ans'],
                    ['label' => 'Moins de 5 ans', 'value' => 'placement_moyen_terme_inferieur_a_5_ans'],
                    ['label' => 'Plus de 5 ans', 'value' => 'placement_long_terme_superieur_a_5_ans'],
                ],
            ],
        ],
    ],
    [
        'titre' => 'Pertes',
        'champs' => [
            [
                'type' => 'date-field',
                'name' => 'risque_1_profil_investisseur',
                'label' => 'Veuillez indiquer votre date de naissance :',
            ],
            [
                'type' => 'number-field',
                'name' => 'risque_2_profil_investisseur',
                'label' => 'Personnes dans votre foyer :',
            ],
            [
                'type' => 'number-field',
                'name' => 'risque_3_profil_investisseur',
                'label' => 'Nombre de parts fiscales',
            ],
            [
                'type' => 'radio-field',
                'name' => 'risque_4_profil_investisseur',
                'label' => 'Départ à la retraite',
                'options' => [
                    ['label' => 'Je suis déjà à la retraite', 'value' => 'je_suis_deja_a_la_retraite'],
                    ['label' => 'Dans moins de 5 ans', 'value' => 'dans_moins_de_5_ans'],
                    ['label' => 'Dans plus de 5 ans', 'value' => 'dans_plus_de_5_ans'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'risque_5_profil_investisseur',
                'label' => 'Revenus annuels du foyer',
                'options' => [
                    ['label' => 'Inférieur à 25 000 €', 'value' => 'inferieur_a_25000_euros'],
                    ['label' => 'Entre 25 000 € et 50 000 €', 'value' => 'entre_25000_euros_et_50000_euros'],
                    ['label' => 'Entre 50 000 € et 75 000 €', 'value' => 'entre_50000_euros_et_75000_euros'],
                    ['label' => 'Entre 75 000 € et 100 000 €', 'value' => 'entre_75000_euros_et_100000_euros'],
                    ['label' => 'Entre 100 000 € et 150 000 €', 'value' => 'entre_100000_euros_et_150000_euros'],
                    ['label' => 'Entre 150 000 € et 300 000 €', 'value' => 'entre_150000_euros_et_300000_euros'],
                    ['label' => 'Plus de 300 000 €', 'value' => 'plus_de_300000_euros'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'risque_6_profil_investisseur',
                'label' => 'Épargne mensuelle',
                'options' => [
                    ['label' => 'Je n\'épargne pas', 'value' => 'je_n_epargne_pas'],
                    ['label' => 'Entre 0 et 500 €', 'value' => 'entre_0_et_500_euros'],
                    ['label' => 'Entre 500 et 1 000 €', 'value' => 'entre_500_et_1000_euros'],
                    ['label' => 'Entre 1 000 € et 2 000 €', 'value' => 'entre_1000_et_2000_euros'],
                    ['label' => 'Plus de 2 000 €', 'value' => 'plus_de_2000_euros'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'risque_7_profil_investisseur',
                'label' => 'Patrimoine immobilier',
                'options' => [
                    ['label' => 'Je n\'ai pas de patrimoine immobilier ', 'value' => 'je_n_ai_pas_de_patrimoine_immobilier'],
                    ['label' => 'Moins de 100 000 € ', 'value' => 'moins_de_100000_euros'],
                    ['label' => 'Entre 100 000 € et 300 000 €', 'value' => 'entre_100000_et_300000_euros'],
                    ['label' => ' Entre 300 000 € et 500 000 € ', 'value' => 'entre_300000_et_500000_euros'],
                    ['label' => 'Entre 500 000 € et 1 000 000 €', 'value' => 'entre_500000_et_1000000_euros'],
                    ['label' => ' Plus de 1 000 000 €', 'value' => 'plus_de_1000000_euros'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'risque_8_profil_investisseur',
                'label' => 'Patrimoine financier',
                'options' => [
                    ['label' => 'Moins de 20 000 €', 'value' => 'estim_patrimoine_financier_20'],
                    ['label' => ' Entre 20 000 € et 50 000 €', 'value' => 'estim_patrimoine_financier_50'],
                    ['label' => ' Entre 50 000 € et 200 000 € ', 'value' => 'estim_patrimoine_financier_200'],
                    ['label' => 'Plus de 200 000 €', 'value' => 'estim_patrimoine_financier_plus200'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'risque_9_profil_investisseur',
                'label' => 'Emprunts',
                'options' => [
                    ['label' => 'Je ne suis pas endetté(e) ', 'value' => 'je_ne_suis_pas_endette'],
                    ['label' => 'Moins de 500 € ', 'value' => 'moins_de_500_euros'],
                    ['label' => 'Entre 500 et 1 000 € ', 'value' => 'entre_500_et_1000_euros'],
                    ['label' => 'Entre 1 000 € et 2 000 € ', 'value' => 'entre_1000_et_2000_euros'],
                    ['label' => 'Plus de 2 000 €', 'value' => 'plus_de_2000_euros'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'risque_10_profil_investisseur',
                'label' => 'Charges mensuelles',
                'options' => [
                    ['label' => 'Moins de 1 000 €', 'value' => 'moins_de_1000_euros'],
                    ['label' => ' Entre 1 000 € et 2 000 € ', 'value' => 'entre_1000_et_2000_euros'],
                    ['label' => 'Entre 2 000 € et 5 000 € ', 'value' => 'entre_2000_et_5000_euros'],
                    ['label' => 'Plus de 5 000 €', 'value' => 'plus_de_5000_euros'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'risque_11_profil_investisseur',
                'label' => 'Logement principal',
                'options' => [
                    ['label' => 'Locataire Hébergé(e) à titre gratuit', 'value' => 'locataire_heberge_a_titre_gratuit'],
                    ['label' => ' Propriétaire et mon emprunt finit dans plus de 5 ans ', 'value' => 'proprietaire_et_mon_emprunt_finit_dans_plus_de_5_ans'],
                    ['label' => 'Propriétaire et mon emprunt finit dans moins de 5 ans ', 'value' => 'proprietaire_et_mon_emprunt_finit_dans_moins_de_5_ans'],
                    ['label' => 'Propriétaire sans remboursement d\'emprunt', 'value' => 'proprietaire_sans_remboursement_d_emprunt'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'risque_12_profil_investisseur',
                'label' => 'Dépense imprévue',
                'options' => [
                    ['label' => 'Oui', 'value' => 'oui_epargne_suffisante_imprevu'],
                    ['label' => 'Non', 'value' => 'non_epargne_suffisante_imprevu'],
                    ['label' => 'Je ne sais pas', 'value' => 'je_ne_sais_pas'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'risque_13_profil_investisseur',
                'label' => 'Évolution des revenus',
                'options' => [
                    ['label' => 'Vont augmenter régulièrement dans le temps', 'value' => 'vont_augmenter_regulierement_dans_le_temps'],
                    ['label' => ' Devraient rester à peu près stables ', 'value' => 'devraient_rester_a_peu_pres_stables'],
                    ['label' => 'Pourraient baisser ou être irréguliers', 'value' => 'pourraient_baisser_ou_etre_irreguliers'],
                    ['label' => ' Je ne sais pas', 'value' => 'je_ne_sais_pas'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'preference_1_profil_investisseur',
                'label' => 'Parmi les objectifs suivants, cochez ceux qui vous conviennent.',
                'desc' => '(plusieurs réponses possibles)',
                'options' => [
                    ['label' => 'Préservation du capital : Chercher avant tout à protéger son capital et limiter les pertes.', 'value' => 'preservation_du_capital_strategie_d_investissement_prudente_dont_l_objectif_principal_est_de_preserver_le_capital_et_d_eviter_les_pertes_au_sein_d_un_portefeuille_cette_strategie_ne_permet_pas_d_investir_sur_le_marche_action'],
                    ['label' => 'Croissance du capital : Chercher à faire croître son capital en acceptant un risque plus élevé.', 'value' => 'croissance_du_capital_strategie_d_investissement_dont_l_objectif_principal_est_d_augmenter_le_capital_avec_en_contrepartie_un_risque_de_perte_plus_eleve_cette_strategie_permet_de_s_exposer_plus_ou_moins_sur_le_marche_des_actions'],
                    ['label' => 'Revenus réguliers : Privilégier des placements qui génèrent des revenus (dividendes, intérêts, etc.).', 'value' => 'revenus_cette_strategie_privilegie_les_placements_qui_procurent_des_revenus_dividendes_coupons_autres_revenus_distribues'],
                    ['label' => 'Couverture des risques (hedging) : Utiliser des stratégies de protection, réservées aux investisseurs expérimentés.', 'value' => 'hedging_couverture_de_risque_une_strategie_de_hedging_est_une_strategie_de_couverture_elle_consiste_a_couvrir_une_position_ouverte_par_une_autre_position_opposee_c_est_un_objectif_de_placement_adapte_uniquement_aux_investisseurs_experimentes'],
                    ['label' => 'Effet de levier : Prendre des positions plus importantes que le capital investi, avec un risque de pertes élevées.', 'value' => 'exposition_a_effet_de_levier_strategie_d_investissement_qui_vous_permet_contre_couverture_de_prendre_plus_de_positions_sur_les_marches_que_votre_investissement_reel_les_gains_sont_potentiellement_eleves_mais_en_contrepartie_vous_risquez_de_perdre_plus_que_la_somme_reellement_investie'],
                    ['label' => 'Aucun Tous ces objectifs peuvent me convenir.', 'value' => 'aucun_tous_les_objectifs_d_investissement_proposes_peuvent_me_convenir'],
                ],
            ],
        ],
    ],
    [
        'titre' => 'Extra-financier',
        'champs' => [
            [
                'type' => 'radio-field',
                'name' => 'extra_financier_1_profil_investisseur',
                'label' => 'Souhaitez-vous préciser vos préférences en matière de durabilité ?',
                'options' => [
                    ['label' => 'Oui', 'value' => 'oui_interet_extra_financier'],
                    ['label' => 'Non', 'value' => 'non_interet_extra_financier'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'extra_financier_2_profil_investisseur',
                'label' => 'Quels types d’investissements responsables vous intéressent ?',
                'options' => [
                    ['label' => 'Impact environnemental positif : Investir dans des activités bénéfiques pour l’environnement.', 'value' => 'activites_environnementales_vous_souhaitez_investir_dans_des_activites_ayant_un_impact_positif_sur_l_environnement'],
                    ['label' => 'Impact environnemental et/ou social : Investir dans des projets ayant un impact positif sur l’environnement et/ou la société.', 'value' => 'objectif_environnemental_ou_social_vous_souhaitez_que_vos_investissements_repondent_a_un_objectif_environnemental_et_ou_social'],
                    ['label' => 'Réduction des impacts négatifs : Choisir des investissements qui limitent leurs effets négatifs.', 'value' => 'incidences_negatives_vous_souhaitez_selectionner_vos_investissements_en_fonction_de_leur_prise_en_compte_des_principales_incidences_negatives'],
                ],
                'conditions' => [
                    ['field' => 'extra_financier_1_profil_investisseur', 'operator' => 'equal', 'value' => 'oui_interet_extra_financier'],
                ],
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'extra_financier_3_profil_investisseur',
                'label' => 'Sur quels sujets souhaitez-vous être particulièrement attentif ?',
                'desc' => '(plusieurs choix possibles)',
                'options' => [
                    ['label' => 'Émissions de gaz à effet de serre', 'value' => 'gaz_a_effet_serre'],
                    ['label' => 'Protection de la biodiversité', 'value' => 'niveau_d_impact_sur_la_biodiversite'],
                    ['label' => 'Gestion des déchets dangereux', 'value' => 'generation_de_dechets_dangereux'],
                    ['label' => 'Performance énergétique (logement, bâtiments)', 'value' => 'inefficacite_energetique_immobilier'],
                    ['label' => 'Respect des normes internationales', 'value' => 'respect_des_normes_internationales_ocde_nations_unies'],
                    ['label' => 'Contrôle du respect des normes', 'value' => 'processus_de_controle_des_normes_internationales'],
                    ['label' => 'Égalité femmes / hommes', 'value' => 'egalite_hommes_femmes'],
                    ['label' => 'Diversité au sein des conseils d’administration', 'value' => 'diversite_des_genres_au_sein_des_conseils_d_administration'],
                    ['label' => 'Exposition aux armes controversées', 'value' => 'exposition_aux_armes_controversees'],
                ],
                'conditions' => [
                    ['field' => 'extra_financier_1_profil_investisseur', 'operator' => 'equal', 'value' => 'oui_interet_extra_financier'],
                ],
            ],
            [
                'type' => 'radio-field',
                'name' => 'extra_financier_4_profil_investisseur',
                'label' => 'Quelle part de votre investissement souhaitez-vous y consacrer ?',
                'options' => [
                    ['label' => 'Moins de 5 % de votre investissement', 'value' => 'vous_souhaitez_y_consacrer_au_moins_5_pourcents_de_votre_investissement'],
                    ['label' => 'Au moins 25 % de votre investissement', 'value' => 'vous_souhaitez_y_consacrer_au_moins_25_pourcents_de_votre_investissement'],
                    ['label' => 'Au moins 50 % de votre investissement', 'value' => 'vous_souhaitez_y_consacrer_au_moins_50_pourcents_de_votre_investissement'],
                ],
                'conditions' => [
                    ['field' => 'extra_financier_1_profil_investisseur', 'operator' => 'equal', 'value' => 'oui_interet_extra_financier'],
                ],
            ],
            [
                'type' => 'text-field',
                'name' => 'lieu_signature_profil_investisseur',
                'label' => 'Fait à',
            ],
            [
                'type' => 'text-field',
                'name' => 'code_de_verification_client',
                'label' => 'Code de vérification client',
            ],
            [
                'type' => 'checkbox-field',
                'name' => 'acceptation_termes_et_conditions_profil_investisseur',
                'label' => 'Veuillez consulter et accepter les  <a href="/conditions-generales-dutilisation/" target="_blank">conditions générales d\'utilisation</a> et la <a href="/politique-de-confidentialite/" target="_blank">politique de confidentialité</a>.',
                'options' => [
                    ['label' => 'J\'accepte', 'value' => 'acceptation_termes_et_conditions_kyc'],
                ],
            ],
        ],
    ],
];
