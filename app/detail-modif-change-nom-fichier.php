<?php

require 'commun.php';

//redirect admin
require_once './lib/check-admin.php';
//cache
require_once 'lib/gestion-cache.php';

//gestion de la fiche à modifier
$filename_or_id = Helper_var::get_var('file', 0);
if ($filename_or_id === 0) {
    if (isset($_SESSION['derniere_fiche_consulte'])) {
        $filename_or_id = $_SESSION['derniere_fiche_consulte'];
    }
}
$_SESSION['derniere_fiche_consulte'] = $filename_or_id;


$MyVOD_DB = new MyVOD_DB();
//va chercher la fiche dans la bdd
$fichefilm = new MyVOD_Details();
$MyVOD_DB->fiche_get_details($filename_or_id, $fichefilm);



//traitement du formulaire post (sauvegarde)
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'changer') {
        $new_filename = $_POST['nouveau_nom_fichier'];
        $old_filename = $_POST['nom_fichier_actuel'];
        $update_dh_creation = $_POST['update_dh_creation'];
        
        //vérifie qu'on a bien mis l'extension
        
        $extension = pathinfo($old_filename, PATHINFO_EXTENSION);
        $new_extension = pathinfo($new_filename, PATHINFO_EXTENSION);
        if($new_extension==''){
            $new_filename.=".$extension";
        }
        

        //test si le fichier existe dans la bdd
        $test = new MyVOD_Details();
        $MyVOD_DB->fiche_get_details($new_filename, $test);
        if ($new_filename == $old_filename) {
            message::ajouter_alerte_ko('Les 2 noms sont les mêmes.');
        } elseif (strlen($test->Filename) > 0) {
            message::ajouter_alerte_ko('Le fichier existe déjà dans la base de données.');
        } else {
            //var_dump($_POST);
            //début transaction
            $MyVOD_DB->begin_trans();
            //renomme dans la bdd
            $MyVOD_DB->update_filename($old_filename, $new_filename,$update_dh_creation);
            //renomme le fichier s'il existe
            
            //fin transaction dans la bdd
            $MyVOD_DB->commit();

            message::ajouter_alerte_ok("Changement du nom de fichier effectué dans la fiche avec succès.");
            
            Helper_redirection::redirige("detail-modif.php");
            
            //var_dump($_POST);
            //exit();
        }
    }
}






require_once 'template/detail-modif-change-nom-fichier.phtml';
