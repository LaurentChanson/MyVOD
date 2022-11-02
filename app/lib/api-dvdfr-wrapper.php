<?php

header('Content-Type: text/html; charset=utf-8');

//pour le header aléatoire
require_once 'allocine/AlloHelper.class.php';
//les objets WebRechercheData
require_once 'webrecherche.php';

//documentation sur
//http://www.dvdfr.com/api/documentation.php
//les requêtes doivent être en ISO-8859-1

/* si erreur, ca sera de la forme
  <errors>
  <error type="[fatal|warning]">
  <code>CODE_ERREUR</code>
  <message>Message d'erreur</message>
  </error>
  </errors>
 */

//7274

//var_dump(DVDFRAPIWrapper::GetFilm(7274, $error_retour));

class DVDFRAPIWrapper {

    public static function GetIDFromURL($url){
        //exemple : http://www.dvdfr.com/dvd/f7274-qui-veut-la-peau-de-roger-rabbit.html
        $dvdfrf='https://www.dvdfr.com/dvd/f';
        //var_dump($dvdfrf);
        if (substr($url, 0, strlen($dvdfrf)) == $dvdfrf) {
            //on enleve la partie de gauche
            $url=  str_replace($dvdfrf, '', $url);
            $pos=strpos($url, '-');
            return(substr($url, 0, $pos));
            
        }
        return false;
        
    }
    
    
    
    public static function GetQuotasRestant() {
        
        $url='https://www.dvdfr.com/api/quota.php?xml';
        $xmlstr = self::get_result_xml($url);
        //var_dump($xmlstr);
        $q = new SimpleXMLElement($xmlstr);
        $used=''.$q->fetchs->used;
        $max=''.$q->fetchs->maximum;
        return $max-$used;
    }
    
    public static function GetFilm($code, &$error_retour) {

        $error_retour = "";

        $url = 'https://www.dvdfr.com/api/dvd.php' . '?id="' . urlencode($code) . '"';

        $xmlstr = self::get_result_xml($url);
        
        $fiche = new SimpleXMLElement($xmlstr);
        
        $film=new WebGetFilmData;
        $film->init_from_result_dvdfr($fiche);
        //var_dump($fiche);
        
        //var_dump($fiche->bandesAnnonces);
        
        
        return $film;
        
    }
    
    
    

    static function Rechercher($motsCles, &$error_retour) {
        $error_retour = "";

        $url = 'https://www.dvdfr.com/api/search.php' . '?title="' . urlencode($motsCles) . '"';

        $xmlstr = self::get_result_xml($url);

        
        
        
        //parsage de l'objet XML
        $movies = new SimpleXMLElement($xmlstr);
        //var_dump($movies);
//        var_dump($movies->dvd[0]);
//        
//        $e=$movies->dvd[0]->edition;
//        var_dump($e);
        
        $t = array();
        if (count($movies->dvd) == 0) {
            $error_retour = "Pas de résultat pour ".'"'.$motsCles.'"';
        }


        foreach ($movies->dvd as $res) {
            $f = new WebRechercheData();
            $f->init_from_result_dvdfr($res);
            
          
            
            
            array_push($t, $f);
        }
        return $t;
    }

    
    
    private static function get_result_xml($url) {

        $curl = curl_init();
        $UserAgent = AlloHelper::getRandomUserAgent();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => $UserAgent,
            CURLOPT_SSL_VERIFYPEER => false,    //sinon erreur certificat ssl car dvdfr est passé en https
        ));

        // Send the request & save response to $resp
        $xmlstr = curl_exec($curl);
        
        
        // Check the return value of curl_exec(), too
        if ($xmlstr === false) {
            throw new Exception(curl_error($curl), curl_errno($curl));
        }
        
        // Close request to clear up some resources
        curl_close($curl);

        return $xmlstr;
    }
    
    
}
