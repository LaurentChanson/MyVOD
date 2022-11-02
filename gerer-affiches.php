<?php
//entête
require_once 'commun.php';


//construction formulaire
//http://bootsnipp.com/forms

//redirect admin
require_once './lib/check-admin.php';


define("ACTION_SUPPRIMER_AFFICHES", 'supprimer_affiches');
define("ACTION_FACTORISER_AFFICHES", 'factoriser_affiches');

$action = Helper_var::get_var('action', false);

//récupère la liste des affiches qui ne sont pas dans la bdd
$repertoire_scan = MyVOD::img_path();//    config::$repertoireFilms . "/MyCinemaData/img/";
$fichiers_images = array();

class info_img {

    public $nom;
    public $path;
    public $taille;
    public $pointe = 0;

}

$timestamp_debut = microtime(true);
recherche_fichiers_avec_fonction_de_rappel($repertoire_scan, 'affiche_vers_tableau', array('.jpg', '.jpeg', '.png'));

$timestamp_fin = microtime(true);
$difference_ms = $timestamp_fin - $timestamp_debut;

//var_dump('Exécution du script : ' . $difference_ms . ' secondes.');


function affiche_vers_tableau($nom, $path) {
    global $fichiers_images;
    //var_dump($nom."   ". $path);
    $info_img = new info_img();
    $info_img->nom = $nom;
    $info_img->path = realpath($path);
    //$info_img->taille = filesize_utf8($path);

    //array_push($fichiers_images, $info_img);
    $fichiers_images[$info_img->path] = $info_img;
}

//liste des fiches en bdd
$MyVOD_DB = new MyVOD_DB();
$liste_fiches = $MyVOD_DB->get_liste();

$info = new MyVOD_Details();

//récupération du chemin du dossier 'img' local
$dossier_local_images= MyVOD::img_path();

//transforme tout les chemins relatifs en absolu
foreach ($liste_fiches as $k => $info) {
    //récupère le chemin absolu
    //$absolu = realpath($dossier_local_images . '/' . $info->Affiche);
    //$absolu = realpath($dossier_local_images . DIRECTORY_SEPARATOR . $info->Affiche);
    $absolu = $dossier_local_images . DIRECTORY_SEPARATOR . $info->Affiche;
    //cas de l'affiche manquante
    if($absolu==false){
        //var_dump($dossier_local_images . '/' . $info->Affiche);
    }else{
        //pointe dans le tableau des fichiers
        if (array_key_exists($absolu, $fichiers_images)) {
            $fichiers_images[$absolu]->pointe++;
        }
    }
}

//compte la taille totale occupée par les affiches orphelines
$f = new info_img();
$nb_fichiers = 0;
$taille_totale = 0;
foreach ($fichiers_images as $f) {
    if ($f->pointe == 0) {
        $nb_fichiers++;
        //LC: 28/12/2018 : la taille est actualisée ici et pas dans la fonction récursive
        $f->taille=filesize_utf8($f->path);
        $taille_totale+=$f->taille;
    }
}

//récupération de la liste des doublons
//ils sont en général dans les fichiers liés (car en plusieurs CD par ex.)
$affiches_triees = array();

$fiches_doublons = $MyVOD_DB->get_liste_doublons_sur_movieLink();

foreach ($fiches_doublons as $d) {
    //info : 'filename','movieLink','affiche' dans $d
    //on part du principe que si le lien de fiche externe (MovieLink) est la même, c'est la même affiche
    if (isset($affiches_triees[$d->MovieLink]) == false) {
        $affiches_triees[$d->MovieLink] = array();
    }
    array_push($affiches_triees[$d->MovieLink], $d);
}

//var_dump($affiches_triees);



/*
 * $affiches_triees est de la forme
 *  array 
 *      x
 *          0
 *          1
 *          2
 *      y
 *          0
 *          1
 *      z
 *          0
 *          1
 */


// pour éviter de garder les noms comme "copie de ..." ou "... (2)"
//on récupère ceux qu'on veut conserver, par convention, j'ai pris celui dont le nom est plus court
//(gare à ne pas prendre des affiche dont le nom est vide) 


$affiches_a_conserver = array();

foreach ($affiches_triees as $k => $d) {
    
    
    
    
    //on parcourt les sous tableaux (qui sont triés par fiche films)
    foreach ($d as $nouvelle) {
        //si l'élément est vide
        if (isset($affiches_a_conserver[$k]) == false) {
            //1ère initialisation
            $affiches_a_conserver[$k] = $nouvelle->Affiche;
            
            //var_dump('1)'.$nouvelle->Affiche);
            
        } else {
            //l'élément n'est pas vide,
            //on compare l'élément courant et le stocker
            $stocke = $affiches_a_conserver[$k];
            //on prend le nom de fichier le plus court et non vide
            if (strlen($nouvelle->Affiche) < strlen($stocke) && strlen($nouvelle->Affiche)>4) {

                //var_dump('2) '.$affiches_a_conserver[$k] .' -> '. $nouvelle->Affiche);


                $affiches_a_conserver[$k] = $nouvelle->Affiche;
            } else {
                //les noms de fichier ont la même taille, on prent la plus petite alphabétiquement
                if (strlen($nouvelle->Affiche) == strlen($stocke) && (strnatcmp($nouvelle->Affiche, $stocke) > 0)) {

                    //var_dump('3) '.$affiches_a_conserver[$k] .' -> '. $nouvelle->Affiche);

                    $affiches_a_conserver[$k] = $nouvelle->Affiche;
                }
            }
        }
    }
}

//on parcourt une deuxième fois pour mettre à jour
$nb_factorisations = 0;
$taille_totale_factorise = 0;

foreach ($affiches_triees as $k => $d) {
    //on parcourt les sous tableaux (qui sont triés par fiche films)
    foreach ($d as $nouvelle) {
        if ($affiches_a_conserver[$k] != $nouvelle->Affiche) {
            //mise à jour possible



            if ($action == ACTION_FACTORISER_AFFICHES) {
                $old_affiche = $nouvelle->Affiche;
                $new_affiche = $affiches_a_conserver[$k];
                
               
                
                $MyVOD_DB->update_affiche($old_affiche, $new_affiche);
                
                //mycinema_db::update_affiche($old_affiche, $new_affiche);
                //var_dump($nouvelle->affiche  .'<='. $affiches_a_conserver[$k] );
            } else {
                $nb_factorisations++;

                //$path = realpath(MyCinemaAffiche::affiche_to_image_source($nouvelle->Affiche));
                $path = realpath(MyVOD::img_path().DIRECTORY_SEPARATOR. $nouvelle->Affiche);
                $taille_totale_factorise+=filesize_utf8($path);
            }
        }
    }
}


//
//  Traitements utilisateur
//


if ($action != false) {
    switch ($action) {
        case ACTION_SUPPRIMER_AFFICHES:
            //on les supprimes
            foreach ($fichiers_images as $f) {
                if ($f->pointe == 0) {
                    unlink($f->path);
                }
            }
            Helper_redirection::redirige('gerer-affiches.php');

            break;


        case ACTION_FACTORISER_AFFICHES:
            //on redirige car le traitement est fait au dessus

            Helper_redirection::redirige('gerer-affiches.php');

            break;
    }
}
//mycinema_db::fermer();

require_once 'template/gerer-affiches.phtml';
