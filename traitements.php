<?php

require_once 'commun.php';

define("ACTION_DECONNEXION_ADMIN_CONNECT_PARENT", 'deconnexion_et_passe_parent');
define("ACTION_SUPPRIMER_FICHE_BDD", 'delete_fiche');


$referer = $_SERVER['HTTP_REFERER'];

//récupère les données qui sont dans action
$action = Helper_var::get_var('action', false);
$param = Helper_var::get_var('param', false);


if ($action != false) {
    $MyVOD_DB = new MyVOD_DB();

    switch ($action) {
        /*
         * Deconnexion et passage en mode parental
         */
        case ACTION_DECONNEXION_ADMIN_CONNECT_PARENT:
            //si mode admin
            if (Gestion_admin::est_connecte()) {
                //déconnexte l'admin
                Gestion_admin::deconnecter();
                //dévérouille le mode parental
                controle_parental::deverrouiller();
                Helper_redirection::redirige('index.php');
            }else{
                Helper_redirection::redirige('connexion.php');
            }
            exit();
        break;
        
        
        /*
         * suppression de la fiche
         */
        case ACTION_SUPPRIMER_FICHE_BDD:
            if($param != false){
                $detail = new MyVOD_Details(); //pour autocomplétion

                $MyVOD_DB->fiche_get_details($param, $detail);

                message::ajouter_alerte_info('La fiche <strong>"'.$detail->Titre.'"</strong> <i>(Fichier : '.$detail->Filename.')</i> a bien été supprimée de la base de donnée MyVOD.');
                
                $MyVOD_DB->fiche_supprimer($param);
                
                //redirection si on est dans la page detail.php vers l'index
                //var_dump($referer);
                if(stripos($referer,'detail.php')!==false){
                    //var_dump("index");
                    Helper_redirection::redirige('index.php');
                    exit();
                }

                
                
                
                
                
                
                
                
                
            }
            break;
    }
}


//affiche la page appelante

Helper_redirection::redirige($referer);
