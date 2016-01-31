CREATE OR REPLACE VIEW {PREFIXE}.view_identification AS
select `adh_adherent`.`adh_id` AS `adh_id`,`adh_adherent`.`adh_id_compte` AS `adh_id_compte`,`mod_module`.`mod_nom` AS `mod_nom` from (((
`adh_adherent` 
join `ide_identification` on((`adh_adherent`.`adh_id` = `ide_identification`.`ide_id_login`))) left join `aut_autorisation` on((`adh_adherent`.`adh_id` = `aut_autorisation`.`aut_id_adherent`))) left join `mod_module` on((`aut_autorisation`.`aut_id_module` = `mod_module`.`mod_id`))) where (`adh_adherent`.`adh_etat` in (1, 3));

CREATE OR REPLACE VIEW {PREFIXE}.view_menu AS select `adh_adherent`.`adh_id` AS `adh_id`,`mod_module`.`mod_id` AS `mod_id`,`mod_module`.`mod_nom` AS `mod_nom`,`mod_module`.`mod_label` AS `mod_label`,`mod_module`.`mod_admin` AS `mod_admin` from ((`adh_adherent` left join `aut_autorisation` on((`adh_adherent`.`adh_id` = `aut_autorisation`.`aut_id_adherent`))) left join `mod_module` on((`aut_autorisation`.`aut_id_module` = `mod_module`.`mod_id`))) where (`adh_adherent`.`adh_etat` in( 1, 3 )) order by `mod_module`.`mod_ordre`;

ALTER TABLE {PREFIXE}.com_commande ADD `com_droit_non_adherent` TINYINT( 1 ) NOT NULL ;

INSERT INTO {PREFIXE}.`vue_vues` (
`vue_id` ,
`vue_id_module` ,
`vue_nom` ,
`vue_label` ,
`vue_ordre` ,
`vue_visible`
)
VALUES (
NULL , '2', 'ListeNonAdherent', 'Liste des non Adhérents', '2', '1'
);

CREATE OR REPLACE VIEW {PREFIXE}.view_liste_adherent

AS select `adh_adherent`.`adh_id` AS `adh_id`,`adh_adherent`.`adh_numero` AS `adh_numero`,`adh_adherent`.`adh_nom` AS `adh_nom`,`adh_adherent`.`adh_prenom` AS `adh_prenom`,`adh_adherent`.`adh_courriel_principal` AS `adh_courriel_principal`,`cpt_compte`.`cpt_solde` AS `cpt_solde`,`cpt_compte`.`cpt_label` AS `cpt_label` ,
`adh_adherent`.`adh_etat`

from (`adh_adherent` left join `cpt_compte` on((`adh_adherent`.`adh_id_compte` = `cpt_compte`.`cpt_id`))) 

where (`adh_adherent`.`adh_etat` in(1, 3));
