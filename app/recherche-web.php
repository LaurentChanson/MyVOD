<?php
//entêtes HTML
require './commun.php';

//redirect admin
require_once './lib/check-admin.php';

require_once 'lib/webrecherche.php';

define("PARAM_VALIDATION_AUTO",'validation_auto');

//gestion de la persistance des recherches

$recherche = Helper_var::post_get_or_session('recherche_web', '');

//gestion de la recherche d'origine
$recherche_ori=Helper_var::get_var('recherche_web', '');
if(strlen($recherche_ori)>0){
    // trouvé, mise à jour de la session
    Helper_var::set_session('recherche_web_ori', $recherche_ori);
}else{
    //non trouvé, on prend celle de la session
    $recherche_ori=  Helper_var::session_var('recherche_web_ori', $recherche);
}

//$validation_auto=Helper_var::get_var(PARAM_VALIDATION_AUTO, 0);
$validation_auto=Helper_var::get_or_session(PARAM_VALIDATION_AUTO, 0);


/*
var_dump($recherche);
var_dump($_POST);
var_dump($_SESSION);
exit;
*/

$ignore_dictionnaire = Helper_var::post_get_or_session('ignore_dictionnaire', 'ON');
$enleve_dernier_mot_si_non_trouve= Helper_var::post_get_or_session('enleve_dernier_mot_si_non_trouve', 'ON');

$type_recherche = Helper_var::post_get_or_session('type_recherche', config::type_recherche_def());
$type_recherche = new type_recherche($type_recherche);

$page_retour = Helper_var::session_var('page_retour', 'detail-modif.php');



//var_dump($ignore_dictionnaire);
//var_dump($_POST);
//exit;

//valider la recherche (une fois qu'on a choisi un résultat)
if (isset($_POST['web_fiche_code'])) {
    //on affiche le POST
    //rq : on ne devrait pas être là car le formulaire post est envoyé à $page_retour
    var_dump($_POST);
    exit;
}

$error_retour = "";

//ignore le dictionnaire
$config_db = new config_db();
$keywords = $config_db->get_liste_mots_cle_ignore(true);
$keys = array();    

foreach ($keywords as $k){
    array_push($keys, $k->mot);
}

$k=$keys;
//on remet dans l'ordre pour l'affichage
sort($keys);

//tri a l'envers  (les mots commancant par z est
//arsort($k);

//var_dump($keys);


if($ignore_dictionnaire){
    //$recherche=str_ireplace( $k,'',$recherche);
    //strstr($recherche,$remplace_k);
    
    //l'espace est important car sinon, les mots ne sont pas séparés
    $recherche=str_ireplace( $k,' ',$recherche);
}



//enlève la ponctuation
$recherche=  trim(str_ireplace(array( '?', ',', '.', ':', '!',';','_','-','+'), ' ', $recherche));

//enlève les doubles espaces qui ont été générés par l'enlèvement de la ponctuation
do{
    $recherche=str_ireplace( '  ',' ',$recherche,$i);
}while($i>0);

$recherche=  trim($recherche);

//$recherche="zeghzeighzehg";

//lance la recherche
LancerRecherche:
if ($recherche != '') {
    $donnees = WebRecherche::Rechercher($recherche, $type_recherche->numero, $error_retour);

    //var_dump($donnees);
    //si la recherche est infructueuse, on enleve le dernier mot et on relance...
    
    if($donnees == false){
        $nb_resulatats=0;
    }else{
        $nb_resulatats = count($donnees);
    }
    if($enleve_dernier_mot_si_non_trouve){
        if($nb_resulatats==0){
            $mots_recherche= explode(' ', $recherche);

            array_pop ( $mots_recherche);

            if(count($mots_recherche)>0){
                $recherche=implode(' ',$mots_recherche);
                goto LancerRecherche;
            }
        }
    }
}

/*
var_dump($recherche);
var_dump($nb_resulatats);
exit;
  */ 






require_once 'template/recherche-web.phtml';

