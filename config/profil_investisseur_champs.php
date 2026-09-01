<?php

/**
 * Champs à choix (radio / checkbox) du questionnaire Profil investisseur,
 * avec le score ('calculate') associé à chaque option, tel qu'exporté du
 * formulaire JetFormBuilder d'origine. Généré automatiquement, ne pas éditer à la main.
 *
 * Structure : nom_du_champ => ['kind' => 'radio'|'checkbox', 'options' => [valeur => score]]
 */

return [
    'produits_detenus_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'comptes_et_livrets_epargne_profil_investisseur' => 2,
            'contrats_assurance-vie_ou_capitalisation_profil_investisseur' => 2,
            'comptes_titres_profil_investisseur' => 2,
            'per_perp_madelin_profil_investisseur' => 2,
            'produit_epargne_salariale_profil_investisseur' => 1,
            'capital_investissement_profil_investisseur' => 1,
            'non_reponse_produits_detenus_profil_investisseur' => 0,
        ],
    ],
    'modes_de_gestion_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'gestion_directe_profil_investisseur' => 10,
            'gestion_conseillee_profil_investisseur' => 6,
            'gestion_sous_mandat_profil_investisseur' => 2,
            'non_reponse_type_gestion_profil_investisseur' => 0,
        ],
    ],
    'assurance_vie_et_capitalisation_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'oui_ass_vie_capitalisation_profil_investisseur' => 1,
            'non_ass_vie_capitalisation_profil_investisseur' => 0,
        ],
    ],
    'reponse_1_assurance_vie_et_capitalisation_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'deces' => 4.5,
            'rachat' => 0,
            'conditions_heritiers' => 0,
            'ass_vie_rep1_ne_sais_pas' => 2,
        ],
    ],
    'reponse_2_assurance_vie_et_capitalisation_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'sur_un_contrat_de_capitalisation_je_designe_des_beneficiaires' => 0,
            'sur_un_contrat_d_ass_vie_je_designe_des_beneficiaires' => 4.5,
            'la_liste_des_beneficiaires_d_un_contrat_de_capitalisation_ou_d_un_contrat_d_assurance_vie_est_restreinte_a_la_liste_des_heritiers_de_l_assure' => 0,
            'ass_vie_rep2_ne_sais_pas' => 2,
        ],
    ],
    'pea_et_comptes_titres_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'oui_pea_et_comptes_titres_profil_investisseur' => 1,
            'non_pea_et_comptes_titres_profil_investisseur' => 0,
        ],
    ],
    'reponse_1_pea_et_comptes_titres_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'sur_le_compte_titre_si_je_vends_une_action_pour_en_acheter_une_autre_je_ne_paye_pas_d_impot' => 0,
            'apres_5_ans_les_dividendes_et_plus-values_degagees_par_le_pea_sont_exoneres_d_impot_et_des_prelevements_sociaux_contrairement_au_compte_titre' => 0,
            'Sur_le_pea_si_je_vends_une_action_pour_en_acheter_une_autre_je_ne_paye_pas_d_impot' => 4.5,
            'pea_rep1_je_ne_sais_pas' => 2,
        ],
    ],
    'reponse_2_pea_et_comptes_titres_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'sur_le_pea_je_peux_acheter_des_actions_obligations_immeubles' => 0,
            'le_compte_titres_doit_etre_investi_à_75_pourcents_au_moins_en_actions_d_entreprises_cotees_en_dehors_de_l_union_europeenne' => 0,
            'sur_le_pea_je_peux_acheter_des_actions_europeennes' => 4.5,
            'pea_rep2_je_ne_sais_pas' => 2,
        ],
    ],
    'epargne_retraite_et_entreprise_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'oui_epargne_retraite_et_entreprise_profil_investisseur' => 1,
            'non_epargne_retraite_et_entreprise_profil_investisseur' => 0,
        ],
    ],
    'reponse_1_epargne_retraite_et_entreprise_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'le_plan_d_epargne_retraite_est_un_placement_dont_les_sommes_investies_sont_normalement_bloquees_jusquau_depart_a_la_retraite' => 9,
            'le_plan_d_epargne_retraite_est_un_placement_dont_les_sommes_versees_peuvent_etre_retirees_a_tout_moment' => 0,
            'le_plan_d_epargne_retraite_permet_de_recevoir_un_capital_ou_des_revenus_sans_aucune_fiscalite_au_depart_en_retraite' => 0,
            'retraite_rep1_ne_sais_pas' => 4,
        ],
    ],
    'epargne_salariale_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'oui_epargne_salariale_profil_investisseur' => 1,
            'non_epargne_salariale_profil_investisseur' => 0,
        ],
    ],
    'reponse_1_epargne_salariale_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'epargne_salariale_disponible_tout_temps' => 0,
            'epargne_salariale_bloquee_5_ans' => 9,
            'epargne_salariale_imposable_ir' => 0,
            'salariale_rep1_ne_sais_pas' => 4,
        ],
    ],
    'fonds_euros_produits_monetaires_obligataires_et_actions_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'Oui_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur' => 1,
            'non_connaissance_fonds_euros_produits_monetaires_obligataires_actions_profil_investisseur' => 0,
        ],
    ],
    'connaissance_fonds_euros_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'fonds_euros_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_fonds_euros_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'en_cas_de_baisse_des_marches_financiers_votre_investissement_en_fonds_euros_va_subir_la_meme_evolution' => 0,
            'les_fonds_en_euros_sont_composes_essentiellement_d_investissements_obligataires_garantis_par_la_compagnie_vous_assurant_de_ne_pas_perdre_votre_capital' => 9,
            'a_long_terme_les_rendements_des_fonds_euros_sont_plus_eleves_que_ceux_des_unites_de_compte' => 0,
            'euros_rep1_ne_sais_pas' => 4,
        ],
    ],
    'reponse_2_fonds_euros_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'fond_euro_aucune' => 0,
            'fond_euro_de_1_a_5' => 5,
            'fond_euro_plus_de_5' => 10,
        ],
    ],
    'connaissance_produits_monetaires_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'produits_monetaires_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_produits_monetaires_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'les_fonds_monetaires_sont_composes_principalement_de_titres_de_creances_negociables_tcn_de_bons_du_tresor_ainsi_que_d_obligations_à_court_terme' => 9,
            'l_investissement_sur_des_opc_monetaires_est_parfaitement_adapte_pour_un_investissement_de_long_terme' => 0,
            'en_investissant_sur_des_fonds_monetaires_le_capital_est_garanti' => 0,
            'monetaire_rep1_ne_sais_pas' => 4,
        ],
    ],
    'reponse_2_produits_monetaires_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'fond_monetaire_aucune' => 0,
            'fond_monetaire_de_1_a_5' => 5,
            'fond_monetaire_plus_de_5' => 10,
        ],
    ],
    'connaissance_produits_obligataires_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'produits_obligataires_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_produits_obligataires_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'les_obligations_sont_des_dettes_d_etat_ou_d_entreprise_pour_lesquelles_le_defaut_de_remboursement_des_organismes_emprunteurs_est_inexistant' => 0,
            'pour_une_obligation,_un_taux_d_interet_eleve_indique_un_risque_faible' => 0,
            'la_performance_d_un_fonds_obligataire_varie_avec_les_evolutions_des_taux_d_interet' => 9,
            'obligation_rep1_ne_sais_pas' => 4,
        ],
    ],
    'reponse_2_produits_obligataires_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'obligataire_aucune' => 0,
            'obligataire_de_1_a_5' => 5,
            'obligataire_plus_de_5' => 10,
        ],
    ],
    'connaissance_produits_actions_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'produits_actions_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_produits_actions_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'Les_actions_repondent_a_un_investissement_a_court_terme' => 0,
            'Les_variations_du_cours_de_l_action_dependent_de_la_sante_financiere_de_l_entreprise_et_de_son_environnement_economique' => 9,
            'actions_rep1_ne_sais_pas' => 4,
            'Avec_des_actions_l_investisseur_beneficie_de_revenus_reguliers_car_les_entreprises_ont_l_obligation_de_verser_des_dividendes_aux_actionnaires' => 0,
        ],
    ],
    'reponse_2_produits_actions_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'produit_action_aucune' => 0,
            'produit_action_de_1_a_5' => 5,
            'produit_action_plus_de_5' => 10,
        ],
    ],
    'defiscalisation_immobilier_et_produits_structures_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'Oui_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur' => 1,
            'non_connaissance_defiscalisation_immobilier_et_produits_structures_profil_investisseur' => 0,
        ],
    ],
    'connaissance_scpi_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'scpi_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_scpi_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'scpi_oui' => 9,
            'scpi_non1' => 0,
            'scpi_non2' => 0,
            'scpi_nsp' => 4,
        ],
    ],
    'reponse_2_scpi_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'scpi_aucune' => 0,
            'scpi_1_5' => 5,
            'scpi_plus5' => 10,
        ],
    ],
    'connaissance_ocpi_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'ocpi_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_ocpi_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'ocpi_non1' => 0,
            'ocpi_non2' => 0,
            'ocpi_oui' => 9,
            'ocpi_nsp' => 4,
        ],
    ],
    'reponse_2_ocpi_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'ocpi_aucune' => 0,
            'ocpi_1_5' => 5,
            'ocpi_plus5' => 10,
        ],
    ],
    'connaissance_capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'investir_dans_un_fip_fcpi_est_forcement_un_investissement_gagnant_grace_au_gain_fiscal' => 0,
            'investir_dans_le_capital_investissement_c_est_investir_dans_des_titres_cotes_en_bourse_avec_un_fort_potentiel_de_croissance' => 0,
            'les_fip_fcpi_sont_des_placements_risques_qui_doivent_etre_conserves_pendant_6_a_8_ans' => 9,
            'capital_ne_sais_pas' => 4,
        ],
    ],
    'reponse_2_capital_investissement_ou_private_equity_fcpr_fcpi_fip_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'capital_aucune' => 0,
            'capital_1_5' => 5,
            'capital_plus5' => 10,
        ],
    ],
    'connaissance_produits_structures_titres_de_creance_structures_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'produits_structures_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_produits_structures_titres_de_creance_structures_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'produit_oui' => 9,
            'produit_non1' => 0,
            'produit_non2' => 0,
            'produit_nsp' => 4,
        ],
    ],
    'reponse_2_produits_structures_titres_de_creance_structures_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'structure_aucune' => 0,
            'structure_1_5' => 5,
            'structure_plus5' => 10,
        ],
    ],
    'connaissance_sofica_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'sofica_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_sofica_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'sofica_non1' => 0,
            'sofica_oui' => 0,
            'sofica_non2' => 9,
            'sofica_nsp' => 4,
        ],
    ],
    'reponse_2_sofica_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'sofica_aucune' => 0,
            'sofica_1_5' => 5,
            'sofica_plus5' => 10,
        ],
    ],
    'connaissance_produits_obligataires_complexes_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'produits_obligataires_complexes_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_produits_obligataires_complexes_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'poc_non2' => 0,
            'poc_oui' => 9,
            'poc_non1' => 0,
            'poc_nsp' => 4,
        ],
    ],
    'reponse_2_produits_obligataires_complexes_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'poc_aucune' => 0,
            'poc_1_5' => 5,
            'poc_plus5' => 10,
        ],
    ],
    'connaissance_produits_actions_complexes_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'produits_actions_complexes_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_produits_actions_complexes_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'pac_non1' => 0,
            'pac_oui' => 9,
            'pac_non2' => 0,
            'pac_nsp' => 4,
        ],
    ],
    'reponse_2_produits_actions_complexes_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'pac_aucune' => 0,
            'pac_1_5' => 5,
            'pac_plus5' => 10,
        ],
    ],
    'produits_effet_de_levier_et_produits_boursiers_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'Oui_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur' => 1,
            'non_connaissance_produits_effet_de_levier_et_produits_boursiers_profil_investisseur' => 0,
        ],
    ],
    'connaissance_tracker_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'tracker_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_tracker_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'trackers_oui' => 9,
            'trackers_non1' => 0,
            'trackers_non2' => 0,
            'trackers_nsp' => 4,
        ],
    ],
    'reponse_2_tracker_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'tracker_aucune' => 0,
            'tracker_1_5' => 5,
            'tracker_plus5' => 10,
        ],
    ],
    'connaissance_cfd_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'cfd_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_cfd_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'cfd_non1' => 0,
            'cfd_non2' => 0,
            'cfd_oui' => 9,
            'cfd_nsp' => 4,
        ],
    ],
    'reponse_2_cfd_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'cfd_aucune' => 0,
            'cfd_1_5' => 5,
            'cfd_plus5' => 10,
        ],
    ],
    'connaissance_futures_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'futures_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_futures_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'futures_non1' => 0,
            'futures_oui' => 9,
            'futures_non2' => 0,
            'futures_nsp' => 4,
        ],
    ],
    'reponse_2_futures_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'futures_aucune' => 0,
            'futures_1_5' => 5,
            'futures_plus5' => 10,
        ],
    ],
    'connaissance_options_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'options_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_options_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'options_oui' => 9,
            'options_non1' => 0,
            'options_non2' => 0,
            'options_nsp' => 4,
        ],
    ],
    'reponse_2_options_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'options_aucune' => 0,
            'options_1_5' => 5,
            'options_plus5' => 10,
        ],
    ],
    'connaissance_warrants_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'warrants_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_warrants_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'warrants_non1' => 0,
            'warrants_oui' => 9,
            'warrants_non2' => 0,
            'warrants_nsp' => 4,
        ],
    ],
    'reponse_2_warrants_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'warrants_aucune' => 0,
            'warrants_1_5' => 5,
            'warrants__plus5' => 10,
        ],
    ],
    'connaissance_turbos_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'turbos_profil_investisseur' => 1,
        ],
    ],
    'reponse_1_turbos_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'turbos_oui' => 9,
            'turbos_non1' => 0,
            'turbos_non2' => 0,
            'turbos_nsp' => 4,
        ],
    ],
    'reponse_2_turbos_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'turbos_aucune' => 0,
            'turbos_1_5' => 5,
            'turbos_plus5' => 10,
        ],
    ],
    'aversion_1_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'il_ne_faut_pas_prendre_de_risque_on_doit_placer_toutes_ses_economies_dans_des_placements_surs' => 0,
            'on_peut_placer_une_petite_partie_de_ses_economies_sur_des_placements_risques' => 3,
            'on_peut_placer_une_part_importante_de_ses_economies_sur_des_actifs_risques_si_le_gain_en_vaut_la_peine' => 6,
            'on_doit_placer_l_essentiel_de_ses_economies_sur_des_actifs_risques_des_qu_il_y_a_des_chances_de_gains_tres_importants' => 10,
        ],
    ],
    'aversion_3_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'aucun_transaction' => 0,
            'inferieur_ou_egal_a_3000_euros_transaction' => 3,
            'entre_3 000_et_10000_euros_transaction' => 6,
            'superieur_a_10000_euros_transaction' => 10,
        ],
    ],
    'aversion_4_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'oui_perte_sur_placement_financier' => 10,
            'non_perte_sur_placement_financier' => 0,
        ],
    ],
    'aversion_5_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'vous_avez_tout_vendu' => 1,
            'vous_avez_patiente' => 5,
            'vous_avez_reinvesti_sur_ces_placements_financiers' => 10,
        ],
    ],
    'aversion_2_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'hypothese_pessimiste' => 1,
            'hypothese_moyenne' => 5,
            'hypothese_optimale' => 10,
        ],
    ],
    'aversion_6_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'je_conserve_le_placement_actuel' => 1,
            'j_accepte_le_nouveau_placement' => 10,
        ],
    ],
    'aversion_7_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'conserve_placement_actuel' => 1,
            'accepte_nouveau_placement' => 10,
        ],
    ],
    'aversion_8_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'assure_minimum_oui' => 1,
            'assure_minimum_non' => 10,
        ],
    ],
    'aversion_9_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'bien_a_l_avance' => 1,
            'un_peu_a_l_avance' => 5,
            'au_dernier_moment' => 10,
        ],
    ],
    'aversion_10_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'tout_a_fait_d_accord' => 1,
            'plutot_d_accord' => 5,
            'pas_du_tou_d_accord' => 10,
        ],
    ],
    'aversion_11_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'non_j_essaye_de_l_en_dissuader' => 1,
            'oui_mais_en_emettant_des_reserves_ou_des_conseils_de_prudence' => 5,
            'oui_assurement' => 10,
        ],
    ],
    'preference_2_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'placement_tres_court_terme_inferieur_a_1_an' => 0,
            'placement_court_terme_inferieur_a_3_ans' => 3,
            'placement_moyen_terme_inferieur_a_5_ans' => 7,
            'placement_long_terme_superieur_a_5_ans' => 10,
        ],
    ],
    'risque_4_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'je_suis_deja_a_la_retraite' => 1,
            'dans_moins_de_5_ans' => 5,
            'dans_plus_de_5_ans' => 10,
        ],
    ],
    'risque_5_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'inferieur_a_25000_euros' => 1,
            'entre_25000_euros_et_50000_euros' => 3,
            'entre_50000_euros_et_75000_euros' => 5,
            'entre_75000_euros_et_100000_euros' => 7,
            'entre_100000_euros_et_150000_euros' => 8,
            'entre_150000_euros_et_300000_euros' => 9,
            'plus_de_300000_euros' => 10,
        ],
    ],
    'risque_6_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'je_n_epargne_pas' => 0,
            'entre_0_et_500_euros' => 3,
            'entre_500_et_1000_euros' => 5,
            'entre_1000_et_2000_euros' => 8,
            'plus_de_2000_euros' => 10,
        ],
    ],
    'risque_7_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'je_n_ai_pas_de_patrimoine_immobilier' => 0,
            'moins_de_100000_euros' => 2,
            'entre_100000_et_300000_euros' => 4,
            'entre_300000_et_500000_euros' => 6,
            'entre_500000_et_1000000_euros' => 8,
            'plus_de_1000000_euros' => 10,
        ],
    ],
    'risque_8_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'estim_patrimoine_financier_20' => 0,
            'estim_patrimoine_financier_50' => 3,
            'estim_patrimoine_financier_200' => 7,
            'estim_patrimoine_financier_plus200' => 10,
        ],
    ],
    'risque_9_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'je_ne_suis_pas_endette' => 1,
            'moins_de_500_euros' => 3,
            'entre_500_et_1000_euros' => 6,
            'entre_1000_et_2000_euros' => 8,
            'plus_de_2000_euros' => 10,
        ],
    ],
    'risque_10_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'moins_de_1000_euros' => 1,
            'entre_1000_et_2000_euros' => 3,
            'entre_2000_et_5000_euros' => 7,
            'plus_de_5000_euros' => 10,
        ],
    ],
    'risque_11_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'locataire_heberge_a_titre_gratuit' => 10,
            'proprietaire_et_mon_emprunt_finit_dans_plus_de_5_ans' => 7,
            'proprietaire_et_mon_emprunt_finit_dans_moins_de_5_ans' => 3,
            'proprietaire_sans_remboursement_d_emprunt' => 1,
        ],
    ],
    'risque_12_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'oui_epargne_suffisante_imprevu' => 0,
            'non_epargne_suffisante_imprevu' => 10,
            'je_ne_sais_pas' => 5,
        ],
    ],
    'risque_13_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'vont_augmenter_regulierement_dans_le_temps' => 0,
            'devraient_rester_a_peu_pres_stables' => 4,
            'pourraient_baisser_ou_etre_irreguliers' => 10,
            'je_ne_sais_pas' => 7,
        ],
    ],
    'preference_1_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'preservation_du_capital_strategie_d_investissement_prudente_dont_l_objectif_principal_est_de_preserver_le_capital_et_d_eviter_les_pertes_au_sein_d_un_portefeuille_cette_strategie_ne_permet_pas_d_investir_sur_le_marche_action' => 0,
            'croissance_du_capital_strategie_d_investissement_dont_l_objectif_principal_est_d_augmenter_le_capital_avec_en_contrepartie_un_risque_de_perte_plus_eleve_cette_strategie_permet_de_s_exposer_plus_ou_moins_sur_le_marche_des_actions' => 3,
            'revenus_cette_strategie_privilegie_les_placements_qui_procurent_des_revenus_dividendes_coupons_autres_revenus_distribues' => 3,
            'hedging_couverture_de_risque_une_strategie_de_hedging_est_une_strategie_de_couverture_elle_consiste_a_couvrir_une_position_ouverte_par_une_autre_position_opposee_c_est_un_objectif_de_placement_adapte_uniquement_aux_investisseurs_experimentes' => 2,
            'exposition_a_effet_de_levier_strategie_d_investissement_qui_vous_permet_contre_couverture_de_prendre_plus_de_positions_sur_les_marches_que_votre_investissement_reel_les_gains_sont_potentiellement_eleves_mais_en_contrepartie_vous_risquez_de_perdre_plus_que_la_somme_reellement_investie' => 2,
            'aucun_tous_les_objectifs_d_investissement_proposes_peuvent_me_convenir' => 10,
        ],
    ],
    'extra_financier_1_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'oui_interet_extra_financier' => 10,
            'non_interet_extra_financier' => 0,
        ],
    ],
    'extra_financier_2_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'activites_environnementales_vous_souhaitez_investir_dans_des_activites_ayant_un_impact_positif_sur_l_environnement' => 3,
            'objectif_environnemental_ou_social_vous_souhaitez_que_vos_investissements_repondent_a_un_objectif_environnemental_et_ou_social' => 3,
            'incidences_negatives_vous_souhaitez_selectionner_vos_investissements_en_fonction_de_leur_prise_en_compte_des_principales_incidences_negatives' => 3,
        ],
    ],
    'extra_financier_3_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'gaz_a_effet_serre' => 1,
            'niveau_d_impact_sur_la_biodiversite' => 1,
            'generation_de_dechets_dangereux' => 1,
            'inefficacite_energetique_immobilier' => 1,
            'respect_des_normes_internationales_ocde_nations_unies' => 1,
            'processus_de_controle_des_normes_internationales' => 1,
            'egalite_hommes_femmes' => 1,
            'diversite_des_genres_au_sein_des_conseils_d_administration' => 1,
            'exposition_aux_armes_controversees' => 1,
        ],
    ],
    'extra_financier_4_profil_investisseur' => [
        'kind' => 'radio',
        'options' => [
            'vous_souhaitez_y_consacrer_au_moins_5_pourcents_de_votre_investissement' => 2,
            'vous_souhaitez_y_consacrer_au_moins_25_pourcents_de_votre_investissement' => 6,
            'vous_souhaitez_y_consacrer_au_moins_50_pourcents_de_votre_investissement' => 10,
        ],
    ],
    'acceptation_termes_et_conditions_profil_investisseur' => [
        'kind' => 'checkbox',
        'options' => [
            'acceptation_termes_et_conditions_kyc' => 1,
        ],
    ],
];
