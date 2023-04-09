<?php

function debug_duree($msg){
    global $startTime;
    $endTime = microtime(true);
    $elapsed = $endTime - $startTime;
    $s = number_format($elapsed * 1000, 0, '.', ' ');
    
    var_dump($msg.' : '.$s.' ms');
}


//fichier qui fait tous les requires nécessaires
require './commun.php';
require './lib/derniers-lus.php';



//si la config n'est pas déjà faite, on va vers la page de config
if (strlen(config::repertoireFilmsLocal()) == 0) {
    Helper_redirection::redirige('parametrages.php');
}

$recherche = Helper_var::post_or_session('search_query', '');
$rech_titre = Helper_var::post_or_session('rech_titre', false);
$rech_synopsis = Helper_var::post_or_session('rech_synopsis', false);
$rech_acteurs = Helper_var::post_or_session('rech_acteurs', false);
$rech_realisateur = Helper_var::post_or_session('rech_realisateur', false);
$rech_nom_fichier = Helper_var::post_or_session('rech_nom_fichier', false);
$action = strtolower(Helper_var::post_get_or_session('action', ''));

$rech_genres_etendus = Helper_var::post_or_session('rech_genres_etendus', false);

//coche le $rech_titre par défaut s'il y a aucun de coché
if ($rech_acteurs == false && $rech_synopsis == false && $rech_nom_fichier == false && $rech_realisateur == false) {
    $rech_titre = true;
}

//Gestion du nombre de dernier ajouts
$nb_derniers_ajouts_prec = Helper_var::session_var('nb_derniers_ajouts', config::nb_visu_ajouts());
$nb_derniers_ajouts = Helper_var::get_or_session('nb_derniers_ajouts', config::nb_visu_ajouts());

$nb_derniers_ajouts_change = false;
if ($nb_derniers_ajouts_prec != $nb_derniers_ajouts) {
    $nb_derniers_ajouts_change = true;
}
//si on est en mode admin et si la page vient d'ailleurs, on force le nombre de derniers ajouts
if (Gestion_admin::est_connecte() && Helper_redirection::referer_different_page_actuelle()) {
    $nb_derniers_ajouts_change = true;
}

if ($nb_derniers_ajouts_prec > $nb_derniers_ajouts) {
    $nb_derniers_ajouts_prec = $nb_derniers_ajouts;
}
//var_dump($nb_derniers_ajouts_change);
//Gestion du nombre d'historique
$nb_derniers_lus_prec = Helper_var::session_var('nb_derniers_lus', config::nb_visu_histo());
$nb_derniers_lus = Helper_var::get_or_session('nb_derniers_lus', config::nb_visu_histo());
$nb_derniers_visus_change = $nb_derniers_lus != $nb_derniers_lus_prec;

//var_dump($nb_derniers_lus.'   '.$nb_derniers_lus_prec.'  '.$nb_derniers_visus_change);
//connexion à la base de données MyVOD
$MyVOD_DB = new MyVOD_DB();

$nb_films = Helper_var::session_var('nb_films', -1);
if (($nb_films == -1) || $nb_derniers_ajouts_change) {
    refresh_cache_liste_films();
} else {
    //on va cherche le reste en session
    $liste_titre = Helper_var::session_var('liste_titre', array());
    $derniers_ajouts = Helper_var::session_var('derniers_ajouts', array());
}

//derniers lus
$derniers_lus = array();
//derniers lus pour les cases à cocher
if (config::nb_visu_histo() > 0) {
    //pas besoin d'actualiser les derniers lus quand on est admin car ils ne sont pas historisés
    if (Gestion_admin::est_connecte() && ($nb_derniers_visus_change == false)) {
        $derniers_lus = Helper_var::session_var('derniers_lus', array());
    } else {
        //var_dump('change');
        $derniers_lus = derniers_lus::get_derniers_lus($nb_derniers_lus);
        Helper_var::set_session('derniers_lus', $derniers_lus);
    }
}

//variables utilisés pour les genres
$genres = $MyVOD_DB->get_genres();
$nb_genres = count($genres);

//idem pour les années de prod
$annees = $MyVOD_DB->get_annees_production();
$nb_annees = count($annees);

//idem pour le type de publique
$publics = $MyVOD_DB->get_publictypes();
$nb_publics = count($publics);

//idem pour les nationalités
$nationalites = $MyVOD_DB->get_liste_nationalites_uniques();
$nb_nationalites= count($nationalites);


init_liste_genres_public_annees();


//par défaut, on prend tous les genres, années ...
$genre_filtre = Helper_var::post_or_session('genre_filtre', $genre_filtre);
$annee_filtre = Helper_var::post_or_session('annee_filtre', $annee_filtre);
$public_filtre = Helper_var::post_or_session('public_filtre', $public_filtre);
$nationalite_filtre= Helper_var::post_or_session('nationalite_filtre', $nationalite_filtre);

$taille_min =  Helper_var::post_or_session('taille_min', '0');
$taille_max =  Helper_var::post_or_session('taille_max', '0');

$filtre_jamais_vu =  Helper_var::post_or_session('filtre_jamais_vu', '0');

//tri l'année dans l'autre sens
$annees2 = array();
foreach ($annees as $a) {
    if (strlen($a->AnneeSortie) == 4) {
        //met au début du tableau
        array_unshift($annees2, $a);
    } else {
        //met à la fin
        array_push($annees2, $a);
    }
}
$annees = $annees2;


//liste des fichiers déjà mis en cache
require_once 'lib/data/cache_db.php';
$cache_db = new cache_db();
$paths = $cache_db->get_all_dico();

//gestion du tri
$type_tri = Helper_var::post_or_session('tri', config::tri_recherche_def());


//qualité (HD ou non)
$filtre_qualite = Helper_var::post_or_session('qualite', 0);
define('FILTRE_QUALITE_TOUS', 0);
define('FILTRE_QUALITE_SD', 1);
define('FILTRE_QUALITE_HD', 2);

//filtre si le film est soumis au contrôle parental
define('FILTRE_RECHERCHE_PARENTAL_TOUS', 0);
define('FILTRE_RECHERCHE_PARENTAL_NON', 1);
define('FILTRE_RECHERCHE_PARENTAL_OUI', 2);

//l'option de recherche
$filtre_recherche_parental = Helper_var::post_or_session('filtre_recherche_parental', 0);


$controle_parental_actif = controle_parental::filtrage_actif();
//si on est déconnecté et que le filtrage est actif, on le met à 0
if ($controle_parental_actif)
    $filtre_recherche_parental = 0;

//détection du changement de statut du contrôle parental
//pour inhiber le filtre de recherche
$last_filtre_statut = Helper_var::session_var('last_filtre_statut', $controle_parental_actif);

//var_dump("last_filtre_statut=$last_filtre_statut , controle_parental_actif=$controle_parental_actif, diff=" . ($last_filtre_statut != $controle_parental_actif));

if ($last_filtre_statut != $controle_parental_actif) {
    //on inhibe la recherche
    raz_recherche();
}
//change le statut pour une future detection
$_SESSION['last_filtre_statut'] = $controle_parental_actif;


//var_dump($_POST);
//exit();
//récup des données en fonction du filtre
$nombre_films_trouves = -1;

//recherche par genre (via statistiques)
//var_dump($action);
switch ($action) {
    case 'recherche_par_genre':
        raz_recherche();
        $action = 'recherche';
        $genre_filtre = array();
        $g = Helper_var::get_var('genre', '');

        $k = array_search_from_arr_stdclass($genres, $g, 'Nom');
        if ($k !== false) {
            $genre_filtre[$k] = $g;
        }
        break;

    case 'recherche_par_public':

        raz_recherche();
        $action = 'recherche';
        $public_filtre = array();
        $p = Helper_var::get_var('public', '');

        //var_dump($p);


        $k = array_search_from_arr_stdclass($publics, $p, 'Nom');
        if ($k !== false) {
            $public_filtre[$k] = $p;
        }
        break;

    case 'recherche_par_annee':
        //var_dump($annees);
        raz_recherche();
        $action = 'recherche';
        $annee_filtre = array();
        $a = Helper_var::get_var('annee', '');

        $k = array_search_from_arr_stdclass($annees, $a, 'AnneeSortie');
        if ($k !== false) {
            $annee_filtre[$k] = $a;
        }
        break;


    case 'recherche_par_decade':
        //var_dump($annees);
        raz_recherche();
        $action = 'recherche';
        $annee_filtre = array();
        $d = Helper_var::get_var('decade', '');
        if ($d == 'date inconnue')
            $d = '--';

        if (substr($d, 4, 2) == "'s") {
            $a = substr($d, 0, 4);
            for ($i = $a; $i < $a + 10; $i++) {
                $k = array_search_from_arr_stdclass($annees, $i, 'AnneeSortie');
                if ($k !== false) {
                    $annee_filtre[$k] = $i;
                }
            }
        } else {
            $k = array_search_from_arr_stdclass($annees, $d, 'AnneeSortie');
            if ($k !== false) {
                $annee_filtre[$k] = $d;
            }
        }

        break;
}




if ($action == 'recherche') {

//var_dump($_POST);
//exit();

    //si toutes les années sont cochées, on met à 'false' pour qu'il ne soit pas pris dans les requêtes
    $lst_annees = count($annee_filtre) == $nb_annees ? false : $annee_filtre;
    //idem pour les genres
    $lst_genres = count($genre_filtre) == $nb_genres ? false : $genre_filtre;
    //idem pour les publics
    $lst_publics = count($public_filtre) == $nb_publics ? false : $public_filtre;
    //idem pour les nationalités
    $lst_nationalites = count($nationalite_filtre) == $nb_nationalites ? false : $nationalite_filtre;
    
    /*    RECHERCHE    */
    
    //Lancement de la recherche
    $result = $MyVOD_DB->get_liste($recherche, $rech_titre, $rech_acteurs, $rech_realisateur , $rech_synopsis, $rech_nom_fichier, 
            $lst_genres, $lst_annees, $type_tri, '', $filtre_qualite, $lst_publics, $filtre_recherche_parental, $rech_genres_etendus,
            $lst_nationalites,$taille_min, $taille_max,$filtre_jamais_vu);

    //var_dump($result);
    
    $t_description_filtre = array();

    if (strlen($recherche) > 0) {

        //construction du label de description des résulats

        if ($rech_titre != false) {
            array_push($t_description_filtre, 'le titre, titre original');
        }
        if ($rech_acteurs != false) {
            array_push($t_description_filtre, 'les acteurs');
        }
        if ($rech_synopsis != false) {
            array_push($t_description_filtre, 'le synopsis');
        }
        if ($rech_nom_fichier != false) {
            array_push($t_description_filtre, 'le nom de ficher');
        }

        if($rech_realisateur !=false){
            array_push($t_description_filtre, 'le nom du réalisateur');
        }
        
        
        $description_filtre = '<strong>' . implode(', ', $t_description_filtre) . '</strong>' . " contient <strong>'$recherche'</strong>";

        $t_description_filtre = array();
        array_push($t_description_filtre, $description_filtre);
    } else {
        $description_filtre = "";
    }

    //filtre par genres
    if ($genre_filtre != false) {
        if (count($genre_filtre) === 1) {
            if ($rech_genres_etendus) {
                array_push($t_description_filtre, "<b>au moins un des genres</b> est <b>'" . join(', ', $genre_filtre) . "'</b>");
            } else {
                array_push($t_description_filtre, "le <b>genre principal</b> est <b>'" . join(', ', $genre_filtre) . "'</b>");
            }
        } else {
            if (count($genre_filtre) != $nb_genres) {
                if ($rech_genres_etendus) {
                    array_push($t_description_filtre, "<b>au moins un des genres</b> sont <b>'" . join(', ', $genre_filtre) . "'</b>");
                } else {
                    array_push($t_description_filtre, "les <b>genres principaux</b> sont <b>'" . join(', ', $genre_filtre) . "'</b>");
                }
            }
        }
    }

    //filtre par public
    if ($public_filtre != false) {
        if (count($public_filtre) === 1) {
            array_push($t_description_filtre, "le <b>type de publique</b> est <b>'" . join(', ', $public_filtre) . "'</b>");
        } else {
            if (count($public_filtre) != $nb_publics) {
                array_push($t_description_filtre, "les <b>types de publique</b> sont <b>'" . join(', ', $public_filtre) . "'</b>");
            }
        }
    }



    //filtre par années de sortie
    if ($annee_filtre != false) {
        if (count($annee_filtre) === 1) {
            array_push($t_description_filtre, "l'<b>année de sortie</b> est <b>'" . join(', ', $annee_filtre) . "'</b>");
        } else {
            if (count($annee_filtre) != $nb_annees) {
                array_push($t_description_filtre, "les <b>années de sortie</b> sont <b>'" . join(', ', $annee_filtre) . "'</b>");
            }
        }
    }

    //filtre par nationalités
    if($nationalite_filtre !=false){
        if(count($nationalite_filtre)===1){
            array_push($t_description_filtre, "la '<b>nationalité</b> est <b>'" . join(', ', $nationalite_filtre) . "'</b>");
        }else{
            if (count($nationalite_filtre) != $nb_nationalites) {
                array_push($t_description_filtre, "les <b>nationalités</b> sont <b>'" . join(', ', $nationalite_filtre) . "'</b>");
            }
        }
    }


    //la qualité
    if ($filtre_qualite != FILTRE_QUALITE_TOUS) {
        if ($filtre_qualite == FILTRE_QUALITE_SD) {
            array_push($t_description_filtre, "la qualité est <strong>SD</strong>");
        }

        if ($filtre_qualite == FILTRE_QUALITE_HD) {
            array_push($t_description_filtre, "la qualité est <strong>HD</strong>");
        }
    }

    //taille du fichier
    //$taille_min, $taille_max
    $txt_taille_min = $taille_min>=1000 ? ($taille_min/1000).' Go' : $taille_min.' Mo';
    $txt_taille_max = $taille_max>=1000 ? ($taille_max/1000).' Go' : $taille_max.' Mo';
    
    if($taille_min >0 && $taille_max>0){
        if($taille_min<$taille_max){
            array_push($t_description_filtre, "la <strong>taille du fichier</strong> est compris <strong>entre $txt_taille_min et $txt_taille_max</strong>");
        }else{
            array_push($t_description_filtre, "la <strong>taille du fichier</strong> est compris <strong>entre $txt_taille_max et $txt_taille_min</strong>");
        }
    }else{
        if($taille_min >0){
            array_push($t_description_filtre, "la <strong>taille du fichier</strong> est <strong>> à $txt_taille_min</strong>");
        }
        if($taille_max>0){
            array_push($t_description_filtre, "la <strong>taille du fichier</strong> est <strong>< à $txt_taille_max</strong>");
        }
    }
    
    //film dont j'ai jamais vu
    if($filtre_jamais_vu!=0){
        array_push($t_description_filtre,"la vidéo <strong>n'a pas encore été visionnée</strong>");
    }
    
    
    
    //filtrage_autorisé
    if ($filtre_recherche_parental != FILTRE_RECHERCHE_PARENTAL_TOUS) {
        if ($filtre_recherche_parental == FILTRE_RECHERCHE_PARENTAL_NON) {

            array_push($t_description_filtre, "la liste est <strong>interdit au filtrage parental</strong>");
        }

        if ($filtre_recherche_parental == FILTRE_RECHERCHE_PARENTAL_OUI) {
            array_push($t_description_filtre, "la liste appartient au <strong>filtrage parental autorisé</strong>");
        }
    }


    //on recolle les descriptions
    $n = count($t_description_filtre);

    $description_filtre = '';
    for ($i = 0; $i < $n; $i++) {
        $glue = ', dont ';
        switch ($i) {
            case 0:
                $glue = '';
                break;
            case $n - 1:
                $glue = ' et dont ';
                break;
        }

        $description_filtre.=$glue . $t_description_filtre[$i];
    }
    //ajoute un point s'il n'y a pas de résultat (car il y a un ':' avec le nombre s'il y a résultat)
    $description_filtre.= count($result) == 0 ? '.' : '';

    $nombre_films_trouves = count($result);
} else {
    $result = null;
    //$result = mycinema_db::get_liste('', false, false, false);
}

$_SESSION['derniere_recherche']=$result;


//type d'affichage
$affichage_gallerie = MyVOD::affichage_galerie(); //config::affichage_gallerie();
$type_affichage = Helper_var::post_get_or_session("type_affichage", "-1");
if ($type_affichage != -1) {
    $affichage_gallerie = $type_affichage;
}


//planels pliés ou non
$recherche_deplie = Helper_var::post_or_session("recherche_deplie", "0");
//si le type d'affichage passé en get, on laisse déplié le panel de recherche
if(isset($_GET['type_affichage'])){
    $recherche_deplie=true;
}

$genre_deplie = Helper_var::post_or_session("genre_deplie", "0");
$annee_deplie = Helper_var::post_or_session("annee_deplie", "0");
$public_deplie = Helper_var::post_or_session("public_deplie", "0");
$nationalite_deplie = Helper_var::post_or_session("nationalite_deplie", "0");
//$taille_deplie = Helper_var::post_or_session("taille_deplie", "0");

$avance_deplie = $taille_min>0 || $taille_max>0;



//les panels se replie si tous les éléments sont cochés (meilleur visibilité)
if(count($genre_filtre)==$nb_genres){$genre_deplie="0";}
if(count($annee_filtre)==$nb_annees){$annee_deplie="0";}
if(count($public_filtre)==$nb_publics){$public_deplie="0";}
if(count($nationalite_filtre)==$nb_nationalites){$nationalite_deplie="0";}

//version V.2.2.1 (pour les petits ecrans, on replie encore d'un niveau les panels)
$panels_deplie = ($genre_deplie==true) || ( $annee_deplie==true) || ($public_deplie==true) || ($nationalite_deplie ==true) || ($avance_deplie == true);
//$panels_deplie=false;


//Génération de la page
require './template/index.phtml';


/**
 * 
 * FIN
 * 
 */


function refresh_cache_liste_films() {
    global $nb_films, $liste_titre, $derniers_ajouts;
    global $nb_derniers_ajouts;
    global $MyVOD_DB;

    //le nombre de films
    $nb_films = $MyVOD_DB->nb_films();

    //Liste des titres pour la recherche(auto-complétion)
    $liste_titre = $MyVOD_DB->get_liste_titres();

    //derniers ajouts
    $derniers_ajouts = $MyVOD_DB->get_n_derniers_ajouts($nb_derniers_ajouts);


    //on met session
    Helper_var::set_session('nb_films', $nb_films);
    Helper_var::set_session('liste_titre', $liste_titre);
    Helper_var::set_session('derniers_ajouts', $derniers_ajouts);

    //var_dump('refresh_cache_liste_films()');
}

function init_liste_genres_public_annees() {
    global $genres, $genre_filtre;
    global $annees, $annee_filtre;
    global $publics, $public_filtre;
    global $nationalites, $nationalite_filtre;
    
    //var_dump('init');

    $genre_filtre = array();
    foreach ($genres as $g) {
        array_push($genre_filtre, $g->Nom);
    }
    $annee_filtre = array();
    foreach ($annees as $a) {
        array_push($annee_filtre, $a->AnneeSortie);
    }
    $public_filtre = array();
    foreach ($publics as $p) {
        array_push($public_filtre, $p->Nom);
    }

    $nationalite_filtre=$nationalites;

    //var_dump($genres);
    //var_dump($genre_filtre);
}

function raz_recherche() {
    global $action, $recherche;
    global $rech_titre, $rech_synopsis, $rech_acteurs, $rech_nom_fichier;
    global $filtre_recherche_parental;
    global $rech_genres_etendus;

    global $genre_filtre, $annee_filtre, $public_filtre, $nationalite_filtre;

    global $taille_min, $taille_max;


    init_liste_genres_public_annees();
    //sauvegarde de la session
    Helper_var::set_session('genre_filtre', $genre_filtre);
    Helper_var::set_session('annee_filtre', $annee_filtre);
    Helper_var::set_session('public_filtre', $public_filtre);
    Helper_var::set_session('nationalite_filtre', $nationalite_filtre);
    Helper_var::set_session('action', '');
    Helper_var::set_session('search_query', '');

    $action = '';
    //reset des champs (de recherche et cases à cocher)
    $recherche = '';
    $rech_titre = true;
    $rech_synopsis = false;
    $rech_acteurs = false;
    $rech_nom_fichier = false;

    $filtre_recherche_parental = 0;
    $rech_genres_etendus = false;

    $taille_min=0;
    $taille_max=0;

    refresh_cache_liste_films();
    //var_dump('raz_recherche()');
    //exit();
}

function array_search_from_arr_stdclass($array, $search, $parametre) {
    foreach ($array as $key => $value) {
        //var_dump($value->$parametre,$search);
        if ($value->$parametre == $search) {
            return $key;
        }
    }
    return false;
}
