<?php

$size_sur_64_bits = true;   //mappe la fonction php "filesize" pour gérer les fichier de + de 4Go
                            //sera modifié plus tard en fct des paramètres
function is_php7(){
    if (PHP_MAJOR_VERSION>=7){
        return true;
    }
    return false;
}

class fichier_info {

    public $nom;
    public $path;
    public $taille;

}

function filesize_php7($fichier) {
    $size_sur_64_bits=false;
    if(isset($_SESSION['taille_fichiers_64_bits'])){
        $size_sur_64_bits=$_SESSION['taille_fichiers_64_bits'];
    }
    if (file_exists($fichier) == false) {
        return '';
    }
    

    if ($size_sur_64_bits) {
        //cette méthode est beaucoup + long
        $taille = filesize_64(utf8_decode($fichier));
        /*$taille = filesize_64('C:\wamp\www\MyCinema\on.léappelle.jeeg.robot.french.bdrip.mkv');
        var_dump($taille);
        exit();*/
        
    } else {
 
        $taille = filesize($fichier);
        //pour les fichiers de + de 2Go, $taille prend une valeur négative
        //car il est stocké dans un entier signé 32bits.
        //var_dump($taille);
        if ($taille < 0) {
            //$taille = filesize($fichier);
            $taille = sprintf("%u", $taille);
        }
    }
    return $taille;
    
}





function filesize_utf8($fichier) {
    if(is_php7()){
        return filesize_php7($fichier);
    }
    $size_sur_64_bits=false;
    if(isset($_SESSION['taille_fichiers_64_bits'])){
        $size_sur_64_bits=$_SESSION['taille_fichiers_64_bits'];
    }
    
    //var_dump($size_sur_64_bits);
    
    //test si le fichier n'existe pas
    if (file_exists_utf8($fichier) == false) {
        return '';
    }
    
    if ($size_sur_64_bits) {
        //cette méthode est beaucoup + long
        $taille = filesize_64(utf8_decode($fichier));
    } else {
        $taille = filesize(utf8_decode($fichier));
        //pour les fichiers de + de 2Go, $taille prend une valeur négative
        //car il est stocké dans un entier signé 32bits.
        //var_dump($taille);
        if ($taille < 0) {
            $taille = filesize(utf8_decode($fichier));
            $taille = sprintf("%u", $taille);
        }
        //end();
    }

    return $taille;
}

//http://php.net/manual/fr/function.filesize.php
function filesize_64($file) {
//var_dump($file);
    //return 0;
    if (is_dir($file)) {
        return 0;
    }
    if (!(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')) {
        $size = trim(`stat -c%s $file`);
    } else {
        $fsobj = new COM("Scripting.FileSystemObject");
        $f = $fsobj->GetFile($file);
        $size = $f->Size;
    }
    return $size;

    /*
      //Fonctionne mais plus lent
     * 
      if(substr(PHP_OS, 0, 3) == "WIN")
      {
      exec('for %I in ("'.$file.'") do @echo %~zI', $output);
      $return = $output[0];
      }
      else
      {
      $return = filesize($file);
      }
      return $return;

     */
}

//https://stackoverflow.com/questions/3964793/php-case-insensitive-version-of-file-exists
function fileExists($fileName, $caseSensitive = true) {

    if(file_exists($fileName)) {
        return $fileName;
    }
    if($caseSensitive) return false;

    // Handle case insensitive requests            
    $directoryName = dirname($fileName);
    $fileArray = glob($directoryName . '/*', GLOB_NOSORT);
    $fileNameLowerCase = strtolower($fileName);
    foreach($fileArray as $file) {
        if(strtolower($file) == $fileNameLowerCase) {
            return $file;
        }
    }
    return false;
}

function file_exists_utf8($file_path, $caseSensitive = true) {
    // var_dump($file_path);

    return fileExists(utf8_decode($file_path), $caseSensitive);
}

function create_dir_if_not_exists($rep_path) {
    if (!file_exists($rep_path)) {
        mkdir($rep_path, 777, true);
    }
}

function path_equals($path1, $path2) {
    $p1 = mb_convert_case(($path1), MB_CASE_LOWER, "UTF-8");
    $p2 = mb_convert_case(($path2), MB_CASE_LOWER, "UTF-8");
    $p1 = str_ireplace("\\", "/", $p1);
    $p2 = str_ireplace("\\", "/", $p2);
    return $p1 == $p2;
}

function taille_fichier_en_texte($taille) {

    // Conversion en Go, Mo, Ko
    if ($taille >= 1099511627776) {
        $taille = round($taille / 1099511627776 * 100) / 100 . " To";
    } elseif ($taille >= 1073741824) {
        $taille = round($taille / 1073741824 * 100) / 100 . " Go";
    } elseif ($taille >= 1048576) {
        $taille = round($taille / 1048576 * 100) / 100 . " Mo";
    } elseif ($taille >= 1024) {
        $taille = round($taille / 1024 * 100) / 100 . " Ko";
    } else {
        $taille = $taille . " o";
    }
    if ($taille == 0) {
        $taille = "-";
    }
    return $taille;
}

function get_extension($filename) {
    $extension = strrchr($filename, '.');
    return $extension;
}

function recherche_fichiers_avec_fonction_de_rappel($chemin_repertoire, $callable_function_avec_2_parametres, $filtre_extensions = array('.wpl', '.avi', '.mpeg', '.mpg', '.mov', '.mp4', '.mkv', '.wmv', '.iso', '.nrg', '.m2ts', '.dvd', '.bluray')) {
    //var_dump(is_php7());
    
    $dossier = opendir($chemin_repertoire);  // on ouvre le repertoire
    //var_dump($dossier);
    if (!$dossier) {
        return ''; //oups , impossible d'ouvrir le repertoire
    }
    while ($entree = readdir($dossier)) {
        
        
        
        
        if ($entree == "." || $entree == ".." || $entree == "#recycle") { // on ne regarde pas . ( lien vers le dossier courant ) et .. ( lien vers le dossier parent )
            continue;
        }
        $cheminEntree = $chemin_repertoire . DIRECTORY_SEPARATOR . $entree;


        if (is_dir($cheminEntree)) { // on est sur un repertoire
            $video_ts = $cheminEntree . '/video_ts';

            if (file_exists_utf8($video_ts,false)) {
                // on est dans un répertoire qui contient 'video_ts'
                //c'est la structure typique d'un DVD
                //var_dump("ici ".$video_ts.", '".$entree."'");
                //exit();
                if(is_php7()){
                    call_user_func($callable_function_avec_2_parametres, $entree, $video_ts);
                }else{
                    call_user_func($callable_function_avec_2_parametres, utf8_encode($entree), utf8_encode($video_ts));
                }
            } else {
                //si ce n'est pas une structure 'DVD', on regarde ce qu'il y a dedans
                recherche_fichiers_avec_fonction_de_rappel($cheminEntree, $callable_function_avec_2_parametres); //on va voir ce que cache ce repertoire
            }
        } else {
            //on a trouvé le fichier
            $ext = get_extension(strtolower($entree));
            if (in_array($ext, $filtre_extensions)) {
                if(is_php7()){
                    call_user_func($callable_function_avec_2_parametres, $entree, $cheminEntree);
                }else{
                    call_user_func($callable_function_avec_2_parametres, utf8_encode($entree), utf8_encode($cheminEntree));
                }
                
            }
        }
    }
    return '';
}

function utf8_decode_si_php5($s){
    if(!is_php7()){return utf8_decode($s);}
    return $s;
}

function utf8_encode_si_php5($s){
    if(!is_php7()){return utf8_encode($s);}
    return $s;
}

function is_dir_utf8($r){
    return is_dir( utf8_decode($r));
}
function recherche_et_retourne_chemin_complet($chemin_repertoire, $filname) {

    
    //var_dump($chemin_repertoire);
    
    //$filname = mb_strtolower($filname, 'UTF-8');
    
    //var_dump($chemin_repertoire);
    
    $dossier = opendir(utf8_decode($chemin_repertoire));  // on ouvre le repertoire
    if (!$dossier) {
        return ''; //oups , impossible d'ouvrir le repertoire
    }
    

    
    while ($entree = utf8_encode_si_php5(readdir($dossier))) {
        //$entree = mb_strtolower($entree, 'UTF-8');
        
        //var_dump($entree.'   '.$filname);



        if ($entree == "." || $entree == "..") { // on ne regarde pas . ( lien vers le dossier courant ) et .. ( lien vers le dossier parent )
            continue;
        }
        $cheminEntree = $chemin_repertoire . '/' . $entree;
//var_dump($cheminEntree.'    '. is_dir_utf8($cheminEntree));
        if (is_dir_utf8($cheminEntree)) { // on est sur un repertoire
            $video_ts = $cheminEntree . '/video_ts';

            if (file_exists_utf8($video_ts) && ($entree === $filname)) {
                return($video_ts);
            }

            $s = recherche_et_retourne_chemin_complet($cheminEntree, $filname); //on va voir ce que cache ce repertoire
            if (strlen($s) > 0) {
                return $s;
            }
        } else {

            //var_dump($filname.' '. utf8_encode( $entree));
            // Traitement du chemin d'accès
            //utf8_encode
            //var_dump($filname.' '. $entree);
            //if (($entree) === $filname) {
            if(strcasecmp($entree,$filname)==0){
                //on a trouvé le fichier
                return $cheminEntree;
            }
        }
    }
    return '';
}
