<?php

$page_courante = get_page_courante();


//il y a un message (voulez vous renvoyer ce formulaire...) quand on raffraichit et qu'il y a un POST
//solution : quand un post est détecté, on refresh la page
//sauf pour la page détail-modif (cela permet d'afficher les infos et pouvoir annuler)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($page_courante != 'detail-modif.php' ) {
        if($page_courante == 'index.php'){
            //affiche la page juste au dessus du menu (enleve le bandeau)
            
            Helper_redirection::redirige('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'].'#menu');
        }else{
            //on garde les variables get (en plus du POST) pour le passage de paramètre ponctuel
            $get='';
            //sauf pour la page '/recherche-web.php' qui aura pour effet de réinitialiser la recherche
            if($page_courante != 'recherche-web.php'){
                if(isset($_GET) && count($_GET)>0){
                    foreach ($_GET as $key => $value) {
                        $get.=strlen($get)>0?'&':'';
                        $get.="$key=$value";
                    }
                }

                if(strlen($get)>0){
                    $get='?'.$get;
                }
            }
            
            
            Helper_redirection::redirige('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'].$get);
        }
        
    }
    
//    SCRIPT_NAME
//    REQUEST_URI
}


//theme
require_once './theme/theme_config.php';

//gestion du titre de la page
if (isset($titre_page) == false) {

    $titre_page = "";
    switch ($page_courante) {
        case "index.php":
            $titre_page = 'Liste de films';
            break;
        case "admin.php":
            $titre_page = 'Administration';
            break;
        case "connexion.php":
            $titre_page = 'Connexion';
            break;
        case "a-propos.php";
            $titre_page = 'A Propos';
            break;
        case "recherche-web.php":
            $titre_page = 'Recherche Allociné ou DVDFR';
            break;
        case "maintenance-fichiers.php":
            $titre_page = 'Maintenance de mes fichiers';
            break;
        case "plusieurs-fichiers.php":
            $titre_page = 'Films en plusieurs parties';
            break;
        case "gerer-affiches.php":
            $titre_page = 'Gérer les affiches';
            break;
        case "parametrages.php":
            $titre_page = 'Paramétrages';
            break;
        case "mots-cles.php":
            $titre_page = 'Mots clé à exclure lors d\'une recherche';
            break;
        case "statistiques.php":
            $titre_page = 'Statistiques';
            break;
    }
}

//gestion de la page précédente
MyVOD::actualise_derniere_page();


//rendu du header

require_once 'header.phtml';


//
//  FIN
//


function get_page_courante() {
    return basename($_SERVER['SCRIPT_NAME']);
}
