<?php



require_once 'lib/functions-helper.php';
require_once 'lib/data/myvod_db.php';



class derniers_lus{

    

    public static function get_derniers_lus($nb_max){
        
        $ip=  Helper_system::nav_ip();
        $myvod_db = new MyVOD_DB();
        //var_dump("greergergergerg");
        //exit();
        return $myvod_db->dernier_lu_get_liste($ip,  $nb_max);
        
        
    }
    
    
    
    public static  function ajouter($filename){
        
        $ip=  Helper_system::nav_ip();
        $myvod_db = new MyVOD_DB();
        $myvod_db->dernier_lu_ajouter($filename,$ip);
        
       
    }
    
    
    
    
    
    
}