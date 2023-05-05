<?php

/*
$db = new sqlite_db(dirname(dirname(dirname(__DIR__))) . '/data/data.db');
$db->test();
*/

//// SQLLiteExpert ////
//pense bête
//http://lehollandaisvolant.net/tuto/sqlite/#popen

/**
 * Description of db
 *
 * @author Laurent
 */
class sqlite_db {

    public $db_handle;
    public $fic_sqlite;

    public static $arr_fichiers=array();
    
    public static function repertoire_data(){
        $dir= dirname(dirname(dirname(__DIR__))).'/data/';
        $dir=str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $dir);
        return $dir;
    }

    /**
     * permet de tranformer un standard class en objet
     * @param stdClass $stdClass objet standard class retourné par sqlite
     * @param type $obj objet déjà instancié
     */
    public static function std_class_to_obj(stdClass $stdclass, &$obj){
        foreach ($stdclass as $attribut => $valeur) {
            $obj->$attribut = $valeur;
        }
        
    }


    /**
     * retourne vrai si la table existe
     * @param type $table_name : nom de la table
     * @return type booléen
     */
    protected function table_exists($table_name) {

        $sql = "SELECT count(*) FROM sqlite_master WHERE type='table' AND name LIKE '$table_name'";

        $n = $this->get_value($sql);
        
        return $n > 0;
    }

    /**
     * retourne le 1er élément d'une requête
     * @param type $requete
     * @return type
     */
    protected function get_value($requete) {

        // On prépare et éxécute la requête
        $req = $this->db_handle->prepare($requete);
        $req->execute();

        $t = $req->fetch(PDO::FETCH_BOTH);
        
        //var_dump($t);
        
        //retourne le 1er élément
        return $t[0];
    }

    /**
     * retourne une colonne sous forme de tableau de string
     * @param type $requete
     * @param type $num_colonne
     * @return type tableau de string (avec comme index 0,1,2,3...)
     */
    protected function get_vector($requete, $num_colonne = 0) {

        // On prépare et éxécute la requête
        $req = $this->db_handle->prepare($requete);
        $req->execute();

        $t = $req->fetchAll(PDO::FETCH_COLUMN, $num_colonne);

        return $t;
    }

    /**
     * retourne une requête sous forme de tableau de tableaux
     * @param type $requete sql
     * @return type tableau de tableau
     */
    protected function get_array_arr($requete) {


        // On prépare et éxécute la requête
        $req = $this->db_handle->prepare($requete);
        $req->execute();

        // On change la réponse SQL en réponse PHP.
        // Ici, on transforme toute la réponse en un gros tableau
        // (au lieu de faire ligne par ligne dans une boucle while() par exemple)
        return $req->fetchAll();
    }

     /**
     * retourne une requête sous forme de tableau d'objets
     * @param type $requete sql
     * @return type tableau d'objets
     */
    protected function get_array_obj($requete) {


        // On prépare et éxécute la requête
        $req = $this->db_handle->prepare($requete);
        $req->execute();

        // On change la réponse SQL en réponse PHP.
        // Ici, on transforme toute la réponse en un gros tableau
        // (au lieu de faire ligne par ligne dans une boucle while() par exemple)
        return $req->fetchAll(PDO::FETCH_OBJ);
    }

    
    
    /**
     * retourne une requête sous forme d'objets (seule la 1ère ligne est retournée même s'il y a plusieurs lignes)
     * @param type $requete
     * @return type objet
     */
    protected function get_obj($requete) {

        //var_dump($requete);

        // On prépare et éxécute la requête
        $req = $this->db_handle->prepare($requete);
        $req->execute();

        return $req->fetch(PDO::FETCH_OBJ);
    }
    
      /**
     * retourne une requête sous forme de tableau (seule la 1ère ligne est retournée même s'il y a plusieurs lignes)
     * @param type $requete
     * @return type objet
     */
    protected function get_arr($requete) {


        // On prépare et éxécute la requête
        $req = $this->db_handle->prepare($requete);
        $req->execute();

        return $req->fetch();
    }
    
    
    /**
     * Execute une requête sql et retourne vrai en cas de succès
     * @param type $requete
     * @return boolean
     * @throws Exception
     */
    public function execute($requete) {

        //mode debug
        /*echo '<pre>';
        print_r( $requete );
        echo '</pre>';*/
        
        
        
        // On prépare et éxécute la requête
        try {
            $req = $this->db_handle->prepare($requete);
            return $req->execute();
            
        } catch (Exception $e) {
            print_r('<FONT color="red">Erreur lors de l\'exécution de la requête : </FONT><br>' .
                    str_ireplace("\n", "<br>", $requete) .
                    "<br>" . '<FONT color="red">' . $e->getMessage() .
                    "</FONT>");
            // on la relance
            throw $e;
        }


        return false;
    }


    /*
     * Gestion des transactions
     * 
     */

    public function begin_trans() {
        $this->db_handle->beginTransaction();
        //$this->begin_trans();
        
        //return $this->execute('BEGIN TRANSACTION;');
    }

    public  function rollback() {
        $this->db_handle->rollBack();
        //return $this->execute('ROLLBACK;');
    }

    public  function commit() {
        $this->db_handle->commit();
        //return $this->execute('COMMIT;');
    }
   

    
    
    function __construct($fichier_sqlite) {
        
        $this->fic_sqlite = $fichier_sqlite;
        $this->open();
    }
    
    
    
     public  function open() {
        
        if(isset(self::$arr_fichiers[$this->fic_sqlite])){
            $this->db_handle=self::$arr_fichiers[$this->fic_sqlite];
            //var_dump('OK');
            //return;
        }
        
         
        
        $fichier_exists = 0;
        //c'est la 1ère fois qu'on ouvre
        if ($this->db_handle == null) {
            //test si le fichier existe
            $fichier_exists = file_exists($this->fic_sqlite);
            //si le fichier n'existe pas, on teste si le répertoire exitste
            if($fichier_exists==false){
                create_dir_if_not_exists(self::repertoire_data());
            }
            $this->open_sqlite();
            
            //fait la mise à jour initiale si le fichier n'existait pas
            if ($fichier_exists == false) {
                $this->maj_initiale();
            }
            //active les clés etrangères
            $this->execute('PRAGMA foreign_keys=on;');
            
            //met en cache la connexion pour éviter qu'il y en ait plusieurs
            self::$arr_fichiers[$this->fic_sqlite]=$this->db_handle;
            //var_dump(self::$arr_fichiers);
            
            
        }
    }
    
    /**
     * Cette fonction est à overrider s'il y a la mise à jour initiale
     * à faire.
     */
    public  function maj_initiale(){
        //à overrider
    }
    
    
    
    /**
     * 
     * @param type 
     * @return type handle de l'ouverture de la db
     */
    protected function open_sqlite() {

        try {

            // Nouvel objet de base SQLite 
            $this->db_handle = new PDO('sqlite:' . $this->fic_sqlite);
            //https://blog.amartynov.ru/php-sqlite-case-insensitive-like-utf8/
             //$this->db_handle->sqliteCreateFunction('noDiacritics','noDiacritics');
             //
             //
             //
             $this->db_handle->sqliteCreateFunction('regexp',
    function ($pattern, $data, $delimiter = '~', $modifiers = 'isuS')
    {
        if (isset($pattern, $data) === true)
        {
            $r=(preg_match(sprintf('%1$s%2$s%1$s%3$s', $delimiter, $pattern, $modifiers), $data) > 0);
            //var_dump($data.'-'.$pattern.'-'.$r);
            return ($r);
        }

        return null;
    }
);

            // Quelques options
            $this->db_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }


        return $this->db_handle;
    }


    /**
     * clos la connexion
     */
    protected function close() {

        $this->db_handle = null;
    }
    
    function __destruct() {
        $this->close();
    }

}
