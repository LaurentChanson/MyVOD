<?php

require_once 'config_db_maj.php';
require_once 'sqlite_db.php';


//classe utilisé pour les paramètres
class config_data {

    public $version;
    public $rep_films_local;
    public $rep_films_web;
    public $rep_films_lan;
    public $affichage_gallerie;
    public $mot_de_pass_admin;
    public $controle_parental;
    public $code_parental;
    public $nb_visu_histo=8;
    public $nb_visu_ajouts=8;
    public $affichage_visionnes_apres_ajouts;
    public $taille_fichiers_64_bits=false;
    public $mots_cles_suppl_google_search="VF";
    public $affichage_liste_tablette = 1;
    public $tri_recherche_def = 0;
    
}

class mot_cle {

    public $id;
    public $mot;

}

class config_db extends config_db_MAJ {

    /**
     * 
     * @return \config objet config
     */
    public function get_config() {
        $config = new config_data();
        $r = parent::get_obj('SELECT * FROM config');
        parent::std_class_to_obj($r, $config);
        return $config;
    }

    public function update_mot_de_passe($new_mdp) {
        $requete = 'UPDATE config SET mot_de_pass_admin=' . sql::chaine_vers_sql($new_mdp);
        parent::execute($requete);
    }

    public function update_parametres($repertoireFilmsLocal, $repertoireWebPartage, $repertoireWebFilms, $affichage_gallerie,$controle_parental,$code_parental,
            $nb_visu_histo,$nb_visu_ajouts,$affichage_visionnes_apres_ajouts,
            $taille_fichiers_64_bits,$mots_cles_suppl_google_search,$affichage_liste_tablette,
            $tri_recherche_def) {
        $sql = sprintf("UPDATE config 
                SET rep_films_local=%s,
                rep_films_lan=%s,
                rep_films_web=%s,
                affichage_gallerie=%s,
                controle_parental=%s,
                code_parental=%s,
                nb_visu_histo=%s,
                nb_visu_ajouts=%s,
                affichage_visionnes_apres_ajouts=%s,
                taille_fichiers_64_bits=%s,
                mots_cles_suppl_google_search=%s,
                affichage_liste_tablette=%s,
                tri_recherche_def=%s", 
                sql::chaine_vers_sql($repertoireFilmsLocal), 
                sql::chaine_vers_sql($repertoireWebPartage), 
                sql::chaine_vers_sql($repertoireWebFilms),
                sql::entier_vers_sql($affichage_gallerie),
                sql::entier_vers_sql($controle_parental),
                sql::chaine_vers_sql($code_parental),
                $nb_visu_histo,$nb_visu_ajouts,$affichage_visionnes_apres_ajouts,
                $taille_fichiers_64_bits,
                sql::chaine_vers_sql($mots_cles_suppl_google_search),
                sql::entier_vers_sql($affichage_liste_tablette),
                sql::entier_vers_sql($tri_recherche_def)
        );

        //var_dump($sql);
        //exit();
        
        parent::execute($sql);
    }

    public function get_liste_mots_cle_ignore($ordre_special_filtre_inverse = false) {
        $sql = "SELECT * FROM mot_cle_ignore m\n";
        if ($ordre_special_filtre_inverse) {
            $sql.=" ORDER BY length(m.mot) DESC,m.mot DESC";
        } else {
            $sql.=" ORDER BY m.mot";
        }

        return $this->get_array_obj($sql);
    }

    public function ajouter_mots_cle_ignore($nouveau_mot) {
        return $this->insert_mot_cle_ignore($nouveau_mot);
    }

    public function enlever_mots_cle_ignore_by_id($id) {
        $sql = "DELETE FROM mot_cle_ignore 
WHERE id=$id";
        return $this->execute($sql);
    }

    public function get_mot_cle_from_id($id) {
        $sql = "SELECT m.mot FROM mot_cle_ignore m
WHERE m.id=$id";
        return $this->get_value($sql);
    }

    private function insert_mot_cle_ignore($mot) {
        return parent::execute("INSERT INTO mot_cle_ignore (mot) VALUES (LOWER(" . sql::chaine_vers_sql($mot) . "))");
    }


    
    /*
     * Ouverture de la bdd
     */

    function __construct() {
        $this->fic_sqlite = self::repertoire_data() . 'config.db';
        $this->open();
        $this->maj_suivantes();
    }

    
    
    
    

    
    
    
    
    /*
     * mise à jour initiale
     */

    public function maj_initiale() {
        //mise à jour initiale
        parent::begin_trans();
        $this->execute("CREATE TABLE config (
    version            INT  DEFAULT (0),
    rep_films_local    TEXT DEFAULT (''),
    rep_films_lan      TEXT DEFAULT (''),
    rep_films_web      TEXT DEFAULT (''),
    affichage_gallerie INT (1) DEFAULT (1),
    mot_de_pass_admin  TEXT DEFAULT ('1234') 
);");

        $this->execute('INSERT INTO config (version) VALUES(0);');

        $this->execute("CREATE TABLE mot_cle_ignore (
    id  INTEGER PRIMARY KEY AUTOINCREMENT,
    mot TEXT    NOT NULL
                UNIQUE ON CONFLICT IGNORE
);");

        $ignores = array('1',
            '5',
            '2011',
            '2012',
            '2013',
            '2014',
            '2015',
            'ac3',
            'avi',
            'bdrip',
            'brrip',
            'com',
            'dvdrip',
            'fr',
            'french',
            'libertyland',
            'mkv',
            'truefrench',
            'ttf',
            'tv',
            'x264',
            'xvid',
            'xvid-avitech',
            'xvid-lcktm',
            'xvid-slay3r-www',
            'xvid--www',
            'zone-telechargement',
            'www');

        foreach ($ignores as $s) {
            $this->insert_mot_cle_ignore($s);
        }



        parent::commit();
        //fin de la mise à jour initiale
    }

}

/*
 * utilisé pour tests
 * 
$config_db = new config_db();
var_dump(  $config_db->get_config());
 */