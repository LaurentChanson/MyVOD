<?php

require_once 'sqlite_db.php';
require_once 'myvod_db.php';

/**
 * Description of myvod_db_maj
 *
 * @author Laurent
 */
class MyVOD_DB_MAJ extends sqlite_db {

    /**
     * récupère le numéro de version de la base de données data.db
     * @param MyVOD_DB $myvod_db
     */
    public function get_version() {
        $sql = 'SELECT Version FROM MyVOD';
        return $this->get_value($sql);
    }

    private function maj_begin() {
        parent::begin_trans();
        //$this->execute('BEGIN;');
    }

    private function maj_commit(&$version) {
        //incrément de version
        $version++;
        //enregistre dans la bdd
        $this->execute("UPDATE `myvod` SET `version`=$version;");
        //commit
        parent::commit();
        //$this->execute('COMMIT;');
        //refresh du numéro de version
        $version = $this->get_version();
    }

    public function maj_suivantes() {
        //récupère le numéro de version
        $version = $this->get_version();

        if ($version > 28) {
            //var_dump($_POST);
            //var_dump($version);
            //exit();
            return;
        }

        /*
         * Changement de la vue 'DetailSansDoublon'
         */
        if ($version == 0) {
            $this->maj_begin();

            $this->execute('DROP VIEW DetailSansDoublon;');

            $this->execute('CREATE VIEW DetailSansDoublon AS
                    SELECT *
                      FROM Detail
                     WHERE filename NOT IN (
                               SELECT filename2
                                 FROM Liaison
                           );');

            $this->maj_commit($version);
        }

        /*
         * Ajout de la table BandeAnnonce (25/09/2015)
         */
        if ($version == 1) {
            $this->maj_begin();

            $this->execute("CREATE TABLE BandeAnnonce (
    ID       INTEGER PRIMARY KEY,
    Filename TEXT    REFERENCES Video (Filename) ON DELETE CASCADE
                                                 ON UPDATE CASCADE
                     NOT NULL,
    Titre    TEXT,
    Url      TEXT    DEFAULT (''),
    Code     TEXT    DEFAULT (''),
    Embed    TEXT    DEFAULT (''),
    Langue   TEXT    DEFAULT ('') 
);");


            $this->maj_commit($version);
        }


        /*
         * Ajout de la table BandeAnnonceType (01/10/2015)
         */

        if ($version == 2) {
            $this->maj_begin();

            $this->execute("CREATE TABLE BandeAnnonceType (
    ID  INTEGER PRIMARY KEY AUTOINCREMENT
                UNIQUE,
    Nom TEXT    DEFAULT ('') 
);");

            $this->maj_commit($version);
        }


        /*
         * Ajout du champ Type dans la table BandeAnnonce (01/10/2015)
         */

        if ($version == 3) {
            $this->maj_begin();

            $this->execute("ALTER TABLE BandeAnnonce RENAME TO MyVOD_temp_table_maj_4;");

            $this->execute("CREATE TABLE BandeAnnonce (
    ID       INTEGER PRIMARY KEY,
    Filename TEXT    REFERENCES Video (Filename) ON DELETE CASCADE
                                                 ON UPDATE CASCADE
                     NOT NULL,
    TypeID   INTEGER REFERENCES BandeAnnonceType (ID),
    Titre    TEXT,
    Url      TEXT    DEFAULT (''),
    ThumbnailUrl TEXT    DEFAULT (''),
    Code     TEXT    DEFAULT (''),
    Embed    TEXT    DEFAULT (''),
    Langue   TEXT    DEFAULT ('') 
);");
            $this->execute("INSERT INTO BandeAnnonce (
                             ID,
                             Filename,
                             Titre,
                             Url,
                             Code,
                             Embed,
                             Langue
                         )
                         SELECT ID,
                                Filename,
                                Titre,
                                Url,
                                Code,
                                Embed,
                                Langue
                           FROM MyVOD_temp_table_maj_4;");


            $this->execute("DROP TABLE MyVOD_temp_table_maj_4;");

            $this->maj_commit($version);
        }


        /*
         *  Création de la table DernierLu 
         */


        if ($version == 4) {

            $this->maj_begin();

            $this->execute("CREATE TABLE DernierLu (
    IP         TEXT     NOT NULL,
    Filename   TEXT     REFERENCES Video (Filename) ON DELETE CASCADE
                                                    ON UPDATE CASCADE
                        NOT NULL,
    DHCreation DATETIME DEFAULT ( (datetime('now', 'localtime') ) ),
    PRIMARY KEY (
        IP,
        Filename
    )
);
");

            $this->maj_commit($version);
        }


        /*
         *  Création des champs HD720 et HD1080 de la table Video 
         */


        if ($version == 5) {
            $this->maj_begin();

            $this->execute("ALTER TABLE Video ADD COLUMN  HD720            INT      DEFAULT (0);");
            $this->execute("ALTER TABLE Video ADD COLUMN  HD1080            INT      DEFAULT (0);");


            $this->maj_commit($version);
        }


        /**
         * ajouts d'index dans la table Video
         */
        if ($version == 6) {
            $this->maj_begin();
            $this->execute("CREATE INDEX AnneeSortie ON Video (AnneeSortie);");
            $this->execute("CREATE INDEX HD720 ON Video (HD720);");
            $this->execute("CREATE INDEX HD1080 ON Video (HD1080);");
            $this->execute("CREATE INDEX TypePublic ON Video (TypePublic);");



            $this->maj_commit($version);
        }

        /**
         * ajouts d'index dans la table Genre
         */
        if ($version == 7) {
            $this->maj_begin();
            $this->execute("CREATE INDEX GenreNom ON Genre (Nom);");

            $this->maj_commit($version);
        }


        /**
         * Création de la table TypePublicAutorise
         */
        if ($version == 8) {
            $this->maj_begin();
            $this->execute("CREATE TABLE TypePublicAutorise (
    Nom      TEXT    PRIMARY KEY 
);");
            $this->maj_commit($version);
        }




        /**
         * Ajout du champ 'Autorise' et 'ListeBlanche' dans la table 'Genre'
         */
        if ($version == 9) {
            $this->maj_begin();

            $this->execute("ALTER TABLE Genre ADD COLUMN Autorise INTEGER DEFAULT (0) ;");
            $this->execute("ALTER TABLE Genre ADD COLUMN ListeBlanche INTEGER DEFAULT (0) ;");

            $this->maj_commit($version);
        }



        /**
         * Création de la vue DetailSansDoublonFiltre
         */
        if ($version == 10) {
            $this->maj_begin();
            $this->execute("CREATE VIEW DetailSansDoublonFiltre AS
    SELECT *, 1 as Autorise FROM Detail
WHERE
filename NOT IN (
               SELECT filename2 FROM Liaison
           )
AND 
        Detail.TypePublic IN (
               SELECT t.Nom FROM TypePublicAutorise t
           )
AND ((
       Detail.GenreID1 IN ( 
               SELECT g.ID FROM Genre g WHERE g.Autorise <> 0
           )
AND 
       (Detail.GenreID2 IN (
                SELECT g.ID  FROM Genre g WHERE g.Autorise <> 0
            ) OR Detail.GenreID2 IS NULL)
AND 
       (Detail.GenreID3 IN (
                SELECT g.ID FROM Genre g WHERE g.Autorise <> 0
            ) OR Detail.GenreID3 IS NULL)
   )
   OR 
       Detail.GenreID1 IN ( 
               SELECT g.ID FROM Genre g WHERE g.ListeBlanche <> 0
           )
   OR 
       Detail.GenreID2 IN ( 
               SELECT g.ID FROM Genre g WHERE g.ListeBlanche <> 0
           )
   OR 
       Detail.GenreID3 IN ( 
               SELECT g.ID FROM Genre g WHERE g.ListeBlanche <> 0
           )
);
");


            $this->maj_commit($version);
        }


        /**
         * Modofocation de la vue DetailSansDoublon
         */
        if ($version == 11) {
            $this->maj_begin();
            $this->execute("DROP VIEW DetailSansDoublon;");


            $this->execute('CREATE VIEW DetailSansDoublon AS
            SELECT * FROM DetailSansDoublonFiltre
            
            UNION

            SELECT *, 0 as Autorise
            FROM Detail
            WHERE filename NOT IN (
                SELECT filename2
                FROM Liaison
            )
            AND filename NOT IN (
                SELECT filename FROM DetailSansDoublonFiltre
);');




            $this->maj_commit($version);
        }

        /**
         * ajouts d'index dans la table Video
         */
        if ($version == 12) {
            $this->maj_begin();
            $this->execute("CREATE INDEX VideoFilename ON Video (Filename);");

            $this->maj_commit($version);
        }
        /**
         * ajouts d'index dans la table Video
         */
        if ($version == 13) {
            $this->maj_begin();

            $this->execute("CREATE INDEX Nationalite ON Video (Nationalite);");

            $this->maj_commit($version);
        }
        /**
         * ajouts d'index GenresX dans la table Video car SQLite ne crée pas d'index
         * quand il y a des foregn keys
         */
        if ($version == 14) {
            $this->maj_begin();

            $this->execute("CREATE INDEX GenreID1 ON Video (GenreID1);");
            $this->execute("CREATE INDEX GenreID2 ON Video (GenreID2);");
            $this->execute("CREATE INDEX GenreID3 ON Video (GenreID3);");
            $this->maj_commit($version);
        }
        /**
         * ajouts d'index dans la table Genre 
         */
        if ($version == 15) {
            $this->maj_begin();

            $this->execute("CREATE INDEX GenreAutorise ON Genre (Autorise);");
            $this->execute("CREATE INDEX GenreListeBlanche ON Genre (ListeBlanche);");

            $this->maj_commit($version);
        }
        /**
         * ajouts d'index dans la table TypePublicAutorise 
         */
        if ($version == 16) {
            $this->maj_begin();

            $this->execute("CREATE INDEX TypePublicAutoriseNom ON TypePublicAutorise (Nom);");

            $this->maj_commit($version);
        }


        /**
         * ajouts d'index dans la table DernierLu
         */
        if ($version == 17) {
            $this->maj_begin();
            $this->execute("CREATE INDEX dlFilename ON DernierLu (Filename);");
            $this->execute("CREATE INDEX dlIP ON DernierLu (IP);");

            $this->maj_commit($version);
        }



        /**
         * Modofocations de la table DernierLu
         */
        if ($version == 18) {
            $this->maj_begin();

            $this->execute("ALTER TABLE DernierLu RENAME TO myvod_temp_table_18;");
            $this->execute("CREATE TABLE DernierLu (
    IP         TEXT     NOT NULL,
    Filename   TEXT     REFERENCES Video (Filename) ON DELETE CASCADE
                                                    ON UPDATE CASCADE
                        NOT NULL,
    DHCreation DATETIME DEFAULT ( (datetime('now', 'localtime') ) ),
    PRIMARY KEY (
        IP,
        Filename,
        DHCreation
    )
);");
            $this->execute("INSERT INTO DernierLu (
                          IP,
                          Filename,
                          DHCreation
                      )
                      SELECT IP,
                             Filename,
                             DHCreation
                        FROM myvod_temp_table_18;");
            $this->execute("DROP TABLE myvod_temp_table_18;");
            //recréation des index
            $this->execute("CREATE INDEX dlFilename ON DernierLu (Filename);");
            $this->execute("CREATE INDEX dlIP ON DernierLu (IP);");
            $this->execute("CREATE INDEX dlDHCreation ON DernierLu (DHCreation);");

            $this->maj_commit($version);
        }

        /*
         * Création de "HistoLu" à partir de "DernierLu" & création de la vue "DernierLu"
         */
        if ($version == 19) {
            $this->maj_begin();

            $this->execute("ALTER TABLE DernierLu RENAME TO HistoLu;");

            $this->execute("CREATE VIEW DernierLu AS
    SELECT IP,
           FileName,
           MAX(DHCreation) AS DHCreation,
           COUNT() AS NbLu
      FROM HistoLu
     GROUP BY FileName,
              IP;");

            $this->maj_commit($version);
        }


        /*
         * Remplace l'IP dans HistoLu ('::1' devient '127.0.0.1')
         */
        if ($version == 20) {
            $this->maj_begin();

            $this->execute("UPDATE HistoLu set IP='127.0.0.1' WHERE IP='::1'");

            $this->maj_commit($version);
        }

        /*
         * Ajout d'index dans les bandes annonces
         */
        if ($version == 21) {
            $this->maj_begin();

            $this->execute("CREATE INDEX baFilename ON BandeAnnonce (Filename);");
            $this->execute("CREATE INDEX baTypeID ON BandeAnnonce (TypeID);");
            $this->execute("CREATE INDEX baCode ON BandeAnnonce (Code);");

            $this->maj_commit($version);
        }



        /**
         * ajouts d'index dans la table Liaison
         */
        if ($version == 22) {
            $this->maj_begin();
            $this->execute("CREATE INDEX lFilename1 ON Liaison (Filename1);");
            $this->execute("CREATE INDEX lFilename2 ON Liaison (Filename2);");

            $this->maj_commit($version);
        }


        /**
         * Modofocation de la vue Detail
         */
        if ($version == 23) {
            $this->maj_begin();
            $this->execute("DROP VIEW Detail;");

            $this->execute('CREATE VIEW Detail AS
                SELECT
    t.Nom IS NOT NULL AS TypePublicAutorise,
     v.*,
           g1.Nom AS GenreNom1,
           g2.Nom AS GenreNom2,
           g3.Nom AS GenreNom3,
           
           g1.Autorise AS GenreAutorise1,
           g2.Autorise AS GenreAutorise2,
           g3.Autorise AS GenreAutorise3,
           g1.ListeBlanche AS GenreListeBlanche1,
           g2.ListeBlanche AS GenreListeBlanche2,
           g3.ListeBlanche AS GenreListeBlanche3,
           IFNULL(t.Nom IS NOT NULL AND 
                  ( (g1.Autorise <> 0 AND 
                     (g2.Autorise <> 0 OR 
                      v.GenreID2 IS NULL) AND 
                     (g3.Autorise <> 0 OR 
                      v.GenreID3 IS NULL) ) OR 
                    g1.ListeBlanche <> 0 OR 
                    g2.ListeBlanche <> 0 OR 
                    g3.ListeBlanche <> 0), 0) AS Autorise
      FROM Video v
           LEFT JOIN
           TypePublicAutorise t ON t.Nom = v.TypePublic
           LEFT JOIN
           Genre g1 ON v.GenreID1 = g1.ID
           LEFT JOIN
           Genre g2 ON v.GenreID2 = g2.ID
           LEFT JOIN
           Genre g3 ON v.GenreID3 = g3.ID;');




            $this->maj_commit($version);
        }




        /**
         * Modofocation de la vue DetailSansDoublon
         */
        if ($version == 24) {
            $this->maj_begin();
            $this->execute("DROP VIEW DetailSansDoublon;");

            $this->execute('CREATE VIEW DetailSansDoublon AS
            SELECT *
              FROM Detail;');

            $this->maj_commit($version);
        }

        /**
         * Modofocation de la vue DetailSansDoublonFiltre
         */
        if ($version == 25) {
            $this->maj_begin();
            $this->execute("DROP VIEW DetailSansDoublonFiltre;");

            $this->execute('CREATE VIEW DetailSansDoublonFiltre AS
            SELECT *
              FROM Detail d
             WHERE d.Autorise <> 0;');


            $this->maj_commit($version);
        }

        /*
         *  Création du champ MessageModif de la table Video 
         */


        if ($version == 26) {
            $this->maj_begin();

            $this->execute("ALTER TABLE Video ADD COLUMN MessageModif TEXT DEFAULT ('');");

            $this->maj_commit($version);
        }

        /*
         * création de l'index sur le titre
         */
        if ($version == 27) {
            $this->maj_begin();

            $this->execute("CREATE INDEX vTitre ON Video (Titre);");

            $this->maj_commit($version);
        }
        
        
        /*
         * Ajout de la colonne 'NumFicheTmdb' dans la table 'Video'
         */
        if ($version == 28) {
            
            $this->maj_begin();

            $this->execute('ALTER TABLE Video ADD COLUMN NumFicheTmdb INTEGER;');

            $this->maj_commit($version);
           
        } 
        
        
        
        
        
        
        /*


          DetailSansDoublonFiltre : a tester a la place des requetes imbriquées


         */









        //rappel : modifier au début de la procédure car sinon la maj ne se fera pas
    }

    /*
     * Mise à jour initiale
     */

    public function maj_initiale() {

        /*
         * Création de la table commentaire
         */

        $this->execute("CREATE TABLE Commentaire (
    ID             INTEGER  PRIMARY KEY AUTOINCREMENT,
    Filename       TEXT     REFERENCES Video (Filename) ON DELETE CASCADE
                                                        ON UPDATE CASCADE,
    DHCreation     DATETIME DEFAULT ( (datetime('now', 'localtime') ) ),
    DHModification DATETIME DEFAULT ( (datetime('now', 'localtime') ) ),
    User           TEXT     DEFAULT (''),
    Commentaires   TEXT     DEFAULT ('') 
);");

        /*
         * Création de la table Genre
         */



        $this->execute("CREATE TABLE Genre (
    ID  INTEGER PRIMARY KEY AUTOINCREMENT
                UNIQUE,
    Nom TEXT    DEFAULT ('') 
);");



        /*
         * Création de la table Ignore
         */

        $this->execute("CREATE TABLE [Ignore] (
    Filename TEXT PRIMARY KEY
                UNIQUE
);");


        /*
         * Création de la table Liaison
         */
        $this->execute("CREATE TABLE Liaison (
    Filename1 TEXT NOT NULL
                   REFERENCES Video (Filename) ON DELETE CASCADE
                                               ON UPDATE CASCADE,
    Filename2 TEXT NOT NULL
                   REFERENCES Video (Filename) ON DELETE CASCADE
                                               ON UPDATE CASCADE,
    PRIMARY KEY (
        Filename1,
        Filename2
    )
    ON CONFLICT IGNORE
);");

        /*
         * Création de la table MyVOD
         */

        $this->execute("CREATE TABLE MyVOD (
    Version INT
);");



        /*
         * Création de la table Video
         */



        $this->execute("CREATE TABLE Video (
    ID               INTEGER  PRIMARY KEY AUTOINCREMENT,
    Filename         TEXT     UNIQUE
                              NOT NULL,
    Titre            TEXT     DEFAULT (''),
    TitleKey         TEXT     DEFAULT (''),
    TypeFilm         TEXT     DEFAULT (''),
    TitreOriginal    TEXT     DEFAULT (''),
    DateSortie       DATE,
    AnneeSortie      INTEGER  DEFAULT (0),
    DureeSec         TIME,
    Nationalite      TEXT     DEFAULT (''),
    TypePublic       TEXT     DEFAULT (''),
    GenreID1         INTEGER  REFERENCES Genre (ID),
    GenreID2         INTEGER  REFERENCES Genre (ID),
    GenreID3         INTEGER  REFERENCES Genre (ID),
    Realisateur      TEXT     DEFAULT (''),
    Acteurs          TEXT     DEFAULT (''),
    NotePresse       REAL,
    NoteSpec         REAL,
    Synopsis         TEXT     DEFAULT (''),
    Affiche          TEXT     DEFAULT (''),
    MovieLink        TEXT     DEFAULT (''),
    NumFicheAllocine INTEGER,
    NumFicheDvdFr    INTEGER,
    UrlImageSource   TEXT     DEFAULT (''),
    BandeAnnonceUrl  TEXT     DEFAULT (''),
    BandeAnnonceCode  TEXT     DEFAULT (''),
    BandeAnnonceEmbed TEXT     DEFAULT (''),
    DHCreation       DATETIME DEFAULT ( (datetime('now', 'localtime') ) ),
    DHModification   DATETIME DEFAULT ( (datetime('now', 'localtime') ) ),
    Remarques        TEXT     DEFAULT ('')
);");


        //les index 
        $this->execute('CREATE INDEX [] ON Video (
    Filename COLLATE NOCASE);');





        /*
         * Création de la vue DetailSansDoublon
         */

        $this->execute("CREATE VIEW DetailSansDoublon AS
    SELECT l.*,
           g.Nom AS GenreNom1,
           g2.Nom AS GenreNom2,
           g3.Nom AS GenreNom3
    FROM Video l
           LEFT JOIN
           Genre g ON l.GenreID1 = g.ID
           LEFT JOIN
           Genre g2 ON l.GenreID2 = g2.ID
           LEFT JOIN
           Genre g3 ON l.GenreID3 = g3.ID
     WHERE l.filename NOT IN (
               SELECT filename2
                 FROM Liaison
           );");


        /*
         * Création de la vue Detail
         */

        $this->execute("CREATE VIEW Detail AS
    SELECT l.*,
           g.Nom AS GenreNom1,
           g2.Nom AS GenreNom2,
           g3.Nom AS GenreNom3
      FROM Video l
           LEFT JOIN
           Genre g ON l.GenreID1 = g.ID
           LEFT JOIN
           Genre g2 ON l.GenreID2 = g2.ID
           LEFT JOIN
           Genre g3 ON l.GenreID3 = g3.ID;");



        /*
         * Valeur par défaut pour les paramètres
         */

        $this->execute("INSERT INTO MyVOD (Version) VALUES (0);");
    }

}
