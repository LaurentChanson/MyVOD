<?php

require_once 'functions-helper.php';

require_once 'data/config_db.php';

class config {

    //répertoire principal de MyCinema (le .jar) accessible en interne 
    //sans le \ à la fin
    //exemple : C:\wamp\www\MyCinema
    //private static $repertoireFilmsLocal_def = 'C:\wamp\www\MyCinema';
    //alias ou rep relatif apache du répertoire MyCinema
    //sans le / à la fin
    //il est utilisé pour construire le bouton télécharger (http)
    //private static $repertoireFilmsWeb_def='/MyCinema';
    //chemin réseau utilisé dans le fichier m3u
    //private static $repertoireFilmsPartage_def='//192.168.1.36/MyCinema';
    //mot de passe admin (utilisé dans la page admin)
    private static $mot_de_pass_admin_def = '1234';

    /*
      EST CE Q'IL Y EN A ENCORE BESOIN?
     */

    public static function affichage_gallerie() {
        self::check();
        return $_SESSION['config_affichage_gallerie'];
    }

    public static function affichage_liste_tablette() {
        self::check();
        return $_SESSION['config_affichage_liste_tablette'];
    }
    
    public static function repertoireFilmsLocal() {
        self::check();
        return $_SESSION['config_repertoireFilmsLocal'];
    }

    public static function repertoireWebPartage() {
        self::check();
        return $_SESSION['config_repertoireFilmsPartage'];
    }

    public static function repertoireWebFilms() {
        self::check();
        return $_SESSION['config_repertoireFilmsWeb'];
    }
    
    public static function nb_visu_histo() {
        self::check();
        return $_SESSION['nb_visu_histo'];
    }
    public static function nb_visu_ajouts() {
        self::check();
        return $_SESSION['nb_visu_ajouts'];
    }
    public static function affichage_visionnes_apres_ajouts() {
        self::check();
        return $_SESSION['affichage_visionnes_apres_ajouts']<>0;
    }
    public static function mots_cles_suppl_google_search() {
        self::check();
        return $_SESSION['config_mots_cles_suppl_google_search'];
    }

    public static function tri_recherche_def(){
        self::check();
        return $_SESSION['config_tri_recherche_def'];
    }
    
    
    public static function set_parametres($repertoireFilmsLocal, $repertoireWebPartage, $repertoireWebFilms, 
            $affichage_gallerie,$controle_parental,$code_parental,$nb_visu_histo,$nb_visu_ajouts,$affichage_visionnes_apres_ajouts,
            $taille_fichiers_64_bits,$mots_cles_suppl_google_search,$affichage_liste_tablette, $tri_recherche_def) {
        /*var_dump($taille_fichiers_64_bits);
        exit();*/
        self::check();
        $_SESSION['config_repertoireFilmsLocal'] = $repertoireFilmsLocal;
        $_SESSION['config_repertoireFilmsWeb'] = $repertoireWebFilms;
        $_SESSION['config_repertoireFilmsPartage'] = $repertoireWebPartage;
        $_SESSION['config_affichage_gallerie'] = $affichage_gallerie;
        $_SESSION['config_affichage_liste_tablette'] = $affichage_liste_tablette;
        $_SESSION['config_controle_parental'] = $controle_parental;
        $_SESSION['config_code_parental'] = $code_parental;
        $_SESSION['nb_visu_histo'] = $nb_visu_histo;
        $_SESSION['nb_visu_ajouts'] = $nb_visu_ajouts;
        $_SESSION['nb_derniers_ajouts'] = $nb_visu_ajouts;
        $_SESSION['affichage_visionnes_apres_ajouts'] =$affichage_visionnes_apres_ajouts;
        $_SESSION['taille_fichiers_64_bits'] =$taille_fichiers_64_bits;
        $_SESSION['config_mots_cles_suppl_google_search']=$mots_cles_suppl_google_search;
        $_SESSION['config_tri_recherche_def']=$tri_recherche_def;
        
        //enregistrement dans la bdd
        $config_db = new config_db();
        $config_db->update_parametres($repertoireFilmsLocal, $repertoireWebPartage, $repertoireWebFilms, $affichage_gallerie,
                $controle_parental,$code_parental,$nb_visu_histo,$nb_visu_ajouts,$affichage_visionnes_apres_ajouts,$taille_fichiers_64_bits,
                $mots_cles_suppl_google_search,$affichage_liste_tablette,$tri_recherche_def);
    }

    public static function mot_de_pass_admin() {
        self::check();
        return $_SESSION['config_mot_de_pass_admin'];
    }

    public static function set_mot_de_pass_admin($new_mdp) {
        self::check();
        $_SESSION['config_mot_de_pass_admin'] = $new_mdp;
        //enregistrement dans la bdd
        require_once 'data/config_db.php';
        $config_db = new config_db();
        $config_db->update_mot_de_passe($new_mdp);
    }

    public static function controle_parental_actif(){
        self::check();
        return $_SESSION['config_controle_parental'] ;
    }
    
    public static function code_parental(){
        self::check();
        return $_SESSION['config_code_parental'] ;
    }
    
    public static function taille_fichiers_64_bits(){
        self::check();
        return $_SESSION['taille_fichiers_64_bits'] ;
    }
    
    private static function check() {
        if (session_id() == "") {
            session_start();
        }
        if (isset($_SESSION['config_repertoireFilmsLocal']) == false) {
            //on va chercher les infos dans la bdd
            $config_db = new config_db();
            $config = new config_data();
            $config = $config_db->get_config();

            //on stocke en session
            $_SESSION['config_repertoireFilmsLocal'] = $config->rep_films_local;
            $_SESSION['config_repertoireFilmsWeb'] = $config->rep_films_web;
            $_SESSION['config_repertoireFilmsPartage'] = $config->rep_films_lan;
            $_SESSION['config_affichage_gallerie'] = $config->affichage_gallerie;
            $_SESSION['config_affichage_liste_tablette'] = $config->affichage_liste_tablette;
            $_SESSION['config_mot_de_pass_admin'] = $config->mot_de_pass_admin;
            $_SESSION['config_controle_parental'] = $config->controle_parental;
            $_SESSION['config_code_parental'] = $config->code_parental;
            $_SESSION['nb_visu_histo'] = $config->nb_visu_histo;
            $_SESSION['nb_visu_ajouts'] = $config->nb_visu_ajouts;
            $_SESSION['affichage_visionnes_apres_ajouts'] = $config->affichage_visionnes_apres_ajouts;
            $_SESSION['taille_fichiers_64_bits']  = $config->taille_fichiers_64_bits;
            $_SESSION['config_mots_cles_suppl_google_search']=$config->mots_cles_suppl_google_search;
            $_SESSION['config_tri_recherche_def']=$config->tri_recherche_def;
            //var_dump($_SESSION);
        }
    }

}
