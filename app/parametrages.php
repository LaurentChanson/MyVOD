<?php

require_once 'commun.php';

//seul l'admin peut accéder à cette page
require_once './lib/check-admin.php';

define("ACTION_ENREGISTRER", 'enregistrer');
define("ACTION_MODIFIER_MDP", 'modifier_mdp');

//var_dump($_POST);

$action = Helper_var::post_var('action', false);

if ($action != false) {
    switch ($action) {

        case ACTION_ENREGISTRER:
            
            $repertoireFilmsLocal = Helper_var::post_var('repertoireFilmsLocal', '');
            $repertoireWebPartage = Helper_var::post_var('repertoireWebPartage', '');
            $repertoireWebFilms = Helper_var::post_var('repertoireWebFilms', '');
            $affichage_gallerie = Helper_var::post_var('affichage_gallerie', '1');
            $affichage_liste_tablette= Helper_var::post_var('affichage_liste_tablette', '1');
            
            $controle_parental =  Helper_var::post_var('controle_parental_actif','1');
            $code_parental =  Helper_var::post_var('code_parental','0000');
            
            $nb_visu_histo=  Helper_var::post_var('nb_visu_histo','8');
            $nb_visu_ajouts=  Helper_var::post_var('nb_visu_ajouts','8');	
            
            $affichage_visionnes_apres_ajouts=Helper_var::post_var('affichage_visionnes_apres_ajouts','0');
            $taille_fichiers_64_bits=Helper_var::post_var('taille_fichiers_64_bits','0');
            
            $mots_cles_suppl_google_search=Helper_var::post_var('mots_cles_suppl_google_search','');
            //rince la variable de session pour prendre en compte le nouveau paramétrage d'affichage
            $_SESSION['type_affichage']=-1;
            
            
            $tri_recherche_def=Helper_var::post_var('tri_recherche_def','0');
            //change le tri par défaut de la page d'accueil
            $_SESSION['tri']=$tri_recherche_def;
            
            $tmdb_api_key=Helper_var::post_var('tmdb_api_key','');
            
            
            $type_recherche_def=Helper_var::post_var('type_recherche_def','2');
            
            $tri_par_ip_derniers_lus=Helper_var::post_var('tri_par_ip_derniers_lus','0');
            
            config::set_parametres($repertoireFilmsLocal, $repertoireWebPartage, $repertoireWebFilms,$affichage_gallerie,$controle_parental,
                    $code_parental,$nb_visu_histo,$nb_visu_ajouts,$affichage_visionnes_apres_ajouts,$taille_fichiers_64_bits,
                    $mots_cles_suppl_google_search,$affichage_liste_tablette, $tri_recherche_def, $tmdb_api_key, $type_recherche_def, $tri_par_ip_derniers_lus  );
            message::ajouter_alerte_ok("Paramètres enregistrés.");
            
            break;

        case ACTION_MODIFIER_MDP:
            $mdp1=Helper_var::post_var('mdp1', '');
            $mdp2=Helper_var::post_var('mdp2', '');
            
            if($mdp1!=$mdp2){
                message::ajouter_alerte_ko( "Les mots de passe 1 & 2 sont dirrérents.");
            }else{
                //on enregistre dans la bdd
                config::set_mot_de_pass_admin($mdp1);
                message::ajouter_alerte_ok( "Nouveau mot de passe enregistré.");
            }
            
            break;
    }

    //todo a remettre (pas besoin car il y a les messages)
    //Helper_redirection::redirige('plusieurs-fichiers.php');
}


//affiche la page
require_once 'template/parametrages.phtml';