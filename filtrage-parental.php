<?php

require './commun.php';

//check si on est bien un parent et redirigé vers l'invite de connexion si ce n'est pas le cas
require_once 'lib/check-parent.php';



define('ACTION_AUTORISER','autoriser');
define('ACTION_INTERDIRE','interdire');
define('ACTION_AUTORISER_GENRES','genres_autoriser');
define('ACTION_INTERDIRE_GENRES','genres_interdire');
define('ACTION_AJOUTER_GENRES_LISTE_BLANCHE','genres_ajouter_liste_blanche');
define('ACTION_ENLEVER_GENRES_LISTE_BLANCHE','genres_enlever_liste_blanche');


$myvod_db=new MyVOD_DB();





//si on a eu un post, on a debug
if(count($_POST)>0){
    
    $action=  Helper_var::post_var('action', '');
    $liste_noire=  Helper_var::post_var('liste_type_public_interdit',  array());
    $liste_blanche=  Helper_var::post_var('liste_type_public_autorise',  array());
    
    
       
    $liste_genres_non_autorise=  Helper_var::post_var('liste_genres_non_autorise',  array());
    $liste_genres_autorise=  Helper_var::post_var('liste_genres_autorise',  array()); 
    
    
    $liste_noire_genres=  Helper_var::post_var('liste_noire_genres',  array());
    $liste_blanche_genres=  Helper_var::post_var('liste_blanche_genres',  array()); 
    
    switch ($action) {
        case ACTION_AUTORISER:
            //on prend de la liste noire vers la blanche
            
            foreach ($liste_noire as $value) {
                $myvod_db->type_public_ajouter_autorisation($value);
            }

            break;
        case ACTION_INTERDIRE:
            //on retire de la liste blanche pour mettre dans la noire
            foreach ($liste_blanche as $value) {
                $myvod_db->type_public_enlever_autorisation($value);
            }
            break;
        case ACTION_AUTORISER_GENRES:
            //on prend de la liste noire vers la blanche
            foreach ($liste_genres_non_autorise as $id) {
                $myvod_db->genres_autoriser_by_id($id);  
            }
            break;
        case ACTION_INTERDIRE_GENRES:
            foreach ($liste_genres_autorise as $id) {
                $myvod_db->genres_interdire_by_id($id) ;  
            } 
            break;
        case ACTION_AJOUTER_GENRES_LISTE_BLANCHE:
            foreach ($liste_noire_genres as $id) {
                $myvod_db->genres_set_liste_blanche_by_id($id) ;  
            } 
            break;
        case ACTION_ENLEVER_GENRES_LISTE_BLANCHE:
            foreach ($liste_blanche_genres as $id) {
                $myvod_db->genres_set_liste_non_blanche_by_id($id) ;  
            } 
            break;     
        default:
            
            var_dump($_POST);
            exit();
            
            break;
    }
    
    
    /*
    var_dump($liste_noire);
    
    var_dump($liste_blanche);
    exit();
    
    */
}

$liste_autorise=$myvod_db->type_public_get_liste_autorise();
$liste_interdit=$myvod_db->type_public_get_liste_non_autorise();


$genres_autorises=$myvod_db->genres_get_liste_autorise();
$genres_interdits=$myvod_db->genres_get_liste_non_autorise();

$genres_liste_blanche=$myvod_db->genres_get_liste_blanche();
$genres_liste_non_blanche=$myvod_db->genres_get_liste_non_blanche();



require 'template/filtrage-parental.phtml';