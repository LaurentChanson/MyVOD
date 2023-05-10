<?php

$titre_page = 'Détails du film';

require './commun.php';

//gestion du cache
require_once 'lib/gestion-cache.php';

//récupération du numéro de fiche via GET ou SESSION
$filename_or_id = 0;
//Helper_system::temps_ecoule('début');

if (isset($_GET['file']) && $_GET['file'] > 0) {
    $filename_or_id = urldecode($_GET['file']);
    
} else {
    if (isset($_SESSION['derniere_fiche_consulte'])) {
        $filename_or_id = $_SESSION['derniere_fiche_consulte'];
    }
}
$_SESSION['derniere_fiche_consulte'] = $filename_or_id;



//va chercher les donnees 

$MyVOD_DB = new MyVOD_DB();
$detail = new MyVOD_Details;
$MyVOD_DB->fiche_get_details($filename_or_id, $detail);

//Helper_system::temps_ecoule('après get détails');

if ($detail->ID != null) {

    //génération des éléments nécessaire pour la page

    $url_afiche = MyVOD::affiche_to_url($detail->Affiche);
    $url_afiche_miniature = MyVOD::affiche_to_url_miniature($detail->Affiche);
    
    //Helper_system::temps_ecoule('après affiche_to_url');
    
    $host_details = false;
    if (substr($detail->MovieLink, 0, 4) == 'http') {
        $host_details = parse_url($detail->MovieLink, PHP_URL_HOST);
    }
    //Le cache
    $nom_fichier = $detail->Filename;

    $file_info = new FileInfos;
    gerer_cache($nom_fichier, $file_info);
    //si on a pas l'info de media info on le génère

    //var_dump( filesize_utf8($file_info->full_path)  );
    
    gerer_media_info($file_info);
    
    
    
    //génération du m3u
    $s = $file_info->full_path;
    $lst_fichiers_a_lire = array();
    $lst_fichiers_pour_m3u = array();
    if (strlen($s) > 0) {
        array_push($lst_fichiers_pour_m3u, $s);
        array_push($lst_fichiers_a_lire, $file_info);
    }
    //liste des fichiers (en 1 ou plusieurs fichiers)
    foreach ($detail->t_liaisons as $f) {
        //var_dump($f);
        $file_info = new FileInfos;
        gerer_cache($f, $file_info);
        
        gerer_media_info($file_info);
        //met dans la liste si le fichier existe
        if (strlen($file_info->full_path)) {
            array_push($lst_fichiers_pour_m3u, $file_info->full_path);
            array_push($lst_fichiers_a_lire, $file_info);
        }
    }


    $i = 0;
    if (count($lst_fichiers_pour_m3u)) {
       

        require_once 'lib/m3u-gen.php';
        $m3u_url=m3u_get_url_from_MyVOD_Details($detail);
        //$m3u_url = 'm3u.php/'.$detail->Titre.'.m3u?file='.urlencode($detail->ID);//.'&path='.urlencode($file_info->full_path);

        //$m3u_url= str_replace('.php?', '.php/'.$detail->Titre.'.m3u?', $m3u_url);
        //$m3u_url = m3u_generer($detail->Titre, $lst_fichiers_pour_m3u);
        
        
    }

    $prec_suivant_ordre_ajout=  Helper_var::get_var('ordreajout', 0);
    //var_dump($prec_suivant_ordre_ajout);

    
    //va chercher les information pour le détail précédent et suivant
    if($prec_suivant_ordre_ajout==0){
        $idx_prec = $MyVOD_DB->get_idx_precedent($detail->ID);
        $idx_suiv = $MyVOD_DB->get_idx_suivant($detail->ID);
    }else{
        $idx_prec =$MyVOD_DB->get_idx_suivant_by_ajout($detail->ID);
        $idx_suiv = $MyVOD_DB->get_idx_precedent_by_ajout($detail->ID);
    }
    
    //var_dump($detail->ID,$idx_prec,$idx_suiv);
    
    if ($idx_prec != false) {
        $detail_prec = new MyVOD_Details();
        $MyVOD_DB->fiche_get_details($idx_prec, $detail_prec);
    }
    
    if ($idx_suiv != false) {
        $detail_suiv = new MyVOD_Details();
        $MyVOD_DB->fiche_get_details($idx_suiv, $detail_suiv);
    }
  
    require 'template/detail.phtml';
  
    
}else{ 
    //affiche qu'on ne trouve pas l'élément
    $message = "La fiche '$filename_or_id' n'existe pas ou est interdit par le contrôle parental.";
    if(controle_parental::filtrage_actif()==false){
        $message="La fiche '$filename_or_id' n'existe pas ou supprimée.";
    }
    
    $message.=' <b><a href="index.php">retour vers la liste de films</a></b>';
    
    message::ajouter_alerte_ko($message);
    
    message::afficher_et_stop();
}
