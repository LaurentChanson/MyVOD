<?php

require 'commun.php';

require_once 'lib/m3u-gen.php';

require_once 'lib/gestion-cache.php';

require_once './lib/check-admin.php';

define("ACTION_LIER", 'lier');
define("ACTION_ENLEVER_LIAISON", 'enlever_liaison');

//récupération des films qui ont le même titre
$MyVOD_DB = new MyVOD_DB();

//fait les traitement si recu par get
$action = Helper_var::get_var('action', false);
if ($action != false) {
    switch ($action) {

        case ACTION_LIER:

            $liaison = new LiaisonFichier();
            $liaison->Filename1 = Helper_var::get_var('fic1', false);
            $liaison->Filename2 = Helper_var::get_var('fic2', false);
            
            $MyVOD_DB->liaison_ajouter($liaison);
            
            break;

        case ACTION_ENLEVER_LIAISON:

            $liaison = new LiaisonFichier();
            $liaison->Filename1 = Helper_var::get_var('fic1', false);
            $liaison->Filename2 = Helper_var::get_var('fic2', false);
            $MyVOD_DB->liaison_supprimer($liaison);
            break;
    }

    
    Helper_redirection::redirige('plusieurs-fichiers.php');
}


//scan du répertoire pour regénérer cache

$cache_db = new cache_db();
cache_scan_and_rebuild_all();


//var_dump($cache);

//affichage des résultats

$fiches_doublons = $MyVOD_DB->get_liste_doublons_sur_titre();




//classe les doublons par titre
$fiches_doublons_titres = array();

//var_dump($fiches_doublons);


//tri par rapport aux titre
foreach ($fiches_doublons as $fic) {

    if (isset($fiches_doublons_titres[strtolower($fic->Titre)]) == false) {
        $fiches_doublons_titres[strtolower($fic->Titre)] = array();
    }
    
    //mise a jour si besoin de la résolution et détail d'encodage
    $file_info = new FileInfos;
    //ouverture du cache si besoin
    if (isset($cache_db) == false) {
        $cache_db = new cache_db();
    }
    $cache_db->lire($fic->Filename, $file_info);
    //var_dump($file_info);
    gerer_media_info($file_info);
     
    //classe sous forme de titre
    //var_dump($fiches_doublons_titres[$fic->Titre]);
    array_push(  $fiches_doublons_titres[strtolower($fic->Titre)], $fic);
    
}


//var_dump($fiches_doublons_titres);

//a ce niveau le cache a été fait

$cache = $cache_db->get_all_dico();


//les laisons (fiches déjà liées)
$liaisons = array();
$MyVOD_DB->liaison_get_liste($liaisons);



require_once 'template/plusieurs-fichiers.phtml';
        