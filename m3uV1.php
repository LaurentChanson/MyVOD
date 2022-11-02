<?php

//http://fr.wikipedia.org/wiki/M3U
//https://github.com/ChoiZ/php-playlist-generator/blob/master/generate.php

require './config.php';

//cache
require_once 'lib/data/db-cache.php';

require_once 'lib/os.php';

$nom_fichier = urldecode($_GET['file']);

header("Content-Type: audio/x-mpegurl; charset=utf-8");
// le nom du fichier par défaut et propose telechargement m3u ,pour windows
if (OS_is_windows()) {
    header('Content-Disposition: attachment; filename="' . $nom_fichier . '.m3u"');
} else {
    header(' filename="' . $nom_fichier . '.m3u"');
}


//efface la 1ere ligne vide
ob_clean();



//récup des données du cache  
$file_info = new FileInfos();

cache_db_ouvrir();
cache_db_lire($nom_fichier, $file_info);
cache_db_fermer();


if (strtolower(substr($file_info->full_path, -4)) == '.wpl') {
    //le wpl est window media playlist
    //on récupère le contenu
    //et le transforme en m3u
    $tfichiers = wpl_get_liste_fichiers($file_info->full_path);

    $dossier = dirname($file_info->full_path);


    $url = '';
    foreach ($tfichiers as $f) {

        $f = path_local_to_distant($dossier . '/' . $f);

        $url = $url . (strlen($url) > 0 ? "\n" : '') . $f;
    }
} else {
    //autre cas on met le fichier
    $url = path_local_to_distant($file_info->full_path);
}





echo($url);

//fin du traitement





function path_local_to_distant($fichier) {
    global $repertoireMyCinema;
    global $aliasMyCinemaTablette;

	if(nav_is_local()){
		$url=$fichier;
		if (OS_is_windows()) {
			$url=str_replace('/','\\',$url);
		} 
		
	}else{
		$url = $aliasMyCinemaTablette . (str_replace($repertoireMyCinema, '', $fichier));
		$url = utf8_encode(str_ireplace('%2F', '/', $url));
		
		
		if (!OS_is_windows()) {
			$url = 'smb:' . $url;
		}
	}
	
    return $url;
}

function wpl_get_liste_fichiers($fichier_wpl) {


    $xmlstr = file_get_contents($fichier_wpl);

    $movies = new SimpleXMLElement($xmlstr);

    $t = array();

    foreach ($movies->body->seq->media as $m) {
        array_push($t, '' . $m['src']);
    }

    return $t;
}
