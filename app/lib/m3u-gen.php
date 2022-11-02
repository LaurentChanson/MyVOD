<?php

require_once 'config.php';
require_once 'functions-helper.php';

//http://fr.wikipedia.org/wiki/M3U
//https://github.com/ChoiZ/php-playlist-generator/blob/master/generate.php
//crée le fichier m3u de conne le chemin
//s'il y a plusieurs fichiers, mettre un séparateur '|' dans full_path

function m3u_get_content($liste_path_fichiers) {
    // traitement si c'est un dossier
    $url = '';
    if (is_dir($liste_path_fichiers)) {
        //on parcourt ce qu'il y a dedans
        $dossier = opendir($liste_path_fichiers);  // on ouvre le répertoire
        if (!$dossier) {
            return false; //oups , impossible d'ouvrir le répertoire
        }
        $taille_min = 400 * 1024 * 1024;
        while ($entree = readdir($dossier)) {
            if ($entree == "." || $entree == "..") { // on ne regarde pas . ( lien vers le dossier courant ) et .. ( lien vers le dossier parent )
                continue;
            }
            $cheminEntree = $liste_path_fichiers . '/' . $entree;
            if (filesize($cheminEntree) > $taille_min) {

                $f = path_local_to_distant($cheminEntree);

                $url = $url . (strlen($url) > 0 ? "\n" : '') . $f;
            }
        }
    } else {

        //cas d'un fichier wpl, on prend le contenu est on le met dans le m3u
        if (strtolower(substr($liste_path_fichiers, -4)) == '.wpl') {
            //le wpl est window media playlist
            //on récupère le contenu
            //et le transforme en m3u
            $tfichiers = wpl_get_liste_fichiers($liste_path_fichiers);

            $dossier = dirname($liste_path_fichiers);

            $url = '';
            foreach ($tfichiers as $f) {

                $f = path_local_to_distant($dossier . '/' . $f);

                $url = $url . (strlen($url) > 0 ? "\n" : '') . $f;
            }
        } else {
            //autre cas on met le fichier
            $url = path_local_to_distant($liste_path_fichiers);

            //TODO : prévoir le cas ou le fichier est lié
        }
    }

    return $url;
}
        
function m3u_get_url_from_path($nom_fichier_m3u, $path) {
    return 'm3u.php/' . urlencode($nom_fichier_m3u) . '.m3u?path=' . urlencode($path);
//$m3u_url = 'm3u.php/'.urlencode($detail->Titre).'.m3u?file='.urlencode($detail->ID);//.'&path='.urlencode($file_info->full_path);
}

function m3u_get_url_from_MyVOD_Details(MyVOD_Details $detail) {
//    if (Helper_system::nav_OS_is_windows()) {
//        $m3u_url = 'm3u.php/' . str_replace('?', '', $detail->Titre) . '.m3u?file=' . urlencode($detail->ID) . '&ext=m3u';
//    } else {
//        $m3u_url = 'm3u.php?file=' . urlencode($detail->ID) . '&ext=m3u';
        $m3u_url = 'm3u.php/' . str_replace('?', '', $detail->Titre) . '.m3u?file=' . urlencode($detail->ID) ;
//    }
    return $m3u_url;
}

//génère 1 fichier m3u à partir de la liste donnée en paramètres
function generer_fichier_m3u($nom_fichier_m3u, $liste_path_fichiers) {
    //protection de l'utf8
    $nom_fichier_m3u = utf8_decode($nom_fichier_m3u);

    //Si le paramètre n'est pas un tableau, on le transfome en tableau
    //pour boucler dessus
    if (is_array($liste_path_fichiers) == false) {
        $s = $liste_path_fichiers;
        $liste_path_fichiers = array();
        array_push($liste_path_fichiers, $s);
    }

    // génération du fichier
    $url = '';
    foreach ($liste_path_fichiers as $path) {
        $path = utf8_decode($path);

        $url = $url . (strlen($url) > 0 ? "\n" : '') . m3u_get_content($path);
    }



    //crée le répertoire temporaire s'il n'existe pas
    $d = getcwd() . '/../temp/m3u';
    $carac_interdits = array(">", "<", ":", "/", "\\", "*", "|", "?", '"', '<', '>', "'");
    $nom_fichier_m3u = str_replace($carac_interdits, "", $nom_fichier_m3u);

    $m3u_path = $d . '/' . str_ireplace(" ", "_", $nom_fichier_m3u) . '.m3u';

    //création du dossier temporaire plus contrôle si le fichier existe déjà
    if (!file_exists($d)) {
        mkdir($d, 777, true);
    } else {
        if (file_exists($m3u_path)) {
            unlink($m3u_path);
        }
    }


    //écriture dans le fichier
    file_put_contents($m3u_path, $url);
    $m3u_path = utf8_encode($m3u_path);

    //remplace le chemin local en url absolu web
    $url_base = (dirname(dirname($_SERVER['SERVER_PROTOCOL']) . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));

    //retourne le chemin du fichier accessible depuis un navigateur
    return str_ireplace(getcwd(), $url_base, $m3u_path);
}

function path_local_to_distant($fichier) {




    // ============  en test
    //$url = config::$aliasMyCinemaTablette . (str_replace(config::$repertoireMyCinema, '', $fichier));
    //$url = utf8_encode(str_ireplace('%2F', '/', $url));
    //$url = 'smb:' . $url;
    //return $url;
    // ============  fin test : a mettre en commentaires...
    //en local, on a directement le fichier
    if (Helper_system::nav_is_local()) {
        $url = $fichier;
        if (Helper_system::nav_OS_is_windows()) {
            $url = str_replace('/', '\\', $url);
        }
    } else {

        //en lan, on prend celui du lan

        $url = config::repertoireWebPartage() . (str_replace(config::repertoireFilmsLocal(), '', $fichier));

        //var_dump($url);
        $url = utf8_encode(str_ireplace('%2F', '/', $url));


        if (!Helper_system::nav_OS_is_windows()) {
            $url = 'smb:' . $url;
        }
    }

    return $url;
}

function wpl_get_liste_fichiers($fichier_wpl) {


    $xmlstr = file_get_contents($fichier_wpl);

    $movies = new SimpleXMLElement($xmlstr);

    $t = array();

    foreach ($movies->body->seq->media as $m) {
        array_push($t, '' . $m['src']);
    }

    return $t;
}
