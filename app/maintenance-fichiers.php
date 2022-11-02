<?php

//entête
require_once 'commun.php';
require_once './lib/check-admin.php';
require_once 'lib/m3u-gen.php';



//definition des constantes d'action
define("ACTION_AJOUTER_IGNORES", 'ajouter_bl');   
define("ACTION_RETIRER_IGNORES", 'retirer_bl');
define("ACTION_CREER_FICHE", 'creer_fiche');
define("ACTION_CREER_FICHE_ET_RECHERCHER", 'creer_fiche_et_rechercher');
define("ACTION_LIRE_FICHIER", 'lire_fichier');
define("ACTION_SUPPRIMER_FICHIER", 'supprimer_fichier');
define("ACTION_SUPPRIMER_FICHE_BDD", 'delete_fiche');
define("ACTION_FORCE_RECHERCHE", 'forcer_recherche');

define("PARAM_VALIDATION_AUTO",'validation_auto');

//récupère les données qui sont dans action
$action = Helper_var::get_var('action', false);
$param = Helper_var::get_var('param', false);

$validation_auto = Helper_var::get_var(PARAM_VALIDATION_AUTO, 0);

$chk_j_ai_chance_checked = Helper_var::session_var(PARAM_VALIDATION_AUTO, 1);
//mise en forme pour le code HTML
$chk_j_ai_chance_checked= $chk_j_ai_chance_checked !=0?'checked':'';
      

$fichiers_doublons_ou_deplaces = array();
$fichiers_detectes = array();


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

        case ACTION_CREER_FICHE_ET_RECHERCHER:
            //test si la fiche existe
            $detail = new MyVOD_Details();
            $exists = $MyVOD_DB->fiche_get_details($param, $detail);
            var_dump(urlencode($param));

            var_dump($exists);
            //si la fiche n'est pas encore crée, on la crée
            if ($exists == false) {
                $MyVOD_DB->liste_ajouter($param);
            }

            $_SESSION['derniere_fiche_consulte'] = $param;

            //gestion PARAM_VALIDATION à re transmettre
            $param=urlencode($param);
            if($validation_auto!=0){
                $param.='&'.PARAM_VALIDATION_AUTO.'=1';
            }  
            $url="recherche-web.php?recherche_web=".$param;
            //var_dump($url);
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


//Helper_system::temps_ecoule('Après le case');
//récupération de la black list
$fichiers_ignores = $MyVOD_DB->blacklist_get_liste_fichiers();

//Helper_system::temps_ecoule('Après récup black list');
//liste des fiches en bdd
$liste_fiches = $MyVOD_DB->get_liste();

//Helper_system::temps_ecoule('Après récup liste en bdd');
//liste des liaisons en bdd
$MyVOD_DB->liaison_get_liste($liaisons);

//Helper_system::temps_ecoule('Après récup liaisons');
//extrait des nom de fichiers à partie de la liste
$noms_fichiers = array();
refresh_noms_fichiers($noms_fichiers);

//Helper_system::temps_ecoule('Après traitement liaisons et liste fichiers');

//récup de la liste en session
$fichiers_disque = Helper_var::session_var('fichiers_disque', false);

//var_dump('$fichiers_disque ' . count($fichiers_disque));

//change la cle pour aller + vite en utilisant les fonctions native de php
$liste_fiches_tmp = array();
foreach ($liste_fiches as $f) {
    $liste_fiches_tmp[$f->Filename] = $f;
}
$liste_fiches = $liste_fiches_tmp;





//recherche de fichiers sur le disque si pas en session (ou si on force)
if ($fichiers_disque === false || $force_recherche_disk == true) {
    $fichiers_disque = array();

    //réinit en tableau
    $fichiers_detectes = array();
    //recherche sur le disque
    recherche_fichiers_avec_fonction_de_rappel(config::repertoireFilmsLocal(), 'traitement_fichier_trouve');

    message::ajouter_alerte_info('Recherche sur le disque. ' . count($fichiers_detectes).' fichier(s) trouvé(s) sur '. count($fichiers_disque) . ' fichiers scannés.');
    //var_dump($fichiers_detectes);   //pour débug
} else {
    //on restaure ceux de la session
    //$fichiers_detectes = Helper_var::session_var('fichiers_detectes', $fichiers_detectes);
    $fichiers_disque_tmp = Helper_var::session_var('fichiers_disque', $fichiers_disque);

    //var_dump($fichiers_disque_tmp);
    //on fait le traitement avec les fichiers issus de la session
    $fich_info = new fichier_info(); //autocomplétion NetBeans
    //la fonction traitement_fichier_trouve ajoute des éléments dans $fichiers_disque on le rince en amont
    $fichiers_disque = array();

    foreach ($fichiers_disque_tmp as $fich_info) {
        //var_dump($fich_info);
        //fait le traitement fictif et on remet en session
        traitement_fichier_trouve($fich_info->nom, $fich_info->path);
    }
}

//var_dump('$fichiers_disque ' . count($fichiers_disque));
//var_dump($fichiers_disque);
//mise en session des résultats
Helper_var::set_session('fichiers_disque', $fichiers_disque);

//tri par ordre alphabétique du nom de fichier

//exemple de tri sur
//https://itx-technologies.com/fr/blog/2629-trier-un-tableau-dobjets-array-en-php
usort($fichiers_detectes, 'comparer_fichier_info');
function comparer_fichier_info($a, $b) {
  return strcmp($a->nom, $b->nom);
}
//var_dump($fichiers_detectes);

//Helper_var::set_session('liste_fiches', $liste_fiches);

//Helper_system::temps_ecoule('Après recherche fichiers détectés (disque ou sessions)');

//var_dump($fichiers_disque);
//var_dump($liste_fiches);
function traitement_fichier_trouve($fichier, $full_path) {
    //affiche une ligne du tableau
    global $noms_fichiers;
    //global $count;
    global $fichiers_ignores;         //fichiers black listes
    global $fichiers_doublons_ou_deplaces;  //ceux qui sont en doublons
    global $fichiers_detectes;  //ceux qui sont détectés
    global $liste_fiches;       //liste des fiches en bdd à dépiler pour détecter les orpholins
    global $fichiers_disque;    //liste des fichiers trouvés sur le disque
    
    
    //on sort si le fichier est dans la blacklist
    $fichier = mb_convert_case(($fichier), MB_CASE_LOWER, "UTF-8");
    

    //renomme le fichier s'il contient des "+". Car ils posent problèmes avec les "urlencode"
    if (strpos($fichier, "+") !== false && file_exists_utf8($full_path)) {
        $new_name = str_replace("+", "_", $fichier);
        $new_path = dirname($full_path) . DIRECTORY_SEPARATOR . $new_name;
        rename($full_path, $new_path);
        //changement des variables pour la suite
        $fichier = $new_name;
        $full_path = $new_path;
        //var_dump($fichier,$new_path);
    }

    $existe_dans_bdd = false;

    if (in_array($fichier, $noms_fichiers)) {
        //var_dump($fichier);
        $existe_dans_bdd = true;
        unset($liste_fiches[$fichier]);
    /*
     * 
     * 
     * // A SUIVRE POUR DIAG
    }else{
        var_dump($fichier);
        var_dump($noms_fichiers[713]);
       // print_r($noms_fichiers);
     */
    }

    

    //vérifie les doublons
    $fic = new fichier_info();
    $fic->nom = $fichier;
    $fic->path = $full_path;
    //$fic->taille = filesize(utf8_decode($full_path));
    $fic->taille = filesize_utf8($full_path);

    array_push($fichiers_disque, $fic);

    if ( in_array($fichier, $fichiers_ignores) == true) {
        return;
    }
    /*
     // LC : Les doublons ne sont pas détecté si dans bdd
    if ($existe_dans_bdd == true || in_array($fichier, $blacklists) == true) {
        return;
    }
*/
    //var_dump($fic);

    //ajoute dans le tableau des doublons
    if (isset($fichiers_doublons_ou_deplaces[$fic->nom]) == false) {
        $fichiers_doublons_ou_deplaces[$fic->nom] = array();
    }
    array_push($fichiers_doublons_ou_deplaces[$fic->nom], $fic);

    //on sort aussi si le fichier est ds la db car on est sencé afficher les nouveaux 
    //qui ne sont pas enregistrés
    if (in_array($fichier, $noms_fichiers) != FALSE) {
        return;
    }

    //ajout dans les nouveaux fichiers détectés
    array_push($fichiers_detectes, $fic);
}

function refresh_noms_fichiers(&$noms_fichiers) {
    global $liste_fiches;
    global $liaisons;


    foreach ($liste_fiches as $fiche) {
        //met en minuscule pour rendre case insensitive
        $fiche->Filename = mb_convert_case(( $fiche->Filename), MB_CASE_LOWER, "UTF-8");
        array_push($noms_fichiers, $fiche->Filename);
    }

    //ajout de la liste des liaisons dans la liste à regarder
    foreach ($liaisons as $fiche) {
        $fiche->Filename2 = mb_convert_case(( $fiche->Filename2), MB_CASE_LOWER, "UTF-8");
        array_push($noms_fichiers, $fiche->Filename2);
    }
}

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

