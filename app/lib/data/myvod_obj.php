<?php


class MyVOD_Genre {

    public $ID;
    public $Nom;
    public $Count;

}

class MyVOD_AnneeProd {

    public $AnneeSortie;
    public $Count;

}

class LiaisonFichier {

    public $Filename1;
    public $Filename2;
    public $Titre;
    public $Affiche;

}

class BandeAnnonce {

    public $id;
    public $filename;
    public $typeid;
    public $type;
    public $titre;
    public $url;
    public $thumbnailurl;
    public $code;
    public $embed;
    public $langue;
    
    public function __construct() {
     $this->id = '';
     $this->filename = '';
     $this->type = '';
     $this->titre = '';
     $this->url = '';
     $this->thumbnailurl='';
     $this->code = '';
     $this->embed = '';
     $this->langue = '';
 
    }
    
    public function GetEmbedHTML() {
        $this->FixHrefYoutube();
        if (strlen($this->embed) > 0) {
            //$ba = $this->embed;
            //ajoute scrolling="no"
            $ba=str_ireplace('<iframe ','<iframe scrolling="no"',$this->embed);
            //var_dump($this->embed);
            //var_dump($ba);
            return $ba;
        }

        //il n'y a pas d'emnbed, on fabrique un lien avec l'image
        ?>
        <a href="<?= $this->url; ?>" target="_blank">
            <img src="<?= $this->thumbnailurl; ?>" 
                 alt="<?= $this->GetTypeAvecLangue(); ?>"
                 class ="img-responsive bandes-annonce-miniature">
            <img src="<?= theme_config::$repertoire_img . '/lecture.png' ?>" 
                 class="bandes-annonce-miniature-lecture"
                 alt="<?= $this->GetTypeAvecLangue(); ?>"
                 >

        </a>
                
                
        <?php
    }

    public function GetTypeAvecLangue(){
        if ($this->IsVF()){
            return $this->type;//.' (VF)';
        }
         return $this->type.' (VO)';
    }


    public function InitFromStdClass($stdclass) {

        foreach ($stdclass as $attribut => $valeur) {
            $attribut=strtolower($attribut);
            $this->$attribut = $valeur;
        }
    }
    
    public function IsVF(){
        if($this->langue=='Français'||$this->langue=='VF')return TRUE;
        if(strpos($this->titre,'VF'))return TRUE;
        //return false;
        
        //LC: 4/03/2023 on mets par défaut vrai sauf exception
        //VOSTF 
        if(strpos($this->titre,'VO'))return FALSE;
        
        return TRUE; 
        
        
    }
    
    public function IsTypeBandeAnnonce(){
        if($this->type=='Bande-annonce')return true;
        //var_dump($this);
        //var_dump(stripos($this->titre,'Bande'));
        if(stripos($this->titre,'Bande')>0)return true;
        return false;
    }
    
    public static function ExtraitSourceAllocineDepuisTrailerEmbed($embed){
        //'<div id='ACEmbed'><iframe src='http://www.allocine.fr/_video/iblogvision.aspx?cmedia=19547238' style='width:480px; height:270px' frameborder='0' allowfullscreen='true'></iframe></div>'
        //extrait la valeur source (ici c'est http://www.allocine.fr/_video/iblogvision.aspx?cmedia=19547238)
        $pieces = explode("src='", $embed);
        $pieces = explode("'", $pieces[1]);
        return $pieces[0];

    }

    
    public function FixHrefYoutube(){
        if(strpos($this->url, 'youtube')){
            $this->embed='<div class="video-embarquee">
<iframe width="560" height="315" src="https://www.youtube.com/embed/'. $this->code. '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</div>';
            
            //exemple d'intégration youtube
            //https://www.lewebpourlesanes.com/comment-ajouter-video-youtube-iframe-responsive/
            /*
            <div class="video-embarquee">
            <iframe allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" src="https://www.youtube.com/embed/7Bd93IZWj0k" width="560" height="315" frameborder="0"></iframe>
          </div>
            
            */ 
        }
        
    }
    
}

class MyVOD_Details {

    public $ID;
    public $Filename;
    public $TitleKey;
    public $Titre;
    public $TitreOriginal;
    public $TypeFilm;
    public $DateSortie;
    public $AnneeSortie;
    public $DureeSec;
    public $Nationalite;
    public $TypePublic;
    public $GenreID1;
    public $GenreID2;
    public $GenreID3;
    public $GenreNom1;
    public $GenreNom2;
    public $GenreNom3;
    public $Realisateur;
    public $Acteurs;
    public $NotePresse;
    public $NoteSpec;
    public $Synopsis;
    public $Affiche;
    public $MovieLink;
    public $NumFicheAllocine;
    public $NumFicheDvdFr;
    public $NumFicheTmdb;
    public $UrlImageSource;
    public $BandeAnnonceUrl;
    public $BandeAnnonceCode;
    public $BandeAnnonceEmbed;
    public $DHCreation;
    public $DHModification;
    public $Remarques;
    public $t_liaisons = array();

    public $BandesAnnonces = array();
    
    public $HD720 = 0;
    public $HD1080 = 0;
    public $Autorise=null;
    
    public $Lu=0;
    public $DateLu=null;
    
    public $TypePublicAutorise = '';
    public $GenreAutorise1=0;
    public $GenreAutorise2=0;
    public $GenreAutorise3=0;
    public $GenreListeBlanche1=0;
    public $GenreListeBlanche2=0;
    public $GenreListeBlanche3=0;
    
    public $MessageModif='';
    
    public $NbSaisons=0;
    public $NbEpisodes=0;
    
    public function get_args_search_google(){
        //construction de l'argument de recherche
        $args = $this->Titre;
        if(strlen($this->AnneeSortie)>0){
            $args.=' '.$this->AnneeSortie;
        }
        if(strlen(config::mots_cles_suppl_google_search())){
            $args.=' '.config::mots_cles_suppl_google_search();
        }
        return $args;
        
        
        
    }
    
    
    
    public function is_fiche_non_renseignee(){
        return $this->Titre == $this->Filename;
    }
    
    
    
    public function MAJChampHDxxFromFilename(){
        $this->HD720=0;
        $this->HD1080=0;
        if(stripos($this->Filename, '720p')!==FALSE){
            $this->HD720=1;
        }
        if(stripos($this->Filename, '1080p')!==FALSE){
            $this->HD1080=1;
        }
       
    }
    
    public function is_mkv(){
        $ext = substr($this->Filename, -4);
        return strcasecmp ($ext ,'.mkv') ==0;
        //return true;
    }
    
    
    
    public function is_vff(){
        if(stripos($this->Filename, 'truefrench')!==FALSE){
            return true;
        }
        if(stripos($this->Filename, 'vff')!==FALSE){
            return true;
        }
        if(stripos($this->Filename, 'vfi')!==FALSE){
            return true;
        }
        if(stripos($this->Filename, 'vf2')!==FALSE){
            return true;
        }
        return false;
    }
    
    public function is_hd(){
        return $this->HD1080!=0 || $this->HD720!=0;
    }
    
    public function BandeAnnonceRender(){
        
        if(strlen(trim($this->BandeAnnonceEmbed))==0){
            if(strlen(trim($this->BandeAnnonceUrl))>0){
            return '<iframe scrolling="no" width="420" height="315"
    src="'.$this->BandeAnnonceUrl.'">
   </iframe>';
           }
            
        }
        //$ba=$this->BandeAnnonceEmbed;
        $ba=str_ireplace('<iframe ','<iframe scrolling="no"',$this->BandeAnnonceEmbed);
        
        return $ba;
        
    }
    
    
    
    public function __toString() {
        return $this->Filename;
    }
    
    public function TrierBandesAnnonces(){
        
        $tbafr=array();     //bandes annonces an francais
        $tbavo=array();     //bandes annonces en vo
        $tvidfr=array();    //video en francais
        $tvidvo=array();    //videos en vo
        
        //le but est de mettre en 1er les bandes en francais
        //puis les teasers en francais
        //puis les bandes annonces en vo
        //puis le reste
        
        //var_dump($this->BandesAnnonces);
        $ba=new BandeAnnonce(); //pour auto completion
        
        //var_dump($this->BandesAnnonces);
        foreach ($this->BandesAnnonces as $ba) {
            
            
            if($ba->IsTypeBandeAnnonce()){
                if($ba->IsVF()){
                    array_push($tbafr, $ba) ;
                }else{
                    array_push($tbavo, $ba) ;
                }
            }else{
                if($ba->IsVF()){
                    array_push($tvidfr, $ba) ;
                }else{
                    array_push($tvidvo, $ba) ;
                }
            }
            
            //tri des autres vidéos en fonction du type
            //$tvidvo
        }
    
        $this->BandesAnnonces=array_merge($tbafr,$tvidfr,$tbavo,$tvidvo);

        //var_dump($this->BandesAnnonces);
        
    }
    
    
    public function SerialiseBandesAnnonces(){
        return base64_encode(serialize( $this->BandesAnnonces));
    }
    
    
    public function DeserialiseBandesAnnonces($serialized){
        $this->BandesAnnonces=  unserialize(base64_decode($serialized));
    }
    
    
    public function NoteSpecSur10(){
        return round($this->NoteSpec*10,1);
    }

    public function NotePresseSur10(){
        return round($this->NotePresse *10,1);
    }
    
    public function NoteMoyenne(){
        if($this->NoteSpec>0 && $this->NotePresse>0){
            return ($this->NoteSpec+$this->NotePresse)/2;
        }else{
            if($this->NotePresse>0){
                return $this->NotePresse;
            }
        }
        return $this->NoteSpec;
    }
    
    
        public function NoteMoyenneTexte(){
        if($this->NoteSpec>0 && $this->NotePresse>0){
            return ('Note presse : '.$this->NotePresseSur10().'/10, note spec. : '.$this->NoteSpecSur10().'/10');
        }else{
            if($this->NotePresse>0){
                return ('Note presse : '.$this->NotePresseSur10().'/10');
            }
            if($this->NoteSpec>0){
                return ('Note spec. : '.$this->NoteSpecSur10().'/10');
            }
        }
        return 'Pas de note';
    }
    
    
    
    
    /**
     * retourne la liste des genres sous forme présentable
     * @return string
     */
    public function Genres() {
        $t=array();
        for($i=1; $i<=3;$i++){
            $g=$this->{"GenreNom$i"};
            if(strlen($g)){
                array_push($t, $g);
            }
        }
        return join(', ',$t) ;
    }
    
    /**
     * retourne la date au format long
     * @return type string
     */
    public function DateSortieLongue(){
        return self::DateLongue($this->DateSortie);
    }
    
    
    public static function HeureLongue($datecourte_str){
        if($datecourte_str!=null){
            setlocale (LC_TIME, 'fr-FR','fra');
            $trad=strftime("%H:%M",strtotime($datecourte_str));
            return utf8_encode(ucwords($trad));
        }
        return "";
    }

        
    public static function DateLongue($datecourte_str){
        if($datecourte_str!=null){
            setlocale (LC_TIME, 'fr-FR','fra');
            $trad=strftime("%A %d %B %Y",strtotime($datecourte_str));
            if(Helper_system::serv_OS_is_windows()){
                return utf8_encode(ucwords($trad));
            }
            return ucwords($trad);
        }
        return "";
/*
        $d=new DateTime($datecourte_str);
        var_dump($d);
        return $d->format('%A');*/
    }  
    
    /**
     * retourne la durée formatée
     * @return string 
     */
    public function Duree() {
        if(isset($this->DureeSec) && $this->DureeSec >0){
            return MyVOD_Details::duree_en_heure_minutes($this->DureeSec);
        }
        return 'durée inconnue';
    }

    public function Duree_html5() {
        return MyVOD_Details::duree_en_heure_minutes_html5($this->DureeSec);
    }

    public function duree_from_html5($duree_html5){

        sscanf($duree_html5, "%d:%d:%d", $hours, $minutes, $seconds);

        $time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 3600 + $minutes*60;
        $this->DureeSec = $time_seconds;

    }
    
    public function get_infos_dhCreation(){
        $dcre =  MyVOD_Details::DateLongue( $this->DHCreation);
        $dmodif =  MyVOD_Details::DateLongue( $this->DHModification);
        
        //var_dump($dcre,$dmodif);
        
        $txt_creation_modif='Fiche crée le '. $dcre;
        if($dcre != $dmodif ){
            $txt_creation_modif.= ' (modifiée le '.$dmodif.')';
            
        }
        return $txt_creation_modif;
    }
    
    
    
    public function get_infos_details_serie_long(){
        $detail_nb_serie='';
        if($this->NbSaisons>1){
            $detail_nb_serie =  $this->NbSaisons . ' Saison' . ($this->NbSaisons==1 ? '' : 's');
        }
        if($this->NbEpisodes>0){
            if(strlen($detail_nb_serie)>0)$detail_nb_serie.=' - ';
            $detail_nb_serie.= $this->NbEpisodes . ' Epsode' . ($this->NbEpisodes==1 ? '' : 's');
        }
        return $detail_nb_serie;
    }
    
    public function get_infos_details_serie(){
        $detail_nb_serie='';
        if($this->NbSaisons>1){
            $detail_nb_serie =  $this->NbSaisons . ' Sais.';
        }
        if($this->NbEpisodes>0){
            if(strlen($detail_nb_serie)>0)$detail_nb_serie.=' - ';
            $detail_nb_serie.= $this->NbEpisodes . ' Ep.';
        }
        return $detail_nb_serie;
    }
    
    
    //utilisé lors d'un résultat de requête
    public function InitFromStdClass($stdclass) {

        foreach ($stdclass as $attribut => $valeur) {
            
            $this->$attribut = $valeur;
        }
        //var_dump($this->HD720 );
    }

    public function InitFromWebGetFilmData(WebGetFilmData $fiche_web) {


        $this->Titre = $fiche_web->title;
        $this->TitleKey = $fiche_web->keywords;

        $this->TitreOriginal = $fiche_web->originalTitle;


        $this->MovieLink = $fiche_web->href;



        $this->DateSortie = $fiche_web->releaseDate;
        $this->AnneeSortie = $fiche_web->productionYear;

        $this->Nationalite = $fiche_web->nationality;

        $this->TypeFilm = $fiche_web->movieType;


        $this->TypePublic = $fiche_web->publicType;
        $this->DureeSec = $fiche_web->runtime;

        $this->NbSaisons = $fiche_web->NbSaisons;
        $this->NbEpisodes = $fiche_web->NbEpisodes;
    
        $this->GenreNom1 = $fiche_web->genre;
        //idem pour les  2 & 3
        $n = count($fiche_web->genres);
        //je sais c'est pas trop élégant
        if ($n >= 2) {
            $this->GenreNom2 = $fiche_web->genres[1];
        }
        if ($n >= 3) {
            $this->GenreNom3 = $fiche_web->genres[2];
        }

        $this->NumFicheAllocine = $fiche_web->code_allocine;
        $this->NumFicheDvdFr = $fiche_web->code_dvdfr;
        $this->NumFicheTmdb = $fiche_web->code_tmdb;



        $this->Realisateur = $fiche_web->directors;
        $this->Acteurs = $fiche_web->actors;

        //todo quand null
        $this->NotePresse = $fiche_web->pressRating > 0 ? $fiche_web->pressRating * 2 / 10 : null;

        $this->NoteSpec = $fiche_web->userRating > 0 ? $fiche_web->userRating * 2 / 10 : null;

        $this->Synopsis = $fiche_web->synopsis;

        //l'url de l'image
        $this->UrlImageSource = $fiche_web->posterURL;

        //la bande annonce
        $this->BandeAnnonceUrl = $fiche_web->trailer_href;
        
        $this->BandeAnnonceCode=$fiche_web->trailer_code;
        $this->BandeAnnonceEmbed=$fiche_web->trailer_embed;
        
        //les bandes annonces (LC le 25/09/2015)
        $this->BandesAnnonces=$fiche_web->bandes_annonces;
        //reprend les filename des bandes avec celui de notre objet
        //car le filename est le pilier 
        foreach ($this->BandesAnnonces as $ba) {
            $ba->filename=$this->Filename;
            $ba->FixHrefYoutube();
            //var_dump($ba);
            //exit();

        } 
    }


    
    
    
    public static function duree_en_heure_minutes_html5($total_secondes) {
        $total_secondes='0'.$total_secondes;
        //exit();
        $h = (int) ($total_secondes / 3600);
        $m = (int) ($total_secondes / 60) % 60;
        $s = $total_secondes % 36;
        return sprintf('%02d', $h) . ':' . sprintf('%02d', $m);
    }

    public static function duree_en_heure_minutes($total_secondes) {
        $h = (int) ($total_secondes / 3600);
        $m = (int) ($total_secondes / 60) % 60;
        $s = $total_secondes % 36;
        return $h . 'h' . sprintf('%02d', $m) . 'min';
    }

}
