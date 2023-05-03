<?php

$fichiers_doublons_ou_deplaces = array();
$fichiers_detectes = array();


function recherche_videos_sur_disque_local(){
    global $fichiers_disque;
    global $fichiers_detectes;  //ceux qui sont détectés
    
    $fichiers_disque = array();

    //réinit en tableau
    $fichiers_detectes = array();
    //recherche sur le disque
    recherche_fichiers_avec_fonction_de_rappel(config::repertoireFilmsLocal(), 'traitement_fichier_trouve');
    return $fichiers_detectes;
    
}





function init_listes_fichiers_pour_tri(){
    global $fichiers_ignores;
    global $liste_fiches;
    global $liaisons;
    global $noms_fichiers;
    
    $MyVOD_DB = new MyVOD_DB();
    
    //Helper_system::temps_ecoule('Après le case');
    //récupération de la black list
    $fichiers_ignores = $MyVOD_DB->blacklist_get_liste_fichiers();
    
    //Helper_system::temps_ecoule('Après récup black list');
    //liste des fiches en bdd
    $liste_fiches = $MyVOD_DB->get_liste();
    
    //change la cle pour aller + vite en utilisant les fonctions native de php
    $liste_fiches_tmp = array();
    foreach ($liste_fiches as $f) {
        $liste_fiches_tmp[$f->Filename] = $f;
    }
    $liste_fiches = $liste_fiches_tmp;

    //Helper_system::temps_ecoule('Après récup liste en bdd');
    //liste des liaisons en bdd
    $MyVOD_DB->liaison_get_liste($liaisons);
    
    //Helper_system::temps_ecoule('Après récup liaisons');
    //extrait des nom de fichiers à partie de la liste
    $noms_fichiers = array();
    refresh_noms_fichiers($noms_fichiers);


}




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
