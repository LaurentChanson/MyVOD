<?php

/*
 * cette page est à appeler par toutes les pages du site
 * 
 * 
 */
//important de le mettre avant le session_start car il n'arrive pas à caster
require_once 'lib/data/myvod_obj.php';

//les fichiers
require_once 'lib/fileTools.php';

//démarrage de la session
session_start();

//enlève les limite du var_dump
//https://stackoverflow.com/questions/34342777/how-to-see-full-content-of-long-strings-with-var-dump-in-php
ini_set("xdebug.var_display_max_children", '-1');
ini_set("xdebug.var_display_max_data", '-1');
ini_set("xdebug.var_display_max_depth", '-1');

//pour le chrono
$startTime = microtime(true);

//var_dump($startTime);


//la configuration
require_once 'lib/config.php';

//gestion admin (connecté ou non)
require_once 'lib/gestion-admin.php';

//le controle parental
require_once 'lib/controle-parental.php';

//les fonctions diverses liées à MyVOD
require_once 'lib/myvod.php';

//base de données
require_once 'lib/data/myvod_db.php';

//initialisation du filtrage parental pour la base de données
MyVOD_DB::set_filtrage_on(controle_parental::filtrage_actif());

//le helper maison
require_once 'lib/functions-helper.php';



//les message
require_once 'lib/message.php';




//besoin pour les dates
date_default_timezone_set("UTC");

//Pour les serveurs windows
if(Helper_system::serv_OS_is_windows()){
    setlocale(LC_ALL, 'fr_FR');
}else{
    setlocale(LC_ALL, 'fr_FR.UTF-8');
    putenv('LANG=fr_FR.UTF-8'); // Set this to the language you need
    //putenv est pour shell exec
}
//var_dump(setlocale(LC_ALL, 0));

