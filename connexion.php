<?php

require './commun.php';

//il y a 2 modes : adim & contrôle parental


$mode_parental = Helper_var::post_or_get_var('mode_parental', '');

//titre de la page en fonction si on est mode contrôle parental
$titre_page = $mode_parental == true ? 'Contrôle parental' : 'Connexion';



//retour du formulaire POST (pour valider le mot de passe)
if (isset($_POST['valider_mdp'])) {

    $mdp = $_POST['mdp'];

    if ($mode_parental == true) {
        //si le code est bon
        if ($mdp === controle_parental::code_parental() || $mdp === config::mot_de_pass_admin()) {

            controle_parental::deverrouiller();
            //redirige vers la page index pour voir les résultats (quand on clique dans 
            //l'icone à droite du menu)
            
            ob_clean();
            
            if(isset($_SESSION['url_after_connexion_parental'])){
                $url=$_SESSION['url_after_connexion_parental'];
                unset($_SESSION['url_after_connexion_parental']);
                header('Location: '.$url);
                
            }else{
                header('Location: index.php');
            }
            
            
            exit;
        } else {
            //mot de passe ko
            message::ajouter_alerte_ko('Le code parental est incorrect.');
        }
    } else {

        //test si c'est le bon mot de passe (il est dans le fichier config.php)
        if ($mdp === config::mot_de_pass_admin()) {
            //mot de passe ok

            Gestion_admin::connecter();
            //redirige vers la page nécessitant les droits admin
            //Helper_redirection::redirige_derniere_page(); //marche pas
            
            if (isset($_SESSION['url_after_connexion'])) {
                ob_clean();
                header('Location: ' . $_SESSION['url_after_connexion']);
                exit;
            }
        } else {
            //mot de passe ko
            message::ajouter_alerte_ko('Le mot de passe est incorrect.');
        }
    }
}


require 'template/connexion.phtml';
