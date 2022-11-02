<?php

require_once 'gestion-admin.php';

//les session doivent être ouvertes
//récupère l'url en cours

$_SESSION['url_after_connexion'] = Helper_system::get_full_url();



//test si l'utilisateur est connecté

if (Gestion_admin::est_connecte() == FALSE) {

    //pas connecté, on redirige vers la page de connexion
    ob_clean();
    header('Location: connexion.php');
    exit;
} else {


    //var_dump('ok');
}



