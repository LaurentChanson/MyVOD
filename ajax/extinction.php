<?php
header('Content-Type: text/html; charset=utf-8');

require_once '../commun.php';


//test du mot de passe
if( isset($_GET['mdp']) ){
    $mdp=$_GET['mdp'];
    if(config::mot_de_pass_admin()===$mdp){
        echo "Extinction en cours...<br>
		Patientez quelques minutes puis actualiser la page pour vérifier l'arrêt du serveur.";
        Helper_system::eteindre_machine();
        exit;
    }
    
}

//message par défaut
echo("mot de passe incorrect.");
