<?php



//test si l'utilisateur est connecté

if ( controle_parental::filtrage_actif()) {

    //récupère l'url en cours
    $_SESSION['url_after_connexion_parental'] = Helper_system::get_full_url();
    
    
    //pas connecté en tant que parent, on redirige vers la page de connexion
    ob_clean();
    header('Location: connexion.php?mode_parental=1');
    exit;
} else {


    //var_dump('ok');
}