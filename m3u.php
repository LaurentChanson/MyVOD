<?php
//http://stackoverflow.com/questions/2015985/using-header-to-rewrite-filename-in-url-for-dynamic-pdf
//c'est une astuce pour que quand on fait enregistrer sous, c'est le nom en m3u qui apparait

session_start();

require_once 'lib/functions-helper.php';
require_once 'lib/data/cache_db.php';
require_once 'lib/data/myvod_db.php';
require_once 'lib/m3u-gen.php';
require_once 'lib/gestion-cache.php';
require_once 'lib/derniers-lus.php';
require_once 'lib/gestion-admin.php';

$type_mime_m3u = 'audio/x-mpegurl';



if (isset($_GET['path'])) {
    //on recoit directement le "path", on met le path dans le fichier m3u directement

    header('Content-type: '.$type_mime_m3u);

    //via $m3u_data
    $fichier = new FileInfos();
    $fichier->full_path = urldecode($_GET['path']);
    $fichier->file_name = basename($fichier->full_path);
    $nom_fichier_m3u = $fichier->file_name;

    if (Helper_system::nav_OS_is_windows()) {
        header('Content-Disposition: inline; attachment; filename="' . $nom_fichier_m3u);
    } else {
        header(' filename="' . $nom_fichier_m3u);
    }
    //var_dump($fichier);       
    $m3u_data = path_local_to_distant($fichier->full_path);
} else {


    $nom_fichier = urldecode($_GET['file']);
    //on ne sait pas si c'est un nombre (id) ou le filename
    $myvod_db = new MyVOD_DB();

    $detail_film = new MyVOD_Details();
    $myvod_db->fiche_get_details($nom_fichier, $detail_film);


    if($detail_film->Filename==null ){

        if(controle_parental::filtrage_actif()){
            afficher_erreur( "La fiche dans la base de données n'existe plus ou est interdite.");
        }
        afficher_erreur( "La fiche dans la base de données n'existe plus.");
        
    }
    
    

    $nom_fichier = $detail_film->Filename;

    //on regarde s'il y des fichiers associés
    $nom_fichier_m3u = substr($nom_fichier, 0, strrpos($nom_fichier, '.')) . '.m3u';

    
    
    //récup des données du cache  
    $file_info = new FileInfos();
    gerer_cache($nom_fichier, $file_info);

    
    //var_dump(file_exists_utf8($file_info->full_path));
    
    
    //test de l'existance du fichier
    if (file_exists_utf8($file_info->full_path) == false) {
        //on ne retrouve pas
        afficher_erreur( "Le fichier est introuvable ou n'existe plus...");

    }

    //enregistre dans les derniers lus si non admin(car l'admin peut faire des tests et poluer les derniers lus)
    if(Gestion_admin::est_connecte()==false){
        derniers_lus::ajouter($detail_film->Filename);
    }
    
   
    //pour tester (voir le résultat dans le navigateur (debug))
    //il suffit de mettre en commentaire la ligne suivante
    header('Content-type: '.$type_mime_m3u);
    //header("Content-Type:text/html; charset=utf-8"); //pour debug
    //var_dump(Gestion_admin::est_connecte());

    //efface la 1ere ligne vide
    //ob_clean();
    //header("Content-Disposition: inline; filename=listeDeLecture.m3u");
    // le nom du fichier par défaut et propose telechargement m3u ,pour windows

    if (Helper_system::nav_OS_is_windows()) {
        header('Content-Disposition: inline; attachment; filename="' . $nom_fichier_m3u);
    } else {
        header(' filename="' . $nom_fichier_m3u);
    }




//récupération des fichiers liés
//$myvod_db->liaison_get_liste_from_filename($nom_fichier, $liaisons);
    if (strtolower(substr($file_info->full_path, -4)) == '.wpl') {
        //le wpl est window media playlist
        //on récupère le contenu
        //et le transforme en m3u
        $tfichiers = wpl_get_liste_fichiers($file_info->full_path);

        $dossier = dirname($file_info->full_path);


        $m3u_data = '';
        foreach ($tfichiers as $f) {

            $f = path_local_to_distant($dossier . '/' . $f);

            $m3u_data = $m3u_data . (strlen($m3u_data) > 0 ? "\n" : '') . $f;
        }
    } else {

        //autre cas on met le fichier
        $m3u_data = path_local_to_distant($file_info->full_path);

        //affiche les films suivants (ceux qui sont rattachés)
        foreach ($detail_film->t_liaisons as $f) {
            $file_info = new FileInfos;
            gerer_cache($f, $file_info);
            //met dans la liste si le fichier existe
            if (strlen($file_info->full_path)) {
                $m3u_data.= "\n" . path_local_to_distant($file_info->full_path);
            }
        }
    }
}

//un fichier m3u est en ansi
echo(utf8_decode($m3u_data));

//fin du traitement

exit;


function afficher_erreur($message){
    
    
header("Content-Type:text/html; charset=utf-8");
        ?>
        <html>
            <head>
                <title>MyVOD : Erreur</title>
                <!-- compatibilité IE (a tester) -->
                <meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; chrome=1">
                <!-- évite les pb d'accents -->
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta name="author" content="Laurent CHANSON">
                
                <!-- On ouvre la fenêtre à la largeur de l'écran -->
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <!-- favicon marche pas ici  -->
                <link rel="shortcut icon" href="theme/defaut/img/favicon.ico">
                <style>
                    body {
                        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                        /*font-size: 14px;
                        line-height: 1.42857143;*/
                        color: #c8c8c8;
                        background-color: #272b30;
                    }
                </style>
            </head>
            <body>
                <h1>MyVOD</h1>
                <p><?=$message;?></p>
            </body>
        </html>
        <?php
        exit();
    
    
}






//pour test
//echo 'smb://192.168.1.24/MyCinema/Minuscl.1x29-Bouse.au.carré.FR.DvdRip.avi' . "\n";

