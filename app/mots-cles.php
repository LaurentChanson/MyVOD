<?php

require_once 'commun.php';

//seul l'admin peut accéder à cette page
require_once './lib/check-admin.php';

define("ACTION_AJOUTER", 'ajouter');
define("ACTION_ENLEVER", 'enlever');

$conf = new config_db();
$lst_mots_cle = $conf->get_liste_mots_cle_ignore();

$action = Helper_var::post_or_get_var('action', false);

if ($action != false) {

    switch ($action) {

        case ACTION_AJOUTER:
            $s=Helper_var::post_or_get_var('mot','');
            if(strlen($s)>0){
                if($conf->ajouter_mots_cle_ignore($s)){
                    message::ajouter_alerte_ok( "Le mot <b>'$s'</b> a bien été ajouté.");
                }else{
                    message::ajouter_alerte_ko("Impossible d'ajouter le mot <b>'$s'</b>.");
                }
                
            }
            
            break;

        case ACTION_ENLEVER:
            $id = Helper_var::post_or_get_var('id', 0);
            $s=$conf->get_mot_cle_from_id($id);
            if($conf->enlever_mots_cle_ignore_by_id($id)){
                message::ajouter_alerte_warning( "Le mot <b>'$s'</b> a bien été supprimé.");
            }else{
                message::ajouter_alerte_ko("Impossible de supprimer le mot <b>'$s'</b>.");
            }
       
            break;
    }

    Helper_redirection::redirige('mots-cles.php');
}

//affiche la page
require_once 'template/mots-cles.phtml';

