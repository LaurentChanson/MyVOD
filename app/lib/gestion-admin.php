<?php

require_once 'controle-parental.php';

/**
 * Description of gestion-admin
 *
 * @author Laurent
 */

//si on recoit l'ordre de déconnecter, on se déconnecte
if(isset($_GET['deconnect'])){
    Gestion_admin::deconnecter();
}


class Gestion_admin {

    //couleur des labels et boutons réservés aux admin
    //les choix possibles sont :
    //default, primary, success, info, warning, danger
    public static $couleur_admin_bootstrap='danger';
    
    public static function est_connecte() {
        return isset($_SESSION['user_id']);
    }
    
    public static function connecter($user_name='administrateur') {
        $_SESSION['user_id']=$user_name;
       
    }
    
    public static function deconnecter() {
        unset($_SESSION['user_id']);
        controle_parental::verrouiller();
    } 

    
}


