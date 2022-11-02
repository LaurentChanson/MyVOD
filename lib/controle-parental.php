<?php

require_once  'config.php';
require_once 'functions-helper.php';

class controle_parental{
    
    //indique s'il y a une gestion de filtrage parental au niveau de la config.
    public static function config_actif(){
        return config::controle_parental_actif();
    }
    
    //indique si le filtrage est actif. Par défaut en mode admin, il n'y a pas de filtrage
    public static function filtrage_actif(){
        //en mode admin, pas de filtrage
        if(Gestion_admin::est_connecte()){
            return false;
        }
        return self::config_actif() && Helper_var::session_var('filtrage_actif', true);
    }
    
    public static function code_parental(){
        return config::code_parental();
    }

    public static function deverrouiller(){
        self::set_filtrage_actif(false);
    }
    public static function verrouiller(){
        self::set_filtrage_actif(true);
    }
    
    private static function set_filtrage_actif($new_value){
        $_SESSION['filtrage_actif']=$new_value;
    }
}
