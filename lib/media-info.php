<?php

class media_info {

    public $width;
    public $height;
    public $duration_sec;

    public function media_info_fichier($fichier) {
        $fichier = str_replace('/', '\\', utf8_decode($fichier));
        $cmd = realpath(".");
        if (substr($cmd, -3) != 'lib') {
            $cmd.='\\lib';
        }
        $cmd = $cmd . '\media_info\MediaInfo.exe "' . $fichier . '" "--Inform=Video;%Width% %Height% %Duration%';

        $output = shell_exec($cmd);
        //enleve le retour chariot
        $output = str_replace("\n", '', $output);
        //parse les résultats
        $t = explode(' ', $output);
       
        if (count($t) == 3) {
            $this->width = $t[0];
            $this->height = $t[1];
            $this->duration_sec = $t[2] / 1000;
            return true;
        }
        //var_dump($info);
        return false;
    }

    public static function get_media_info_HTML($fichier) {

        $fichier = str_replace('/', '\\', utf8_decode($fichier));
        $cmd = realpath(".");
        if (substr($cmd, -3) != 'lib') {
            $cmd.='\\lib';
        }
        $cmd = $cmd . '\media_info\MediaInfo.exe "' . $fichier . '" --output=HTML';
        $output = shell_exec($cmd);

        //récupère que la partie body
        preg_match('`<body[^>]*>(.*)</body[^>]*>`isU', $output, $matches);

        $body = '';
        if (isset($matches[1])) {
            $body .= $matches[1];
        }
        //enleve le style de la table
        $body = str_replace('style="border:1px solid Navy"', '', $body);
        //met le td width en pourcentage
        $body = str_replace('<td width="150">', '<td width="28%">', $body);

        return $body;
    }

}

//exemple d'utilisation :
//$fichier="C:\wamp\www\MyCinema\Nouveau dossier\Qui veut la peau de Roger Rabbit French DVDrip.avi";
//$fichier="C:\wamp\www\MyCinema\Nouveau dossier\Siderman 3.avi";
//echo(media_info_HTML($fichier));

/*
 //exemple pour récupérer la largeur
MediaInfo.exe "C:\wamp\www\MyCinema\aa 720P.avi" --Inform=Video;%Width%
 //idem pour la hauteur
MediaInfo.exe "C:\wamp\www\MyCinema\aa 720P.avi" --Inform=Video;%Height%
 //Durée
MediaInfo.exe "C:\wamp\www\MyCinema\aa 720P.avi" --Inform=General;%Duration%
 */