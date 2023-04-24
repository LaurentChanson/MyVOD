<?php

require_once 'lib/fileTools.php';
require_once 'lib/data/cache_db.php';

//efface le cache puis scan complet du répertoire
function cache_scan_and_rebuild_all() {

    //var_dump('cache!!!');
    //cache_db::ouvrir();
    //cache_db::purger();
    //met dans un tableau les données du cache
    recherche_fichiers_avec_fonction_de_rappel(config::repertoireFilmsLocal(), 'ajouter_cache_db');
}

function ajouter_cache_db($file_name, $full_path) {
    global $cache_db;
    //on récuère tout le cache en 1 fois
    static $donnees_cache_complet;
    if (isset($donnees_cache_complet) == false) {
        $donnees_cache_complet = $cache_db->get_all_dico();
    }
    $fichier_trouve = false;
    $fichier_a_mettre_a_jour = false;
    //comparaison du chemin avec la bdd
    $info_fichier_cache = new FileInfos;
    foreach ($donnees_cache_complet as $info_fichier_cache) {
        //var_dump($file_name);
        /*
          if(strcasecmp($file_name,'the.commuter.2018.french.hdrip.x264.mkv2')==0){
          var_dump($file_name);
          exit();
          } */
        if (strcasecmp($info_fichier_cache->file_name, $file_name) == 0) {
            $fichier_trouve = true;

            // test si le chemin est bon. S'il le fichier a été déplacé, on le met à jour ds le cache db
            //$info_fichier = new FileInfos;
            //cache_db::lire($file_name, $info_fichier);
            //var_dump($info_fichier);
            if (( path_equals($info_fichier_cache->full_path, $full_path) == false) || ($info_fichier_cache->size != filesize_utf8($full_path))) {
                $fichier_a_mettre_a_jour = true;
            }


            break;
            //var_dump($info_fichier_cache->file_name, $file_name);
        }
    }




    // un peu long la 1ere fois
    if ($fichier_a_mettre_a_jour == TRUE || $fichier_trouve == FALSE) {

        //var_dump($info_fichier_cache,$fichier_trouve,$fichier_a_mettre_a_jour);
        //on récupère la taille du fichier pour ajouter dans le cache
        $taille = filesize_utf8($full_path);
        $cache_db->ajouter($file_name, $full_path, $taille);
    }
}



function gerer_cache($nom_fichier, FileInfos &$file_info) {

    //var_dump($nom_fichier);
    
    global $cache_db;
    //test si le fichier existe et construit le lien
    //cache_db::ouvrir();
    if (isset($cache_db) == false) {
        $cache_db = new cache_db();
    }
    $cache_db->lire($nom_fichier, $file_info);
    //en test
    /*
      var_dump($file_info);
      //var_dump(($file_info == false || ($file_info != false && file_exists_utf8($file_info->full_path) == false)));
      var_dump(($file_info == false
      || ($file_info != false && file_exists_utf8($file_info->full_path)) == false)
      || ($file_info != false && $file_info->size != filesize_utf8($file_info->full_path) ));

     */

    //exit();
    //phase 1 : test de l'existance du fichier dans le cache
    //phase 2 : test de l'existance du fichier sur le disque (cas d'un déplacement)
    //phase 3 : test si la taille a changé (dans le cas ou on renomme un fichier sur le disque sans renommer dans la fiche)
    if ($file_info == false || ($file_info != false && file_exists_utf8($file_info->full_path) == false) || ($file_info != false && $file_info->size != filesize_utf8($file_info->full_path) )) {

        //var_dump(config::repertoireFilmsLocal(),$nom_fichier);

        //recherche du fichier physique
        $s0 = recherche_et_retourne_chemin_complet(config::repertoireFilmsLocal(), $nom_fichier);
//var_dump($s0);
        if (strlen($s0) > 0) { //un chemin a été retourné
            //var_dump('maj');
            //var_dump($s0);
            $taille = 0;
            //var_dump(is_dir($s0));
            if (!is_dir(( $s0))) {
                //var_dump($s0);
                $taille = filesize_utf8($s0);
                
                //var_dump($taille);
                //var_dump(filesize($s0));
                //$taille = filesize(utf8_decode($s0));
            }



            $cache_db->ajouter($nom_fichier, $s0, $taille);
        } else {

            //var_dump('suppression');
            //on n'efface pas, on met le chemin en chaine vide
            $cache_db->ajouter($nom_fichier, '', -1);
        }

        $file_info = new FileInfos;   //sans cette ligne => Catchable fatal error
        //relecture
        $cache_db->lire($nom_fichier, $file_info);
    }
}


function cache_update_media_info(&$file_info) {
    //FileInfos &$file_info
    global $cache_db;

    $cache_db->update_info_html($file_info->file_name, gzcompress($file_info->media_info_html, 9));
}



function gerer_media_info(FileInfos &$file_info) {

    require_once './lib/media-info.php';
    global $cache_db;
    
    //var_dump($file_info);
    
    if ($file_info->media_info_html == null || strlen($file_info->media_info_html) < 100) {
        //var_dump($file_info->media_info_html);
        //var_dump('UPDATE INFOS HTML');
        $file_info->media_info_html = media_info::get_media_info_HTML($file_info->full_path);
        cache_update_media_info($file_info);
        //var_dump($file_info);
    } else {
        //on le décompresse
        $file_info->media_info_html = gzuncompress($file_info->media_info_html);
    }


    //var_dump($file_info);
    //var_dump($file_info->file_exists());
    //récupération de la résolution à partir de média info.
    if ($file_info->file_exists()) {
        if ($file_info->width == null || $file_info->height == null || $file_info->duration == null) {
            //on update dans la bdd
            $video_info = new media_info();
            $video_info->media_info_fichier($file_info->full_path);
            if ($video_info->width !== null && $video_info->height !== null && $video_info->duration_sec !== null) {
                $cache_db->update_info_media($file_info->file_name, $video_info->width, $video_info->height, $video_info->duration_sec);
            }
            //on met à jour l'objet $file_info
            $file_info->width = $video_info->width;
            $file_info->height = $video_info->height;
            $file_info->duration = $video_info->duration_sec;
            //var_dump($video_info);
        }
    }
}
