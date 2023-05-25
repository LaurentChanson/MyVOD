<?php

//entête
require_once 'commun.php';
require_once './lib/check-admin.php';
require_once 'lib/m3u-gen.php';
require_once 'lib/recherche-fichiers.php';


//definition des constantes d'action
define("ACTION_AJOUTER_IGNORES", 'ajouter_bl');   
define("ACTION_RETIRER_IGNORES", 'retirer_bl');
define("ACTION_CREER_FICHE", 'creer_fiche');
define("ACTION_CREER_FICHE_ET_RECHERCHER", 'creer_fiche_et_rechercher');
define("ACTION_LIRE_FICHIER", 'lire_fichier');
define("ACTION_SUPPRIMER_FICHIER", 'supprimer_fichier');
define("ACTION_SUPPRIMER_FICHE_BDD", 'delete_fiche');
define("ACTION_FORCE_RECHERCHE", 'forcer_recherche');
define("ACTION_RELIER_FICHE", 'relier_fiche');
define("ACTION_RELIER_REPERTOIRE", 'relier_repertoire');

define("PARAM_VALIDATION_AUTO",'validation_auto');

//récupère les données qui sont dans action
$action = Helper_var::get_var('action', false);
$param = Helper_var::get_var('param', false);

//$validation_auto = Helper_var::get_var(PARAM_VALIDATION_AUTO, 0);
//LC : 02/05/2023
$validation_auto = Helper_var::get_or_session(PARAM_VALIDATION_AUTO, 1);
//LC : 02/05/2023
//$chk_j_ai_chance_checked = Helper_var::session_var(PARAM_VALIDATION_AUTO, 1);
//mise en forme pour le code HTML
//$chk_j_ai_chance_checked= $chk_j_ai_chance_checked !=0?'checked':'';
$chk_j_ai_chance_checked= $validation_auto!=0?'checked':'';     




$MyVOD_DB = new MyVOD_DB();


//actions détectés
if ($action != false && $param != false) {
    //Helper_var::set_session('force_recherche_disk', true);
    switch ($action) {


        case ACTION_AJOUTER_IGNORES:

            $MyVOD_DB->blacklist_ajouter($param);
            break;

        case ACTION_RETIRER_IGNORES:
            $MyVOD_DB->blacklist_enlever($param);
            break;

        case ACTION_CREER_FICHE:
            //test si la fiche existe
            $detail = new MyVOD_Details();
            $exists = $MyVOD_DB->fiche_get_details($param, $detail);
            var_dump($exists);
            //si la fiche n'est pas encore crée, on la crée
            if ($exists == false) {
                $MyVOD_DB->liste_ajouter($param);
            }

            Helper_redirection::redirige('detail-modif.php?file=' . urlencode($param));
            break;
            
        case ACTION_RELIER_FICHE:
            $choix_fiche_parent=  Helper_var::get_var('choix_fiche_a_relier', false);
            var_dump($param); //fiche enfant
            var_dump($choix_fiche_parent); //fiche parent
            //var_dump($_GET);
            //quelque checks ($param, $choix_fiche_a_relier)
            
            $detail = new MyVOD_Details();
            
            $detail = CheckFichiersARelier($param, $choix_fiche_parent);
            if($detail!==FALSE){
                //LC: factorisation 09/05/2023
//                //change la date de création pour faire apparaitre dans les derniers ajouts
//                $MyVOD_DB->update_date_creation_now($detail);
//                //patche la clé avant de sauver
//                $detail->ID=0;
//                $detail->Filename=$param;
//                //sauvegarde de la nouvelle fiche
//                $MyVOD_DB->fiche_enregistrer($detail);
//                //une fois crée, on relie les deux fiches     
//                $liaison = new LiaisonFichier();
//                $liaison->Filename1 = $choix_fiche_parent;
//                $liaison->Filename2 = $param;
//                $MyVOD_DB->liaison_ajouter($liaison);
                RelierFiche($param, $detail);
                
            }
            //LC: factorisation 09/05/2023
//            if(strlen($choix_fiche_parent)==0){
//                message::ajouter_alerte_ko("Veuillez choisir une fiche à lier pour '$param'");
//            }else{
//  
//                //Appel de la fiche à dupliquer
//                $detail = new MyVOD_Details();
//                $exists = $MyVOD_DB->fiche_get_details($choix_fiche_parent, $detail);
//                if($exists==false){
//                    message::ajouter_alerte_ko("Impossible de trouver la fiche '$choix_fiche_parent'");
//                }else{
//                    //patche la clé avant de sauver
//                    $detail->ID=0;
//                    $detail->Filename=$param;
//                    //sauvegarde de la nouvelle fiche
//                    $MyVOD_DB->fiche_enregistrer($detail);
//                    //une fois crée, on relie les deux fiches     
//                    $liaison = new LiaisonFichier();
//                    $liaison->Filename1 = $choix_fiche_parent;
//                    $liaison->Filename2 = $param;
//                    $MyVOD_DB->liaison_ajouter($liaison);
//                    message::ajouter_alerte_ok("Fichier '$param' ajouté à la fiche '$detail->Titre'");
//                } 
//            }

            
            //exit();
            break;
        case ACTION_RELIER_REPERTOIRE:
            $choix_fiche_parent=  Helper_var::get_var('choix_fiche_a_relier', false);
            var_dump($param); //fiche enfant
            var_dump($choix_fiche_parent); //fiche parent
            //var_dump($_GET);
            $detail = new MyVOD_Details();
            
            $detail = CheckFichiersARelier($param, $choix_fiche_parent);
            if($detail!==FALSE){
                //recherche du répertoire où il y a le fichier
                $s0 = recherche_et_retourne_chemin_complet(config::repertoireFilmsLocal(), $param);
                if (strlen($s0) == 0) { //un chemin a été retourné
                    message::ajouter_alerte_ko("Impossible de trouver le repertoire contenant le fichier '$param'");
                }else{
                    //on scanne ce répertoire pour récupérer les fichiers à lier
                    $dir=dirname($s0);
                    $files1 = scandir($dir);
                    $files2 = array();
                    $filtre_extensions = array('.wpl', '.avi', '.mpeg', '.mpg', '.mov', '.mp4', '.mkv', '.wmv', '.iso', '.nrg', '.m2ts', '.dvd', '.bluray');
                    foreach ($files1 as $f) {
                        //var_dump($dir.DIRECTORY_SEPARATOR.$f);
                        $path = $dir.DIRECTORY_SEPARATOR.$f;
                        if( $f!=='.' && $f!=='..' && file_exists_utf8($path)){
                            //filtre sur l'extension
                            $ext = get_extension(strtolower($path));
                            if (in_array($ext, $filtre_extensions)) {
                                //on a la liste des fichiers , on filtre ceux qui sont déjà en bdd
                                $d=new MyVOD_Details();
                                $exists = $MyVOD_DB->fiche_get_details($f, $d);
                                if($exists==false){
                                    //var_dump($exists,$f);
                                    //on vérifie aussi dans les fichers liés s'il n'est pas dedans 
                                    if($MyVOD_DB->liaison_exists($f) == false){
                                        array_push($files2, $f);
                                    }
                                }
                            }
                        }
                    }
                    
                    $cpt=0;
                    //on relie les fiches trouvées précédemment
                    foreach ($files2 as $f) {
                        RelierFiche($f, $detail);
                        $cpt++;
                    }
                   
                    if($cpt==0){
                       message::ajouter_alerte_ko("Pas de fichier à reclier!") ;
                    }                   
                }
            }
            
            //exit();
             
            break;
        case ACTION_CREER_FICHE_ET_RECHERCHER:
            //test si la fiche existe
            $detail = new MyVOD_Details();
            $exists = $MyVOD_DB->fiche_get_details($param, $detail);
            var_dump(urlencode($param));
/*
            var_dump($exists);
            var_dump($_GET);
            var_dump($_POST);
            var_dump($validation_auto);
            exit();
            */
            //si la fiche n'est pas encore crée, on la crée
            if ($exists == false) {
                $MyVOD_DB->liste_ajouter($param);
            }

            $_SESSION['derniere_fiche_consulte'] = $param;

            //gestion PARAM_VALIDATION à re transmettre
            $param=urlencode($param);
            /*if($validation_auto!=0){
                $param.='&'.PARAM_VALIDATION_AUTO.'=1';
            }*/  
            $url="recherche-web.php?recherche_web=".$param;
            //var_dump($validation_auto);
            //exit();
            Helper_var::set_session(PARAM_VALIDATION_AUTO, $validation_auto);
            Helper_redirection::redirige($url);
            break;
            

        case ACTION_SUPPRIMER_FICHIER:

            Helper_system::mettre_dans_corbeille($param);

            break;

        case ACTION_SUPPRIMER_FICHE_BDD:
            var_dump($param);
            exit();
            $MyVOD_DB->fiche_supprimer($param);

            break;

        case ACTION_FORCE_RECHERCHE:
            Helper_var::set_session('force_recherche_disk', true);
            //mets le tab1 par défaut
            Helper_var::set_session(onglets_get_key_script_name(), 'tab1');
         
            break;
    }

    //redirection forcée pour éviter que le navigateur sauve dans son historique les éléments GET.
    Helper_redirection::redirige('maintenance-fichiers.php');
}

//récup la variable en session
$force_recherche_disk = Helper_var::session_var('force_recherche_disk', false);
//acquittement si vrai
if ($force_recherche_disk == true) {
    Helper_var::set_session('force_recherche_disk', false);
    //active le 1er onglet
    
}

init_listes_fichiers_pour_tri();
////Helper_system::temps_ecoule('Après le case');
////récupération de la black list
//$fichiers_ignores = $MyVOD_DB->blacklist_get_liste_fichiers();

////Helper_system::temps_ecoule('Après récup black list');
////liste des fiches en bdd
//$liste_fiches = $MyVOD_DB->get_liste();

////Helper_system::temps_ecoule('Après récup liste en bdd');
////liste des liaisons en bdd
//$MyVOD_DB->liaison_get_liste($liaisons);

////Helper_system::temps_ecoule('Après récup liaisons');
////extrait des nom de fichiers à partie de la liste
//$noms_fichiers = array();
//refresh_noms_fichiers($noms_fichiers);

//Helper_system::temps_ecoule('Après traitement liaisons et liste fichiers');

//récup de la liste en session
$fichiers_disque = Helper_var::session_var('fichiers_disque', false);

//var_dump('$fichiers_disque ' . count($fichiers_disque));

////change la cle pour aller + vite en utilisant les fonctions native de php
//$liste_fiches_tmp = array();
//foreach ($liste_fiches as $f) {
//    $liste_fiches_tmp[$f->Filename] = $f;
//}
//$liste_fiches = $liste_fiches_tmp;




//recherche de fichiers sur le disque si pas en session (ou si on force)
if ($fichiers_disque === false || $force_recherche_disk == true) {
//   var_dump($liste_fiches);
//    $fichiers_disque = array();
//
//    //réinit en tableau
//    $fichiers_detectes = array();
//    //recherche sur le disque
//    recherche_fichiers_avec_fonction_de_rappel(config::repertoireFilmsLocal(), 'traitement_fichier_trouve');
    recherche_videos_sur_disque_local();
    //var_dump($t,$fichiers_detectes);
    message::ajouter_alerte_info('Recherche sur le disque. ' . count($fichiers_detectes).' fichier(s) trouvé(s) sur '. count($fichiers_disque) . ' fichiers scannés.');
    //var_dump($fichiers_detectes);   //pour débug
//    var_dump($liste_fiches);
} else {
    //LC: optimisation 11/05/2023
    //on restaure ceux de la session
    $fichiers_detectes = Helper_var::session_var('fichiers_detectes', $fichiers_detectes);
    $liste_fiches = Helper_var::session_var('liste_fiches', $liste_fiches);
    $fichiers_doublons_ou_deplaces = Helper_var::session_var('fichiers_doublons_ou_deplaces', $fichiers_detectes);
    //var_dump($liste_fiches);
    //var_dump($fichiers_detectes);
    //on regarde pour les nouveaux fichiers s'ils n'ont pas été enregistrés dans la bdd
    $f=new fichier_info();
    $fichiers_detectes_tmp = array();
    foreach ($fichiers_detectes as $f){
        //on regarde si entre temps, on n'a pas crée une fiche
        //et si c'est le cas, on enlève des fichiers détectés
        if (in_array($f->nom, $noms_fichiers)) {
            //on ne fait rien car on reconstruit le tableau temporaire
        }else{
            array_push($fichiers_detectes_tmp, $f);
        }
    }
    $fichiers_detectes=$fichiers_detectes_tmp;
    
    //on actualise la Liste des fiches orphelines si ca n'a pas été supprmé entre temps
    //var_dump($fichiers_detectes);
    $f=new MyVOD_Details(); $detail=new MyVOD_Details();
    $liste_fiches_tmp=array();
    //var_dump($liste_fiches);
    foreach ($liste_fiches as $key => $f) {
        //var_dump($key);
        if(  $MyVOD_DB->fiche_get_details($f->Filename, $detail, FALSE)){
            $liste_fiches_tmp[$key]=$detail;
            //var_dump($key);
        }
        
    }
    $liste_fiches=$liste_fiches_tmp;
    //var_dump($liste_fiches);
    /*
    $fichiers_disque_tmp = Helper_var::session_var('fichiers_disque', $fichiers_disque);

    var_dump($fichiers_disque_tmp);
    //on fait le traitement avec les fichiers issus de la session
    $fich_info = new fichier_info(); //autocomplétion NetBeans
    //la fonction traitement_fichier_trouve ajoute des éléments dans $fichiers_disque on le rince en amont
    $fichiers_disque = array();

    foreach ($fichiers_disque_tmp as $fich_info) {
        //var_dump($fich_info);
        //fait le traitement fictif et on remet en session
        traitement_fichier_trouve($fich_info->nom, $fich_info->path);
    }*/
}

//var_dump('$fichiers_disque ' . count($fichiers_disque));
//var_dump($liste_fiches);
//mise en session des résultats
Helper_var::set_session('fichiers_disque', $fichiers_disque);

Helper_var::set_session('fichiers_detectes', $fichiers_detectes);
Helper_var::set_session('liste_fiches', $liste_fiches);
Helper_var::set_session('fichiers_doublons_ou_deplaces', $fichiers_doublons_ou_deplaces);

//tri par ordre alphabétique du nom de fichier

//exemple de tri sur
//https://itx-technologies.com/fr/blog/2629-trier-un-tableau-dobjets-array-en-php
usort($fichiers_detectes, 'comparer_fichier_info');
function comparer_fichier_info($a, $b) {
  return strcmp($a->nom, $b->nom);
}

//Helper_var::set_session('liste_fiches', $liste_fiches);

//Helper_system::temps_ecoule('Après recherche fichiers détectés (disque ou sessions)');

//var_dump($fichiers_disque);
//var_dump($liste_fiches);


//génération des fichiers m3u pour les fichiers détectés

$lst_m3u_url = array();
foreach ($fichiers_detectes as $fic) {

    //génère le fichier m3u

    $_m3u_url = m3u_get_url_from_path($fic->nom, $fic->path);
    //var_dump($fic);
    //var_dump($_m3u_url);
    //$m3u_url='HTTP://localhost/Films/temp/m3u/Django_Unchained.m3u';
    $lst_m3u_url[$fic->nom] = $_m3u_url;
}

//Helper_system::temps_ecoule('Avant require phtml');


require './template/maintenance-fichiers.phtml';


/**
 * FIN
 */

function RelierFiche($nom_fiche_a_relier,   MyVOD_Details $detail){
    global $MyVOD_DB;   
    
    $detail_tmp = clone $detail;
    $choix_fiche_parent=$detail_tmp->Filename;
    //change la date de création pour faire apparaitre dans les derniers ajouts
    $MyVOD_DB->update_date_creation_now($detail_tmp);
    //patche la clé avant de sauver
    $detail_tmp->ID=0;
    $detail_tmp->Filename=$nom_fiche_a_relier;
    //sauvegarde de la nouvelle fiche
    $MyVOD_DB->fiche_enregistrer($detail_tmp);
    //une fois crée, on relie les deux fiches     
    $liaison = new LiaisonFichier();
    $liaison->Filename1 = $choix_fiche_parent;
    $liaison->Filename2 = $nom_fiche_a_relier;
    $MyVOD_DB->liaison_ajouter($liaison);
    
    message::ajouter_alerte_ok("Fichier '$nom_fiche_a_relier' ajouté à la fiche '$detail_tmp->Titre'.");
}


function CheckFichiersARelier($fiche_enfant, $choix_fiche_parent){
    global $MyVOD_DB;
    
    //var_dump($fiche_enfant);
    //var_dump($choix_fiche_parent);
    
    if(strlen($choix_fiche_parent)==0){
        message::ajouter_alerte_ko("Veuillez choisir une fiche à lier pour '$fiche_enfant'");
        return false;
    }else{
  
        //Appel de la fiche à dupliquer
        $detail = new MyVOD_Details();
        $exists = $MyVOD_DB->fiche_get_details($choix_fiche_parent, $detail);
        if($exists==false){
            message::ajouter_alerte_ko("Impossible de trouver la fiche '$choix_fiche_parent'");
            return false;
        }else{
            return $detail;
        }
    }
}