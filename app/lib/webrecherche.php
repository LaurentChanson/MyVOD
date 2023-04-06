<?php

require_once 'api-allocine-wrapper.php';
require_once 'api-dvdfr-wrapper.php';
require_once 'api-tmdb-wrapper.php';
require_once 'countries.iso-3166-1.inc.php';



$error_retour = "";

//var_dump(WebRecherche::Rechercher('roger rabbit',type_recherche::RECHERCHE_DVDFR , $error_retour));
//var_dump($error_retour);


class type_recherche {

    const RECHERCHE_ALLOCINE = 0;
    const RECHERCHE_DVDFR = 1;
    const RECHERCHE_TMDB = 2;
    
    public $numero;

    //constructeur avec le numéro de type recherche
    public function __construct($numero) {
        $this->numero = $numero;
    }

    //retourne la chaine correspondant au type de recherche
    public function get_site() {
        switch ($this->numero) {
            case type_recherche::RECHERCHE_DVDFR:
                return 'DvdFr.com';
                break;
            case type_recherche::RECHERCHE_TMDB:
                return 'TheMoviedb.org';
                break;
            default:
                return 'Allociné.fr';
                break;
        }
    }

}

class WebRecherche {
    //fait une recharche par rapport aux mots cles
    public static function Rechercher($motsCles, $type_recherche, &$error_retour) {
        
        switch ($type_recherche) {
            case type_recherche::RECHERCHE_DVDFR:
                return DVDFRAPIWrapper::Rechercher($motsCles, $error_retour);
                break;
            case type_recherche::RECHERCHE_TMDB:
                return TMDBWrapper::Rechercher($motsCles, $error_retour);
                break;
            default:
                return AllocineAPIWrapper::Rechercher($motsCles, $error_retour);
                break;
        }
    }
    //récupère la fiche du film via son code
    public static function GetFilm($code, $type_recherche, &$error_retour) {

        switch ($type_recherche) {
            case type_recherche::RECHERCHE_DVDFR:
                return DVDFRAPIWrapper::GetFilm($code, $error_retour);
                break;
            case type_recherche::RECHERCHE_TMDB:
                return TMDBWrapper::GetFilm($code, $error_retour);
                break;
            default:
                return AllocineAPIWrapper::GetFilm($code, $error_retour);
                break;
        }
    }

}

class WebRechercheData {

    public $code;
    public $originalTitle;
    public $title;
    public $productionYear;
    public $releaseDate;
    public $directors;
    public $actors;
    public $pressRating;
    public $userRating;
    public $href;
    public $posterURL;
    public $poster;
    public $resume;
    
    public function __construct() {
        //exemple sur : http://www.santerref.com/blogue/2011/07/21/constructeurs-multiples-en-php/
        $ctp = func_num_args();
        $args = func_get_args();
        switch ($ctp) {
            case 1:
                //FromArrayAllocineRechercher($args[0]); 
                //var_dump($args[0]);
                $this->FromArrayAllocineRechercher($args[0]);
                break;
        }
    }

    public function FromArrayAllocineRechercher($t) {
        
        //var_dump($t);
        
        
        $this->code = $t['code'];
        $this->originalTitle = $t['originalTitle'];
        $this->title = $t['title'];
        $this->productionYear = $t['productionYear'];
        $this->releaseDate = $t['release']['releaseDate'];
        $this->directors = $t['castingShort']['directors'];
        $this->actors = $t['castingShort']['actors'];
        $this->pressRating = $t['statistics']['pressRating'];
        $this->userRating = $t['statistics']['userRating'];
        if(isset($t['link'][0]['href'])){
            $this->href = $t['link'][0]['href'];
        }
        $this->posterURL = $t['posterURL'];
        $this->poster = $t['poster'];
    }

    
    
    public function init_from_result_tmdb_dot_org($res) {
        //var_dump($res);
        $this->code = '' . $res->id;
        $this->title = '' . $res->title;
        
        $this->releaseDate = ''. $res->release_date;
        $this->productionYear = substr($this->releaseDate,0,4);
        $this->originalTitle = $res->original_title;
        
        $this->userRating = $res->vote_average;
        
        $this->resume = $res->overview;
        //limitation
        $this->resume=substr($this->resume,0,80);
        
        
        $this->poster = $res->poster_path;
        $this->posterURL = $res->poster_url;
        
        //$this->href = 'https://www.themoviedb.org/movie/'.$this->code.'?language=fr';
        $this->href=TMDBWrapper::GetHRefFromId($this->code);
        //var_dump($this);
        
    }
    
    
    public function init_from_result_dvdfr($res) {
        $this->code = '' . $res->id;
        $edition = '' . $res->edition;
        if ($edition != '')
            $edition = ' ' . $edition;
        $this->title = '' . $res->titres->fr . ' (' . $res->media . $edition . ')';
        if (isset($res->titres->vo) && '' . $res->titres->vo != '') {
            $this->originalTitle = '' . $res->titres->vo;
        } else {
            $this->originalTitle = $this->title;
        }

        $this->productionYear = '' . $res->annee;
        $this->posterURL = '' . $res->cover;
        $this->directors = '' . $res->stars->star;
    }

}



class WebGetFilmData {

    public $code_allocine;
    public $code_dvdfr;
    public $code_tmdb;
    public $movieType;
    public $originalTitle;
    public $title;
    public $keywords;
    public $productionYear;
    public $nationality;    //attention , il peut en avoir plusieurs (allociné :: pour l'instant c'est séparé par une virgule)
    public $genre;          //attention , il peut en avoir plusieurs (allociné)
    public $genres = array();
    public $releaseDate;
    public $synopsis;
    public $synopsisShort;
    public $directors;
    public $actors;
    public $poster;
    public $trailer_code;
    public $trailer_href;
    public $trailer_embed;
    public $pressRating;
    public $userRating;
    public $href;
    public $posterURL;
    public $runtime;
    public $publicType;
    public $bandes_annonces = array();
    
    
    public function duree_en_heure_minutes() {
        $total_secondes = $this->runtime;

        $h = (int) ($total_secondes / 3600);
        $m = (int) ($total_secondes / 60) % 60;
        $s = $total_secondes % 36;
        return $h . 'h' . sprintf('%02d', $m) . 'min';
    }

    public function __construct() {
        //exemple sur : http://www.santerref.com/blogue/2011/07/21/constructeurs-multiples-en-php/
        $ctp = func_num_args();
        $args = func_get_args();
        switch ($ctp) {
            case 1:
                //FromArrayAllocineRechercher($args[0]); 
                //var_dump($args[0]);
                $this->FromArrayAllocineGetFilm($args[0]);
                break;
        }
    }

    public function init_from_result_tmdb($res,$casts,$medias){
        $this->code_tmdb =  '' . $res->id;
        $this->title = '' . $res->title;
        $this->originalTitle = $res->original_title;

        $this->keywords = $this->title;
        $this->releaseDate = '' . $res->release_date;
        $this->productionYear = substr($this->releaseDate,0,4); ;
        $this->movieType = 'Long-métrage';

        $this->synopsis = $res->overview;
        $this->synopsisShort = $res->tagline;
        if(strlen($this->synopsisShort)==0)$this->synopsisShort  = $this->synopsis;

        $this->runtime =  $res->runtime * 60;
        $this->href = 'https://www.themoviedb.org/movie/'.$this->code_tmdb.'?language=fr';

        $this->userRating = $res->vote_average / 2; //car dans les fiches, on est sur 5
       
        //les genres
        foreach ($res->genres as $g) {
             array_push($this->genres, $g['name']);
        }
        if(count($this->genres)>0)$this->genre = $this->genres[0];

        //nationalité : on concatène la liste
        $s = "";
        if(isset($res->production_countries)){
            $countries = countries();
            foreach ($res->production_countries as $n) {
                $pays = $countries[$n['iso_3166_1']];

                $s = $s . (strlen($s) == 0 ? '' : ', ') . $pays;
            }        
        }
        $this->nationality = $s;

        //les acteurs
        $nb_acteurs_maxi=16; //limite à 15 acteurs maxi
        $s = "";
        $nb=1;
        foreach ($casts->cast as $acteur) {
            //var_dump($acteur);
            if($acteur['known_for_department']=='Acting' ){
                if($nb<=$nb_acteurs_maxi){
                    $s = $s . (strlen($s) == 0 ? '' : ', ') . $acteur['name'];
                    if ( strlen($acteur['character'])>0) {
                        $s = $s . ' ('.$acteur['character'].')';
                    }
                    //$s = $s .','.$nb;
                }
               $nb++;
            }
        }
        if($nb>$nb_acteurs_maxi) $s.="...";
        $this->actors = $s;
        
        //var_dump($s);
        //exit();
        //réalisateurs
        $s = "";
        foreach ($casts->crew as $crew) {
            if($crew['job']=='Director' ){
               $s = $s . (strlen($s) == 0 ? '' : ', ') . $crew['name']; 
            }
        }
        $this->directors = $s;

        //Le poster
        $this->href=TMDBWrapper::GetHRefFromId($this->code_tmdb);
        $this->poster = $res->poster_path;
        $this->posterURL = $res->poster_url;      

        //les médias (bandes annonces)
        foreach ($medias as $media){
            $media=(object)$media;
            
            //var_dump($media);
            /* exemple : 
            object(stdClass)[61]
  public 'name' => string 'Bruce tout-puissant (2003) | Bande-annonce VF (HD | 1080p)' (length=58)
  public 'size' => string 'HD' (length=2)
  public 'source' => string 'l5eK3q4JXZo' (length=11)
  public 'type' => string 'Trailer' (length=7)*/
            
            $ba=new BandeAnnonce();
            $ba->filename=$this->title;
            $ba->code=$media->source;
            $ba->titre=$media->name;
            if(strpos($ba->titre,'VF'))$ba->langue='VF';
            if(strpos($ba->titre,'VOSTF'))$ba->langue='VOSTF';
            if(strpos($ba->titre,'VOST'))$ba->langue='VOST';
            
            $ba->type=$media->type;
            if($ba->type=='Trailer')$ba->type='Bande-annonce';
            
            $ba->url="https://www.youtube.com/watch?feature=player_embedded&v=".$ba->code;
            
            //langue à définir (VF,VO...)
            
            //ajout de la bande annonce dans le tableau
            array_push($this->bandes_annonces, $ba);
            
            //var_dump($media);
            //var_dump($ba);
        }
       
       //exit();
       
    }
    
    
    
    public function init_from_result_dvdfr($res) {
        $this->code_dvdfr = '' . $res->id;


        $this->title = '' . $res->titres->fr;
        if (isset($res->titres->vo) && '' . $res->titres->vo != '') {
            $this->originalTitle = '' . $res->titres->vo;
        } else {
            $this->originalTitle = $this->title;
        }

        $this->keywords = $this->title;

        $this->productionYear = '' . $res->annee;
        $this->nationality = '' . $res->sortie;

        $this->releaseDate = '' . $res->sortie;
        $this->synopsis = '' . $res->synopsis;
        $this->synopsisShort = '' . $res->synopsis;

        $this->runtime = '' . $res->duree * 60;

        $this->pressRating = '' . $res->critiques->dvdfr / 2;
        $this->userRating = '' . $res->critiques->public / 2;

        foreach ($res->categories->categorie as $genre) {
            array_push($this->genres, '' . $genre);
        }
        $this->genre = $this->genres[0];

        $this->href = '' . $res->url;
        $this->posterURL = '' . $res->cover;
        $this->publicType = '' . $res->rating;
        $edition = '' . $res->edition;
        if ($edition != '')
            $edition = ' ' . $edition;


        $this->movieType = '' . $res->media . $edition;
        $i = 0;
        $this->actors = array();
        foreach ($res->stars->star as $star) {
            if ($i == 0) {
                $this->directors = '' . $star;
            } else {
                array_push($this->actors, '' . $star);
            }
            $i++;
        }
        $this->actors = join(", ", $this->actors);


        $this->nationality = array();
        foreach ($res->listePays->pays as $pays) {
            array_push($this->nationality, '' . $pays);
        }
        $this->nationality = join(", ", $this->nationality);

        //echo('<hr>');
        if(isset($res->bandesAnnonces) && isset($res->bandesAnnonces->bandeAnnonce))
            
        $tlangues=array();
        
        foreach ($res->bandesAnnonces->bandeAnnonce as $ba_dvdfr) {
            $ba=new BandeAnnonce();
            
            if(isset($ba_dvdfr['url'])){
                $ba->filename=$this->title;
                $ba->type='Bande-annonce';
                if(isset($ba_dvdfr['lang'])){
                    $ba->langue=''.$ba_dvdfr['lang'];
                }
                //var_dump($tlangues);
                if(isset($tlangues[$ba->langue])==false)$tlangues[$ba->langue]=0;
                $tlangues[$ba->langue]=$tlangues[$ba->langue]+1;
                
                
                $ba->titre=  $this->title.' '.$ba->type.' '. $tlangues[$ba->langue]  .' '.$ba->langue;
                
                //le code est de la forme '//i.ytimg.com/vi/aZd-HmEp5Co/0.jpg'
                $t= explode('/', ''.$ba_dvdfr['thumb']);
                $ba->code=$t[count($t)-2];
                
                $ba->url='https://www.youtube.com/watch?feature=player_embedded&v='.$ba->code;
                
                //miniature (de la forme '//i.ytimg.com/vi/aZd-HmEp5Co/0.jpg')
                if(isset($ba_dvdfr['thumb'])){
                    $ba->thumbnailurl=''.$ba_dvdfr['thumb'];
                    if(substr($ba->thumbnailurl, 0, 2)=='//')$ba->thumbnailurl='http:'.$ba->thumbnailurl;
                }
                

                //ajout de la bande annonce dans le tableau
                array_push($this->bandes_annonces, $ba);
            }
            //var_dump($ba);

        }
        
        
        //var_dump($res->bandesAnnonces->bandeAnnonce);


        /*
          $this->poster=
          $this->trailer_code=
          $this->trailer_href=
          $this->trailer_embed=

         */
    }

    public function FromArrayAllocineGetFilm($t) {
        
        //var_dump($t);
        
        $this->code_allocine = $t['code'];
        $this->title = $t['title'];

        $this->keywords = isset($t['keywords']) ? $t['keywords'] : $this->title;
        $this->originalTitle = isset($t['originalTitle']) ? $t['originalTitle'] : $this->title;

        $this->movieType = $t['movieType']['$'];


        //nationalité : on concatène la liste
        $s = "";
        if(isset($t['nationality'])){
            foreach ($t['nationality'] as $n) {
                $s = $s . (strlen($s) == 0 ? '' : ', ') . $n['$'];
            }
        }
        $this->nationality = $s;

//        //le genre
        //       $s = "";
        foreach ($t['genre'] as $g) {
            //$s = $s . (strlen($s) == 0 ? '' : ', ') . $n['$'];
            array_push($this->genres, $g['$']);
        }
        $this->genre = $s;


        $this->genre = $this->genres[0];

        //var_dump($t);
        //le synopsis
        $this->synopsis =  isset($t['synopsis'])?strip_tags($t['synopsis']) : '';
        $this->synopsisShort = isset($t['synopsisShort'])?strip_tags($t['synopsisShort']) : $this->synopsis;

        //la durée (elle est exprimée en secondes)
        $this->runtime = isset($t['runtime']) ? $t['runtime'] : '';

//        $this->releaseDate=isset($t['release']['releaseDate'])?$t['release']['releaseDate']:'Inconnu';
//        $this->productionYear=isset($t['productionYear'])?$t['productionYear']:'Inconnu';

        if (isset($t['release']['releaseDate'])) {
            $this->releaseDate = $t['release']['releaseDate'];
            $this->productionYear = substr($this->releaseDate, 0, 4);
        } else {
            $this->releaseDate = 'Inconnu';
            $this->productionYear = isset($t['productionYear']) ? $t['productionYear'] : 'Inconnu';
        }

        //on a pas trouvé de date, on prend la date de sortie DVD
        if($this->releaseDate == 'Inconnu'){
            if (isset($t['dvdReleaseDate'])) {
                $this->releaseDate = $t['dvdReleaseDate'];
                if($this->productionYear=='Inconnu'){
                    $this->productionYear=substr($this->releaseDate, 0, 4);
                }
            }
        }


        $this->directors = isset($t['castingShort']['directors']) ? $t['castingShort']['directors'] : '';
        $this->actors = isset($t['castingShort']['actors']) ? $t['castingShort']['actors'] : '';

        $this->pressRating = isset($t['statistics']['pressRating']) ? $t['statistics']['pressRating'] : 0;
        $this->userRating = isset($t['statistics']['userRating']) ? $t['statistics']['userRating'] : 0;

        $this->href = $t['link'][0]['href'];

        $this->poster = $t['poster'];

        if (isset($t['posterURL'])) {
            $this->posterURL = $t['posterURL'];
        } else {

            if (isset($t['poster'])) {
                $img = new AlloImage();
                $img = $this->poster;
                $img->url();
                $this->posterURL = $img->url();
            } else {
                $this->posterURL = '';
            }
        }

        $this->trailer_code = isset($t['trailer']['code']) ? $t['trailer']['code'] : '';
        $this->trailer_href = isset($t['trailer']['href']) ? $t['trailer']['href'] : '';
        $this->trailer_embed = isset($t['trailerEmbed']) ? $t['trailerEmbed'] : '';

        $this->publicType = isset($t['movieCertificate']['certificate']['$']) ? $t['movieCertificate']['certificate']['$'] : 'Tous publics';

        //récupération du tableau des bandes annonces
        //$bandes_annonces = array();
        //var_dump($t['media']);
        
        //parcours des medias qui sont attachés
        if(isset($t['media'])){
            foreach ($t['media'] as $media) {
                //si la classe est "video"
                if(isset($media['class']) && $media['class']=='video' && isset($media['trailerEmbed'])){
                    //on prend que les Bande-annonce & Teaser
                    if(isset($media['type']) && isset($media['type']['$'])
                            && ($media['type']['$']=='Bande-annonce' ||$media['type']['$']=='Teaser'  )){
                        
                        $ba=new BandeAnnonce();
                        $ba->filename=$this->title;
                        $ba->code=isset($media['code']) ? $media['code'] : '';
                        $ba->titre=isset($media['title']) ? $media['title'] : $this->title.' '.$media['type']['$'];
                        $ba->type=$media['type']['$'];
                        $ba->embed=$media['trailerEmbed'];
                        if(isset($media['version']) && isset($media['version']['$'])){
                            $ba->langue=$media['version']['$'];
                        }
                        //récup de la source depuis l'embed
                        //car il n'y a pas de champ specifique
                        $ba->url= BandeAnnonce::ExtraitSourceAllocineDepuisTrailerEmbed($ba->embed);
                        //récup de la miniature
                        if(isset($media['thumbnail']) && isset($media['thumbnail']['href'])){
                            $ba->thumbnailurl=$media['thumbnail']['href'];
                        }
//var_dump($ba);
                        //ajout de la bande annonce dans le tableau
                        array_push($this->bandes_annonces, $ba);

                    }
                 }
            } 
            
        }

        
        
        //var_dump($this);
    }

}
