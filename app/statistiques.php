<?php

require './commun.php';
require_once 'lib/chart/chart.php';


$MyVOD_DB = new MyVOD_DB();
$nb_films = $MyVOD_DB->nb_films();

//pour les statistiques
// par genre

$genres = $MyVOD_DB->get_genres();
$series_genres=constructChartSeries($genres,'Genres','index.php?action=recherche_par_genre&genre=');
//var_dump($series_genres);


// par type de publique

$types_public = $MyVOD_DB->get_publictypes('Count');
$series_type_public=constructChartSeries($types_public,'Type de publique','index.php?action=recherche_par_public&public=');


//par années de production

$annees = $MyVOD_DB->get_annees_production('Nom');
$series_annees=constructChartSeries($annees,'Année de production','index.php?action=recherche_par_annee&annee=');


$t_decenies = array();

foreach ($annees as $a) {
    $decenie = new stdClass();
    $decenie->Nom='date inconnue';
    if($a->Nom!='--'){
        $decenie->Nom = ($a->Nom - $a->Nom%10) . "'s";
    }
    $decenie->Count=$a->Count;
    if(array_key_exists(''.$decenie->Nom,  $t_decenies)){
        $t_decenies[$decenie->Nom]=$t_decenies[$decenie->Nom]+$decenie->Count;
    }else{
        $t_decenies[$decenie->Nom]=$decenie->Count;
    }
}

$series_decenies=array();
foreach ($t_decenies as $key => $value) {
   $decenie = new stdClass();
   $decenie->Nom=$key;
   $decenie->Count=$value;
   array_push($series_decenies, $decenie); 
}
$series_decenies=constructChartSeries($series_decenies,'Décade de production','index.php?action=recherche_par_decade&decade=');







require 'template/statistiques.phtml';

//
//
//   FIN
//
//

function constructChartSeries($array, $titre,$prefix_url=null) {

    $series = new ChartSeries();
    $series->name = $titre;


    $chartserie = array();
    $total = 0;
    foreach ($array as $g) {
        $total+=$g->Count;
    }
    foreach ($array as $g) {
        $d = new ChartSeriesData();
        
        if($prefix_url!=null){
            $d->url=$prefix_url. $g->Nom;
        }
        
        if (strlen($g->Nom) > 40) {
            $g->Nom = substr($g->Nom, 0, 40) . '...';
        }
        $d->name = $g->Nom;
        $d->y = $g->Count * 1;//100 / $total;

        array_push($chartserie, $d);
    }
    $series->data = $chartserie;
    return $series;
}
