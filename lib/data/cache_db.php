<?php
require_once 'cache_db_maj.php';
require_once 'sqlite_db.php';
require_once 'sql.php';

// http://php.net/manual/fr/language.variables.scope.php

class FileInfos {

    public $file_name;
    public $full_path;
    public $size;
    public $media_info_html;
    public $width;
    public $height;
    public $duration;
    
    public function file_exists(){
        return $this->size>0;
    }
    
    
}

class cache_db extends cache_db_MAJ{

    /**
     * pour les informations d'infomédia
     */
    

    public function update_info_html($file_name, $media_info_html_compressed) {
        //mise dans la bdd
        $query=$this->db_handle->prepare("UPDATE file SET media_info_html=? WHERE file_name=?");
        $query->bindParam(1, $media_info_html_compressed, PDO::PARAM_LOB);
        $query->bindParam(2, $file_name, PDO::PARAM_STR);
        $query->execute();

    }
    public function update_info_media($file_name,$width,$height,$duration){
        //var_dump($width);
        $sql="UPDATE file SET width=$width ,height=$height,duration=$duration WHERE file_name=".sql::chaine_vers_sql($file_name, "'++++'");
        $this->execute( $sql);
        
    }
    
    
    
    
    /**
     * pour le cache
     */


    public function get_all_dico(){
        $r=  $this->get_all();
        $dic=array();
        foreach ($r as $e){
            $dic[$e->file_name]=$e;
        }
        return $dic;
    } 
    
    public function get_all(){
        $sql='select lower(f.file_name)as file_name,f.full_path,f.size, f.width, f.height
 from file f ORDER BY file_name';
        
        
        //var_dump($sql);
        
        return $this->get_array_obj( $sql);
    }
    
    public function purger(){
        $sql='DELETE FROM file WHERE size>=0';
        $this->execute( $sql);
    }
    
    public function ajouter($file_name, $full_path, $size) {
        //efface l'ancienne entrée (case insensitive)
        $sql = "DELETE FROM file WHERE UPPER(file_name) LIKE " .sql::chaine_vers_sql($file_name);
        $this->execute( $sql);

        $sql = "INSERT INTO [file] (file_name,full_path,size) VALUES (" . sql::chaine_vers_sql($file_name) .
                "," . sql::chaine_vers_sql($full_path) . ",$size)";
        $this->execute( $sql);
    }

    public function lire($file_name, FileInfos &$fichier) {



        $sql = "SELECT f.[file_name],
    f.[full_path],
    f.[size],
    f.[media_info_html],
    f.width,
    f.height,
    f.duration
    FROM [file] f
    WHERE f.[file_name] like " . sql::chaine_vers_sql($file_name) ;

        
        //var_dump($sql);
        
        
        //var_dump($fichier);
        
        
        $r = $this->get_obj($sql);
        
        //var_dump($r);
        
      
        if($r!=false){
            parent::std_class_to_obj($r, $fichier);
        }
        //var_dump($fichier);
        
    }

    /**
     * Constructeur et maj_initiale
     */
    
 
    public function maj_initiale() {


        //mise à jour initiale
        parent::begin_trans();

        //on crée la table si elle existe
        $sql = "CREATE TABLE IF NOT EXISTS [file] (
          [file_name] VARCHAR2(512) NOT NULL ON CONFLICT REPLACE, 
          [full_path] VARCHAR2(2024), 
          [size] INT64,
          CONSTRAINT [] PRIMARY KEY ([file_name]) ON CONFLICT REPLACE);";

        $this->execute( $sql);


        $sql = "CREATE TABLE IF NOT EXISTS [config] (
          [version] INT);";

        $this->execute( $sql);

        $this->execute( 'INSERT INTO config (version) VALUES (0)');

        parent::commit();
        //fin de la mise à jour initiale
    }
    
    function __construct() {
        $this->fic_sqlite= self::repertoire_data() . '/cache.db';
        $this->open();
        $this->maj_suivantes();
    }
    
   

}
