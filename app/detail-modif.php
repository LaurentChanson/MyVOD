<?php
$titre_page = 'Modification du film';


require 'commun.php';

//redirect admin
require_once './lib/check-admin.php';

require_once './lib/webrecherche.php';

require_once 'lib/gestion-cache.php';

//traitement de la variable get

//gestion de la fiche à modifier
//$filename_or_id = Helper_var::get_var('file', 0);
//LC : 31/05/2018 : retour par post quand on vient de la page 'recherche-web.php'
$filename_or_id = Helper_var::post_or_get_var('file', 0);
//var_dump($filename_or_id);

//si pas de paramètre transmis, on prend celle en session
//(peut poser problèmes si multi-onglets)
if ($filename_or_id === 0) {
    if (isset($_SESSION['derniere_fiche_consulte'])) {
        $filename_or_id = $_SESSION['derniere_fiche_consulte'];
    }
}
$_SESSION['derniere_fiche_consulte'] = $filename_or_id;

$_SESSION['page_retour'] = 'detail-modif.php';

//évite de retombre sur la même page lors d'une redirection
MyVOD::actualise_derniere_page();   //obligatoire car il est faut trop tard
 
$page_retour=Helper_redirection::get_derniere_page();

if(strlen($page_retour)==0 || stripos($page_retour,'recherche-web.php')!==false){
    $page_retour=Helper_redirection::get_page_courante();
}
$page_retour=  str_replace('detail-modif.php', 'detail.php', $page_retour);

//var_dump($page_retour);


$MyVOD_DB = new MyVOD_DB();

/*
//LC 20/12/2018 : déplacé tout en bas du script
$liste_type_films=$MyVOD_DB->get_liste_type_film();
$liste_nationalite=$MyVOD_DB->get_liste_nationalites();
*/
/*
var_dump($filename_or_id);
var_dump($_POST);
exit();
*/



//traitement du formulaire post (sauvegarde)
//RQ : s'il y a une recherche via le net, 'action' n'est pas dédini
if (isset($_POST['action'])) {
    
        
        
    //
    //action : sauvegarder (aussi quand on récup l'affiche)
    //
        
    if ($_POST['action'] == 'sauver' || $_POST['action'] == 'recup_affiche' || $_POST['action'] == 'Upload') {
        //var_dump($_POST);
        
        $fichefilm = new MyVOD_Details();

        $fichefilm->ID = $_SESSION['derniere_fiche_consulte'];
        $fichefilm->Filename = $_POST['filename'];

        $fichefilm->DateSortie = $_POST['dateSortie'];

        $fichefilm->AnneeSortie= $_POST['anneeSortie'];
        
        //si l'année de sortie est vide, on extrait celle de la date
        if($fichefilm->AnneeSortie==''){
            $annee = (int) (substr($fichefilm->DateSortie, 0,4));
            if ($annee > 0) {
                $fichefilm->AnneeSortie = $annee;
            }
        }

        $fichefilm->TitleKey = $_POST['title_key'];
        $fichefilm->Titre = $_POST['titre'];
        $fichefilm->TitreOriginal = $_POST['titreOriginal'];
        $fichefilm->MovieLink = $_POST['movieLink'];

        $fichefilm->TypePublic = $_POST['publicType'];
        //$fichefilm->DureeSec =
        $fichefilm->duree_from_html5($_POST['duree']);
        
        $fichefilm->NbSaisons = $_POST['nbSaisons'];
        $fichefilm->NbEpisodes = $_POST['nbEpisodes'];
        
        $fichefilm->GenreNom1 = $_POST['genre1'];
        $fichefilm->GenreNom2 = $_POST['genre2'];
        $fichefilm->GenreNom3 = $_POST['genre3'];
        $fichefilm->Realisateur = $_POST['realisateur'];
        $fichefilm->Acteurs = $_POST['acteurs'];
        $fichefilm->NotePresse = $_POST['notePresse']/10;
        $fichefilm->NoteSpec = $_POST['noteSpec']/10;
        $fichefilm->Synopsis = $_POST['synopsis'];
        $fichefilm->Affiche = $_POST['affiche'];

        $fichefilm->Nationalite = $_POST['nationalite'];
        $fichefilm->TypeFilm= $_POST['type_media'];
        
        $fichefilm->UrlImageSource=$_POST['affiche_url'];
        
        $fichefilm->BandeAnnonceUrl=$_POST['ba_link'];
        $fichefilm->BandeAnnonceCode=$_POST['ba_code'];
        $fichefilm->BandeAnnonceEmbed=$_POST['ba_embed'];
        
        $fichefilm->NumFicheAllocine=$_POST['code_allocine'];
        $fichefilm->NumFicheDvdFr=$_POST['code_dvdfr'];
        
        $fichefilm->NumFicheTmdb=$_POST['code_tmdb'];
        
        $fichefilm->HD720=0;
        $fichefilm->HD1080=0;
        if(isset($_POST['resolution'])){
            if($_POST['resolution']==720)$fichefilm->HD720=1;
            if($_POST['resolution']==1080)$fichefilm->HD1080=1;
        }
        $fichefilm->MessageModif = $_POST['messageModif'];
        
        //var_dump($fichefilm);
        if(isset($_POST['bandes_annonces'])){
            $fichefilm->DeserialiseBandesAnnonces($_POST['bandes_annonces']);
            
        }
        message::ajouter_alerte_info('La fiche du film "<b>'.$fichefilm->Titre.'</b>" a bien été enregistrée.' );
        /*
        var_dump($_POST);
        var_dump($fichefilm);
        exit();
        
        */
        //enregistrement dans la bdd
        $MyVOD_DB->fiche_enregistrer($fichefilm);
        if ($_POST['action'] == 'sauver') {
            //exit();
            //Helper_redirection::redirige_derniere_page();
            Helper_redirection::redirige($page_retour);
        }
       
    }
    
    
    

    
    
    //
    //action : récupérer l'affiche
    //
    
    if ($_POST['action'] == 'recup_affiche') {
        /*
        var_dump($_POST);
        var_dump($_SESSION['derniere_fiche_consulte']);
        exit();

        */
        
        //on récupère l'affiche seulement
        $url = $_POST['affiche_url'];
        
        if($url==''){
            message::ajouter_alerte_ko("Veuillez remplir le champ <b>'url de l'affiche'</b>.");
        }else{
            
            $nouvelle_affiche = MyVOD::affiche_recup_to_local($url); // MyCinemaAffiche::recuperer_affiche($url);

            if ($nouvelle_affiche != false) {
                //on update dans la base (que l'affiche)
                $MyVOD_DB->update_affiche_by_id($_SESSION['derniere_fiche_consulte'], $nouvelle_affiche,$url);
                message::ajouter_alerte_info('L\'affiche du film "<b>'.$fichefilm->Titre.'</b>" a bien été changée.' );
                
            }else{
                $message_htts='';
                if(substr($url, 0, 5)=='https'){
                    $message_htts="\n<br>C'est un lien en https. Veuillez activer l'extension php <b>'php_openssl.dll'</b> et activer l'option <b>'allow_url_fopen = On'</b>.";
                }
                
                
                message::ajouter_alerte_ko("Impossible de récupérer l'affiche à partir de l'adresse '$url'.".$message_htts);
            }
        }
        
    //
    //action : upload l'affiche
    // 
    } else if ($_POST['action'] == 'Upload') {   
        //récupération de l'image
        $image_temp = $_FILES['upload_affiche'];
        if($image_temp['tmp_name']==''){
            message::ajouter_alerte_ko("Veuillez choisir une image avant de l'envoyer.");
        }else{
            if($image_temp['error']!=0){
                message::ajouter_alerte_ko("Erreur pendant l'envoi de l'image.");
            }else{
                $url=$image_temp['tmp_name'];
                $nouvelle_affiche = MyVOD::affiche_recup_to_local($url,$image_temp['name'] ); 

                //var_dump($nouvelle_affiche);
                
                if ($nouvelle_affiche != false) {
                    //on update dans la base (que l'affiche)
                    $MyVOD_DB->update_affiche_by_id($_SESSION['derniere_fiche_consulte'], $nouvelle_affiche,"");
                }
                
                //var_dump($_POST);
                //var_dump($_FILES);

                //exit();
            }
        }
        

        
        
        
       
        
        
        
    //
    //suppression de la bande annonce
    //
        
    } else if ($_POST['action'] == 'supprimer_bande_annonce') {
        //récupération de l'ID à supprimer
        $id_ba_a_supprimer=  Helper_var::post_var('id_bande_annonce_a_supprimer', 0);
        if($id_ba_a_supprimer>0){
            
            //message::ajouter_alerte_info(
            //var_dump($_POST);
            $ba = new BandeAnnonce();
            $ba=$MyVOD_DB->bande_annonce_get_by_id($id_ba_a_supprimer);
            //var_dump($ba);
            //exit();
            //
            //suppression dans la bdd
            $MyVOD_DB->bande_annonce_supprimer_by_id($id_ba_a_supprimer);
            //ajout du message
            message::ajouter_alerte_info("Bande annonce <b>'".$ba->titre."'</b> suprimée.");
            
            
            //var_dump( $id_ba_a_supprimer);
            Helper_redirection::redirige('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'].'#debut_liste_bande_annonce');
            
            
            
            
            //Helper_redirection::redirige_derniere_page();
        }
       
    }
}

//message::ajouter_alerte_info("web_fiche_code=".Helper_var::post_or_get_var('web_fiche_code', false));

//par défaut
//va chercher les données de MyVOD (dans la base de données)

$genres = $MyVOD_DB->get_genres();
$publicTypes = $MyVOD_DB->get_publictypes();
$fichefilm = new MyVOD_Details;
$fiche_exists=$MyVOD_DB->fiche_get_details($filename_or_id, $fichefilm);

//LC 20/12/2018 : déplacé tout en bas du script
$liste_type_films=$MyVOD_DB->get_liste_type_film();
$liste_nationalite=$MyVOD_DB->get_liste_nationalites();

//traitement du post pour la recherche allo ciné (retour de la page du choix de la fiche)
//recherche_allocine_id
//ajouter le type recherche
$code_allocine = Helper_var::post_or_get_var('web_fiche_code', false);
$type_recherche = 0;

//si la fiche n'existe pas, on la crée
if($fiche_exists==false){
    $fichefilm = new MyVOD_Details();

    $fichefilm->ID = $_SESSION['derniere_fiche_consulte'];
    $fichefilm->Filename = $filename_or_id;
        
    //enregistrement dans la bdd
    $MyVOD_DB->fiche_enregistrer($fichefilm); 
        
        
        
}

//si la fiche n'est pas dans la bdd MyVOD, on patche le filename (LC: 01/10/2016)
if($fichefilm->Filename ==  NULL){
    $fichefilm->Filename = $filename_or_id;
}

//
//  Modification de la fiche avec les données du net
//
//rq : obligé de faire une sécu avec l'action car on change l'affiche par ex. et on ecrase avec la recherche.
if (($code_allocine != false ) && ( isset($_POST['action'])==false)  ) {


    
    
    //on va chercher le détail sur alliciné ou dvdfr
    $type_recherche = Helper_var::post_or_get_var('type_recherche', type_recherche::RECHERCHE_ALLOCINE);
    $error_retour = '';
    $fiche = new WebGetFilmData();
    $fiche = WebRecherche::GetFilm($code_allocine, $type_recherche, $error_retour);

    //var_dump($fiche);
    //exit();
    
    if ($fiche == false) {
        message::ajouter_alerte_ko($error_retour);
        //html_bootstrap_alert_danger($error_retour);
    } else {
        
        //var_dump($fiche);
        
        //on convertit la fiche allocine en fiche MyVOD_Details
        $fichefilm->InitFromWebGetFilmData($fiche);
        
        //on regarde par rapport au nom du fichier si ce n'est pas un film en HD
        $fichefilm->MAJChampHDxxFromFilename();
        
        //récupération de l'affiche
        $fichier_affiche = MyVOD::affiche_recup_to_local($fiche->posterURL) ;
        
        $fichefilm->Affiche = $fichier_affiche;
        
        message::ajouter_alerte_info('<i class="fa fa-thumbs-up" aria-hidden="true"></i>
 Les données sont récupérées du net. <b><i>Pensez à sauvegarder</i></b> cette fiche en cliquant sur le bouton <b><i>\'Valider\'</i></b>.');
        //message::ajouter_alerte_warning('test');
//        var_dump($fichefilm);
//        exit();
    }  
}

//récup du chemin du fichier dans le cache
$file_info = new FileInfos();

gerer_cache($fichefilm->Filename, $file_info);

//$code_allocine = AllocineAPIWrapper::GetIDFromURL($fichefilm->MovieLink);
//$code_dvdfr = DVDFRAPIWrapper::GetIDFromURL($fichefilm->MovieLink);
$code_allocine = $fichefilm->NumFicheAllocine;
$code_dvdfr = $fichefilm->NumFicheDvdFr;
$code_tmdb=$fichefilm->NumFicheTmdb;
//var_dump($fichefilm);

require_once 'template/detail-modif.phtml';
