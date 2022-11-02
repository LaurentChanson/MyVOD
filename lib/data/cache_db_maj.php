<?php
require_once 'sqlite_db.php';
require_once 'cache_db.php';

/**
 * Description of cache_db_MAJ
 *
 * @author Laurent
 */
class cache_db_MAJ extends sqlite_db {

    /**
     * récupère le numéro de version de la base de données cache.db
     */
    public function get_version() {
        $sql = 'SELECT version FROM config';
        return $this->get_value($sql);
    }

    private function maj_begin() {
        parent::begin_trans();
    }

    private function maj_commit(&$version) {
        //incrément de version
        $version++;
        //enregistre dans la bdd
        $this->execute("UPDATE `config` SET `version`=$version;");
        //commit
        parent::commit();
        //refresh du numéro de version
        $version = $this->get_version();
    }

    public function maj_suivantes() {
        //récupère le numéro de version
        $version = $this->get_version();
        
        if ($version >3)
            return;


        /*
         * Ajout de la colonne 'infomedia_html' dans la table 'file'
         */
        if ($version == 0) {
            
            $this->maj_begin();

            $this->execute('ALTER TABLE file ADD COLUMN media_info_html BLOB;');

            $this->maj_commit($version);
           
        }
       
         /*
         * Ajout de la colonne 'width' dans la table 'file'
         */
        if ($version == 1) {
            
            $this->maj_begin();

            $this->execute('ALTER TABLE file ADD COLUMN width INTEGER;');

            $this->maj_commit($version);
           
        }
        
         /*
         * Ajout de la colonne 'height' dans la table 'file'
         */
        if ($version == 2) {
            
            $this->maj_begin();

            $this->execute('ALTER TABLE file ADD COLUMN height INTEGER;');

            $this->maj_commit($version);
           
        }
        
         /*
         * Ajout de la colonne 'duration' dans la table 'file'
         */
        if ($version == 3) {
            
            $this->maj_begin();

            $this->execute('ALTER TABLE file ADD COLUMN duration REAL;');

            $this->maj_commit($version);
           
        } 
        
        
        
        
        //rappel : modifier au début de la procédure car sinon la maj ne se fera pas
        
    }




}
