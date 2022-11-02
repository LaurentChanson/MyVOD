<?php

//libs qui vont bien
require './commun.php';
require_once 'lib/data/cache_db.php';
$contenu = Helper_var::get_var('contenu', '');

$filename = "export_my_vod.csv";

if($contenu==''){
    $filename = "export_all_my_vod.csv";
}

//header : format de sortie
//header('Content-Type: text/plain; charset=iso-8859-1');
header('Content-Type: text/csv; charset=iso-8859-1');
header('Content-Disposition: attachment; filename=' . $filename); 


$liste = null;
//récup dans la bdd
switch ($contenu) {
    case 'search':
        //on va chercher les fiches de la dernière recherche
        $liste = Helper_var::session_var('derniere_recherche', null);
        break;

    default:
        //on va chercher toutes les fiches
        $MyVOD_DB = new MyVOD_DB();
        $liste = $MyVOD_DB->get_liste();
        break;
}
if ($liste == null) {
    $liste = array();
}
//idem pour le cache
$cache_db = new cache_db();
$paths = $cache_db->get_all_dico();




//var_dump($paths);

//var_dump($liste);
//
//export en csv
$export = utf8_decode(export_csv($liste, $paths));

echo($export);

//export en csv

function export_csv($liste, $paths) {

    $f = new MyVOD_Details(); //pour l'autocomplétion
    $csv = "Identifiant;Titre;Genre;Année;Durée;Note;Nom fichier;Taille\r\n";

    foreach ($liste as $f) {

        $taille = "";
        if (isset($paths[mb_strtolower($f->Filename, 'UTF-8')]->full_path)) {
            //$cache=new FileInfos(); //pour autocompletion
            $cache=$paths[mb_strtolower($f->Filename, 'UTF-8')];
            if($cache->size>1){
                $taille=taille_fichier_en_texte($cache->size);
            }
        }



        $csv.="$f->ID;$f->Titre;$f->GenreNom1 $f->GenreNom2;$f->AnneeSortie;" . $f->Duree() . ";" . $f->NoteMoyenneTexte() . ";$f->Filename;$taille;\r\n";
    }
    return $csv;
}
