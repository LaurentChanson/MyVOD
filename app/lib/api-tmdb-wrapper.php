<?php

define("tmdb_key",config::tmdb_api_key());

require_once 'tmdb/TMDb.php';
require_once 'webrecherche.php';



//documentation sur
//https://github.com/glamorous/TMDb-PHP-API
//et
//https://developers.themoviedb.org/3/search/search-tv-shows


class TMDBWrapper{
    
    static $tmdb;
    
    /*
    public static function GetIDFromURL($url) {
        //TODO
        //exemple : https://www.themoviedb.org/movie/8810-mad-max-2?language=fr
        //on veut 8810
        
        return false;
    }
    */
    public static function GetHRefFromId($id, $is_serie=false){
        $type = $is_serie ? 'tv' : 'movie';
        $id_tmdb = $id;
        //enleve le 's' si c'est une série (le 's' permet de distinguer des séris des films)
        if(substr($id, 0,1) == 's'){
            $id_tmdb=substr($id, 1);
        }
        return 'https://www.themoviedb.org/'.$type.'/'.$id_tmdb.'?language=fr';
    }
    
    public static function get_poster_path_Url($poster_path){
        self::instancier_tmdb_si_pas_instancie();
        $image_url = self::$tmdb->getImageUrl($poster_path, TMDb::IMAGE_POSTER, 'original');
        return $image_url;
    }
    
    
    
    /**
     * 
     * @param type $motsCles mot cle à rechercher
     * @param type $error_retour string si erreur ou pas de résultat
     * @return l'objet films ou faux si rien trouvé
     */
    public static function Rechercher($motsCles, &$error_retour) {
        
        
        try {
            $error_retour=self::instancier_tmdb_si_pas_instancie();
           // var_dump($error_retour);
            if(strlen($error_retour)>0)return FALSE;
            
            
            $movies = (object)self::$tmdb->searchMovie($motsCles);
            $t = array();
            

            
            
            //parcours des résultats pour les films
            foreach ($movies->results as $res) {
                $f = new WebRechercheData();
                $res=(object)$res;
                
                //$image_url = self::$tmdb->getImageUrl($res->poster_path, TMDb::IMAGE_POSTER, 'original');
                $image_url = self::get_poster_path_Url($res->poster_path);
                $res->poster_url=$image_url;
                
                $f->init_from_result_tmdb_dot_org($res);
                
                array_push($t, $f);
            }
            
            

            //var_dump($movies);
            //var_dump($t);
            //exit();
            
            //tests pour les séries
            $series = (object)self::$tmdb->searchTV($motsCles);
        
            //parcours des résultats pour les séries
            foreach ($series->results as $res) {
                $f = new WebRechercheData();
                $res=(object)$res;
                
                $image_url = self::get_poster_path_Url($res->poster_path);
                $res->poster_url=$image_url;
                
                $f->init_from_result_series_tmdb_dot_org($res);
                
                array_push($t, $f);
            }
            
            
            //var_dump($series);
            //var_dump($t);
            //exit();
            
            if ( count($movies->results) + count($series->results) == 0)  {
                $error_retour = "Pas de résultat pour ".'"'.$motsCles.'"';
            }
            
            
            return $t; 
            
            
        }
        catch (ErrorException $e) {
            // Affichage des informations sur la requête
            //echo "<pre>", print_r($allohelper->getRequestInfos(), 1), "</pre>";
            // Afficher un message d'erreur.
            $error_retour = "Erreur " . $e->getCode() . ": " . $e->getMessage();
        }
        
        
        return FALSE;
        
        
    }
    
    public static function GetFilmOrSerie($code, &$error_retour) {
        $error_retour = "";
        try {
            $error_retour=self::instancier_tmdb_si_pas_instancie();
            
            if(strlen($error_retour)>0)return FALSE;
            
            //Utilise l'API partie Série
            if(substr($code,0,1)=='s'){
                $code_tmdb=substr($code,1);
                $tv_result = (object)self::$tmdb->getTV($code_tmdb);
                
                $image_url = self::get_poster_path_Url($tv_result->poster_path);
                $tv_result->poster_url=$image_url;
            
                //les employés
                $c= (object)self::$tmdb->getTVCredit($code_tmdb);
                //var_dump($c);
            
                // movie trailer
                $t=(object)self::$tmdb->getTVVideos($code_tmdb, 'fr-FR');
                //var_dump($t);

                $film=new WebGetFilmData;
                //var_dump($tv_result);
                
                $film->init_from_result_tmdb_serie($tv_result,$c, $t);

                //var_dump($film);
                //exit();
                return $film;
                
            }
            
            
            
            
            //Utilise l'API partie Film
            //Get Movie with other return format than the default and with an IMDb-id
            $movie_result = (object)self::$tmdb->getMovie($code);
            //var_dump($movie_result);            
            $image_url = self::get_poster_path_Url($movie_result->poster_path);
            $movie_result->poster_url=$image_url;
            //var_dump($movie_result);

            $c= (object)self::$tmdb->getMovieCast($code);
            //var_dump($c);
            
            
            $t=(object)self::$tmdb->getMovieTrailers($code, 'fr-FR');
            //var_dump($t->youtube);
            
            $media=array();
            //on classe les médias dans l'ordre alfabetique
            foreach ($t->youtube as $m){
                $media[$m['name']] = $m;
            }
            ksort($media);
            //var_dump($media);
            
         
            //
            //
            //ksort
            //$media
            
            
            //$k=(object)self::$tmdb->getMovieKeywords($code, 'fr');
            //var_dump($k);
            //c'est en anglais bof
            
            //Get image URL for the backdrop image in its original size
            //$image_url = $tmdb->getImageUrl($filepath, TMDb::IMAGE_POSTER, 'original');
	
            //var_dump($image_url);
            
            
            //$o=(object)self::$tmdb->getMovieTranslations($code);
            //var_dump($o);
            
            $film=new WebGetFilmData;
            
            $film->init_from_result_tmdb($movie_result,$c,$media);
            
            //var_dump($film);
            return $film;
            
            //exit();
            
        }
        catch (ErrorException $e) {
            // Affichage des informations sur la requête
            //echo "<pre>", print_r($allohelper->getRequestInfos(), 1), "</pre>";
            // Afficher un message d'erreur.
            $error_retour = "Erreur " . $e->getCode() . ": " . $e->getMessage();
        }
        
        
        return FALSE;
        
    
    }
    
     /**
     * instancie le membre static allohelper
     */
    private static function instancier_tmdb_si_pas_instancie() {
        try {

            if (!isset(self::$tmdb) || (self::$tmdb == null)) {
                // Créer un objet $tmdb.

                self::$tmdb = new TMDb(tmdb_key, 'fr-FR', TRUE);
                $token = self::$tmdb->getAuthToken();
                //on verra plus tard pour le token
                //peut être pour rediriger l'utilisateur avec identification
                //var_dump($token);
                //exit();

            }
        
        
        } catch (Exception $ex) {
            self::$tmdb = null;
            return("Mauvaise clé TMDB");
        }
        
        return '';
        
    }
    
}