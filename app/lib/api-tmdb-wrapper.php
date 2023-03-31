<?php
require_once 'tmdb/TMDb.php';
require_once 'tmdb/api-tmdb-key-config.php';
// dedans mettre : define("tmdb_key",'your key');

require_once 'webrecherche.php';



//documentation sur
//https://github.com/glamorous/TMDb-PHP-API
//et
//https://developers.themoviedb.org/3/search/search-tv-shows


class TMDBWrapper{
    
    static $tmdb;
    
    
    public static function GetIDFromURL($url) {
        //TODO
        //exemple : https://www.themoviedb.org/movie/8810-mad-max-2?language=fr
        //on veut 8810
        
        return false;
    }
    
    public static function GetHRefFromId($id){
        
        return 'https://www.themoviedb.org/movie/'.$id.'?language=fr';
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
            self::instancier_tmdb_si_pas_instancie();

            $movies = (object)self::$tmdb->searchMovie($motsCles);
            $t = array();
            
            if (count($movies->results) == 0) {
                $error_retour = "Pas de résultat pour ".'"'.$motsCles.'"';
            }
            
            
            //parcourt des résultats
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
    
    public static function GetFilm($code, &$error_retour) {
        $error_retour = "";
        try {
            self::instancier_tmdb_si_pas_instancie();
            
            //Get Movie with other return format than the default and with an IMDb-id
            $movie_result = (object)self::$tmdb->getMovie($code);
                            
            $image_url = self::get_poster_path_Url($movie_result->poster_path);
            $movie_result->poster_url=$image_url;
            var_dump($movie_result);

            $c= (object)self::$tmdb->getMovieCast($code);
            var_dump($c);
            
            
            $i=(object)self::$tmdb->getMovieTrailers($code, 'fr');
            var_dump($i);
            
            //$k=(object)self::$tmdb->getMovieKeywords($code, 'fr');
            //var_dump($k);
            //c'est en anglais bof
            
            //Get image URL for the backdrop image in its original size
            //$image_url = $tmdb->getImageUrl($filepath, TMDb::IMAGE_POSTER, 'original');
	
            //var_dump($image_url);
            
            
            $o=(object)self::$tmdb->getMovieTranslations($code);
            var_dump($o);
            
            $film=new WebGetFilmData;
            
            $film->init_from_result_tmdb($movie_result,$c);
            
            var_dump($film);
            
            
            exit();
            
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

        if (!isset(self::$tmdb)) {
            // Créer un objet $tmdb.
            
            self::$tmdb = new TMDb(tmdb_key, 'fr', TRUE);
            $token = self::$tmdb->getAuthToken();
            //on verra plus tard pour le token
            //peut être pour rediriger l'utilisateur avec identification
            //var_dump($token);
            //exit();
            
        }
    }
    
}