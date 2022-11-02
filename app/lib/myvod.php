<?php

require_once 'fileTools.php';
require_once 'recup-affiche.php';

require_once 'simple-image.php';

require_once 'functions-helper.php';

class MyVOD {

    public static function actualise_derniere_page() {


//gestion de la page précédente
        $tabUrl = parse_url(Helper_redirection::get_referer());
        $fichier = basename($tabUrl["path"]);



        switch ($fichier) {
            case "connexion.php":
            case "recherche-web.php":
            case "detail-modif.php":
            case "detail-modif-change-nom-fichier.php":
                //on ne fait rien
                break;
            default:
                //var_dump($fichier);
                Helper_redirection::set_derniere_page();
        }
    }

    /*
     * permet de savoir si on est en mode galerie ou liste en fct de la plateforme
     */

    public static function affichage_galerie() {
        require_once 'config.php';
        require_once 'functions-helper.php';
        //si on est en mode tablette, on fait le test
        if (Helper_system::nav_OS_is_mobile()) {
            return config::affichage_liste_tablette() ? 0 : 1;
        }

        //par défaut renvoi du paramètre principal
        return config::affichage_gallerie();
    }

    /**
     * retourne le chemin (OS) du répertoire 'MyVOD'
     * @return string
     */
    public static function myvod_path() {
        return dirname(dirname(__DIR__));
    }

    /**
     * retourne le chemin d'originedu programme web
     * @return string
     */
    public static function myvod_url() {
        return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['DOCUMENT_ROOT'];
    }

    /**
     * retourne le chemin (OS) du répertoire 'app' du site
     * @return string  
     */
    public static function app_path() {
        return dirname(__DIR__);
    }

    /**
     * retourne le chemin (OS) du répertoire 'data' du site
     * @return string
     */
    public static function data_path() {
        return realpath(dirname(self::app_path()) . '/data');
    }

    /**
     * retourne le chemin (OS) du répertoire 'data/img' du site
     * @return string
     */
    public static function img_path() {
        return realpath(dirname(self::app_path()) . '/data/img');
    }

    /**
     * Création du dossier 'img' s'il n'existe pas.
     * rq : Le dossier 'img' se situe dans le répertoire 'data'
     */
    public static function check_dossier_img() {
        create_dir_if_not_exists(self::img_path());
    }

    public static function affiche_recup_to_local($url, $nouveau_nom = null) {

        $fichier = Recup_Affiche::recuperer_fichier($url, self::img_path(), $nouveau_nom);
        //var_dump($fichier) ;
        return $fichier;
        /*
          if ($fichier == false)
          return $fichier;
          return self::img_path().'/'. $fichier;
         */
    }

    public static function affiche_to_url($affiche) {
        if (strlen($affiche) == 0) {
            //require_once '/../theme/theme_config.php'; //pb php7
            require_once __DIR__ . '/../theme/theme_config.php';
            return theme_config::$repertoire_img . '/sans-affiche.png';
        }
        return self::path2url(self::img_path() . '/' . $affiche);
    }

    /**
     * Converti l'affiche en url
     * @param type $affiche : Nom de l'affiche qui se situe dans le répertoire 'data/img'
     * @return string : url relative pour afficher dans un navigateur (img src)
     */
    public static function affiche_to_url_miniature($affiche) {

        if (strlen($affiche) == 0) {
            //require_once '/../theme/theme_config.php'; //pb php7
            require_once __DIR__ . '/../theme/theme_config.php';
            return theme_config::$repertoire_img . '/sans-affiche.png';
        }

        self::create_miniature($affiche);

        return self::path2url(self::img_path() . '/miniatures/' . $affiche);
    }

    static function create_miniature($affiche) {
        //test si le répertoire 'miniature' existe
        $dossier_miniatures = self::img_path() . '/miniatures';
        if (!is_dir($dossier_miniatures)) {
            mkdir($dossier_miniatures);
        }


        $path_img_source = self::img_path() . '/' . $affiche;
        $path_img_miniature = self::img_path() . '/miniatures/' . $affiche;

        //retaille l'image
        if (file_exists($path_img_source) && !file_exists($path_img_miniature)) {
            $w = 410; //342;
            $h = 534; //445;
            $q = 85;
            //on traite si le fichier fait plus de 50ko
            if (filesize($path_img_source) > (60 * 1024)) {

                $img_miniature = new SimpleImage();
                $img_miniature->load($path_img_source);

                if ($img_miniature->getWidth() > $w + 10) {

                    $img_miniature->resize($w, $h);

                    $img_miniature->save($path_img_miniature, IMAGETYPE_JPEG, $q);
                } else {
                    //on copie
                    copy($path_img_source, $path_img_miniature);
                }
            } else {
                //on copie
                copy($path_img_source, $path_img_miniature);
            }
        }
    }

    /**
     * transforme de chemin local en web
     */
    public static function path2url($file_path) {
        $s = str_replace(realpath($_SERVER['DOCUMENT_ROOT']), 'http://' . $_SERVER['HTTP_HOST'], $file_path);
        $s = str_replace('\\', '/', $s);


        return $s;
    }

    /*
     * construit le chemin du fichier pour télécharger le fichier
     */

    public static function get_url_download($file_path) {
        //si on est en local, on envoie directe
        /*
         * //marche pas
          if (Helper_system::nav_is_local()) {
          var_dump(DIRECTORY_SEPARATOR);
          $url_save=str_ireplace('/',DIRECTORY_SEPARATOR,$file_path);
          $url_save=str_ireplace('\\',DIRECTORY_SEPARATOR,$url_save);
          return 'file:///'.$url_save;
          } */

        //on n'est pas en local

        $url_save = str_ireplace(config::repertoireFilmsLocal(), config::repertoireWebFilms(), $file_path);

        //                            var_dump(config::repertoireFilmsLocal());
        //                    var_dump(config::repertoireWebFilms());
        //                   var_dump($file_path);

        return $url_save;
    }

}
