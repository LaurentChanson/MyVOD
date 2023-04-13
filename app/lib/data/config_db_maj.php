<?php

require_once 'sqlite_db.php';
require_once 'config_db.php';

class config_db_MAJ extends sqlite_db {

    public function maj_suivantes() {
        //récupère le numéro de version
        $version = $this->get_version();
        

        
        
        if ($version > 10)
            return;

        /*
         * Ajout de la colonne 'controle_parental' dans la table 'config'
         */
        if ($version == 0) {

            $this->maj_begin();

            $this->execute('ALTER TABLE config ADD COLUMN controle_parental INT DEFAULT (0);');

            $this->maj_commit($version);
        }
        /*
         * Ajout de la colonne 'code_parental' dans la table 'config'
         */
        if ($version == 1) {

            $this->maj_begin();

            $this->execute("ALTER TABLE config ADD COLUMN code_parental TEXT DEFAULT ('0000');");

            $this->maj_commit($version);
        }
        /*
         * Ajout de la colonne 'nb_visu_histo' dans la table 'config'
         */
        if ($version == 2) {

            $this->maj_begin();

            $this->execute("ALTER TABLE config ADD COLUMN nb_visu_histo INT     DEFAULT (8);");

            $this->maj_commit($version);
        }

        /*
         * Ajout de la colonne 'nb_visu_ajouts' dans la table 'config'
         */
        if ($version == 3) {

            $this->maj_begin();

            $this->execute("ALTER TABLE config ADD COLUMN nb_visu_ajouts INT     DEFAULT (8);");

            $this->maj_commit($version);
        }
        
     
        /*
         * Ajout de la colonne 'affichage_visionnes_apres_ajouts' dans la table 'config'
         */
        if ($version == 4) {

            $this->maj_begin();

            $this->execute("ALTER TABLE config ADD COLUMN affichage_visionnes_apres_ajouts INT     DEFAULT (0);");

            $this->maj_commit($version);
        }
        /*
         * Ajout de la colonne 'taille_fichiers_64_bits' dans la table 'config'
         */
        if ($version == 5) {

            $this->maj_begin();

            $this->execute("ALTER TABLE config ADD COLUMN taille_fichiers_64_bits INT(1)     DEFAULT (0);");

            $this->maj_commit($version);
        }
        /*
         * Ajout de la colonne 'mots_cles_suppl_google_search' dans la table 'config'
         */
        if ($version == 6) {

            $this->maj_begin();

            $this->execute("ALTER TABLE config ADD COLUMN mots_cles_suppl_google_search TEXT     DEFAULT 'VF';");

            $this->maj_commit($version);
        }
        /*
         * Ajout de la colonne 'affichage_liste_tablette' dans la table 'config'
         */
        if ($version == 7) {

            $this->maj_begin();

            $this->execute("ALTER TABLE config ADD COLUMN affichage_liste_tablette INT (1) DEFAULT (1);");

            $this->maj_commit($version);
        }
        /*
         * Ajout de la colonne 'affichage_liste_tablette' dans la table 'config'
         */
        if ($version == 8) {

            $this->maj_begin();

            $this->execute("ALTER TABLE config ADD COLUMN tri_recherche_def INT (1) DEFAULT (0);");

            $this->maj_commit($version);
        }
        
         /*
         * Ajout de la colonne 'tmdb_api_key' dans la table 'config'
         */
        if ($version == 9) {

            $this->maj_begin();

            $this->execute("ALTER TABLE config ADD COLUMN tmdb_api_key TEXT DEFAULT '';");

            $this->maj_commit($version);
        }
 
        
                /*
         * Ajout de la colonne 'type_recherche_def' dans la table 'config'
         */
        if ($version == 10) {

            $this->maj_begin();

            $this->execute('ALTER TABLE config ADD COLUMN type_recherche_def INT DEFAULT (2);');
            //2=TMDB par défaut car Allociné Down
            $this->maj_commit($version);
        }
        
        //var_dump("version=" . $version);
    }

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

}
