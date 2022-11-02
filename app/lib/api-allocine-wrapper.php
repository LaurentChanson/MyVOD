<?php

// Charger le fichier.
require_once "allocine/api-allocine-helper.php";

require_once 'webrecherche.php';
//testGetFilm();

class AllocineAPIWrapper {

    static $allohelper;

    public static function GetIDFromURL($url) {

//exemple : http://www.allocine.fr/film/fichefilm_gen_cfilm=49543.html
//on veut 49543
        //$url = 'http://www.allocine.fr/film/fichefilm_gen_cfilm=49543.html';
        $res = 0;
        
        if (substr($url, 0, 22) == 'http://www.allocine.fr') {
//on prend la partie de droite
            $pos = strrpos($url, '_cfilm');
            $url = (substr($url, $pos + 7));
//on enlève le .html
            $pos = strrpos($url, '.h');
            return(substr($url, 0, $pos));
        }
        return false;
    }

    /**
     * 
     * @param type $motsCles mot cle à rechercher
     * @param type $error_retour string si erreur ou pas de résultat
     * @return l'objet films ou faux si rien trouvé
     */
    public static function Rechercher($motsCles, &$error_retour) {
//print_r($motsCles);
        self::instancier_allohelper_si_pas_instancie();
        $page = 1;
//print_r($motsCles);
        // Il est important d'utiliser le bloc try-catch pour gérer les erreurs.
        try {

            
            
            // Envoi de la requête avec les paramètres, et enregistrement des résultats dans $donnees.
            $donnees = self::$allohelper->search(utf8_decode($motsCles), $page);

            
            //print_r($donnees);
            
            
            // print_r($donnees['movie'][0]);
            // Affichage des informations sur la requête
            //echo "<pre>", print_r($allohelper->getRequestInfos(), 1),  "</pre>";
            // Pas de résultat ?
            //print_r($donnees);

            if (isset($donnees['movie']) && count($donnees['movie']) > 0) {

                //on retourne le résultat
                //return $donnees['movie'];

                $t = array();
                foreach ($donnees['movie'] as $film) {

                    //var_dump($film);

                    $r = new WebRechercheData($film);
                    //$r->FromArrayAllocineRechercher($film);
                    array_push($t, $r);
                }
                return $t;
            } else {
                // Afficher un message d'erreur.
                $error_retour = 'Pas de résultat pour "' . $motsCles . '"';
            }
        }

        // En cas d'erreur.
        catch (ErrorException $e) {
            // Affichage des informations sur la requête
            //echo "<pre>", print_r($allohelper->getRequestInfos(), 1), "</pre>";
            // Afficher un message d'erreur.
            $error_retour = "Erreur " . $e->getCode() . ": " . $e->getMessage();
        }

        return FALSE;
    }

    public static function GetFilm($code, &$error_retour) {

        self::instancier_allohelper_si_pas_instancie();


        //'small', 'medium', 'large', 1 pour 'small', 2 pour 'medium', 3 pour 'large'
        $profile =  'large';//medium';


        try {

            // Envoi de la requête
            $movie = self::$allohelper->movie($code, $profile);

            //var_dump($movie);
            $f = new WebGetFilmData($movie);
            
            //var_dump($f);
            //var_dump($movie);
            //exit();

            return $f;
        } catch (ErrorException $error) {
            // En cas d'erreur
            $error_retour = "Erreur n°" . $error->getCode() . ": " . $error->getMessage();
        }
        return false;
    }

    /**
     * instancie le membre static allohelper
     */
    private static function instancier_allohelper_si_pas_instancie() {

        if (!isset(self::$allohelper)) {
            // Créer un objet AlloHelper.
            self::$allohelper = new AlloHelper;
        }
    }

}



function testRecherche() {
    $motsCles = "The Dark Knight";
    $motsCles = "13";
    //$motsCles="115362";
    $error_retour = "";
    $donnees = AllocineAPIWrapper::Rechercher($motsCles, $error_retour);
    if ($donnees != false) {
        // Pour chaque résultat de film.
        foreach ($donnees as $film) {
            // Afficher le titre.
            echo "<h2>" . $film->title . "</h2>";

            //var_dump($film);
        }
    } else {
        echo $error_retour;
    }
}

function testGetFilm() {
    $error_retour = "";

    //Broken Arrow : 9736
    // spiderman : 192186
    //13 : 139163
    //127 heures : 174939
    $donnees = AllocineAPIWrapper::GetFilm(174939, $error_retour);




    var_dump($donnees);

    var_dump($donnees->duree_en_heure_minutes());
}
