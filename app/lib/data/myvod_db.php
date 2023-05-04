<?php

define('TYPE_TRI_TITRE', 0);
define('TYPE_TRI_D_AJOUT', 1);
define('TYPE_TRI_D_SORTIE', 2);
define('TYPE_TRI_NOTATION', 3);
define('TYPE_TRI_DUREE', 4);
define('TYPE_TRI_D_SORTIE_ASC', 5);


define("COND_JOINTURE_DEF", 'GROUP BY ID');

require_once 'myvod_db_maj.php';

require_once 'sqlite_db.php';

require_once 'myvod_obj.php';

require_once 'sql.php';

class MyVOD_DB extends MyVOD_DB_MAJ {

    private $table_details_sans_doublons = 'DetailSansDoublon';
    private static $vue_table_video = 'DetailSansDoublon';

    public static function set_filtrage_on($bool) {
        if ($bool) {
            self::$vue_table_video = 'DetailSansDoublonFiltre';
        } else {
            self::$vue_table_video = 'DetailSansDoublon';
        }
    }

    public function get_bande_annonce_types() {
        $sql = "SELECT tba.ID, tba.Nom 
FROM BandeAnnonceType tba
ORDER By tba.Nom";
        $tba = $this->get_array_obj($sql);

        //var_dump($genres);
        return $tba;
    }

    public function bande_annonce_type_ajouter($nom_type_ba) {
        $sql = sprintf("INSERT INTO BandeAnnonceType (Nom) VALUES (%s)", SQL::chaine_vers_sql($nom_type_ba));
        $this->execute($sql);
        return $this->bande_annonce_type_get_id_from_nom($nom_type_ba);
    }

    public function bande_annonce_type_get_id_from_nom($nom_type_ba) {
        $sql = "SELECT tba.ID 
        FROM BandeAnnonceType tba
        WHERE tba.Nom=" . SQL::chaine_vers_sql($nom_type_ba);
        return $this->get_value($sql);
    }

    public function bande_annonce_get_liste_from_filename($filename, &$bandesannonces) {
        $ba = array();
        /*
          $sql = "SELECT ba.*, tba.Nom as Type FROM BandeAnnonce ba ,BandeAnnonceType tba
          WHERE ba.Filename LIKE " . sql::chaine_vers_sql($filename) . "
          AND tba.ID=ba.TypeID
          ORDER BY ba.Titre";
         */
        $sql = "SELECT
LENGTH(ba.titre), 
0+(ba.titre),
  ba.*, tba.Nom as Type FROM BandeAnnonce ba ,BandeAnnonceType tba
            WHERE ba.Filename LIKE " . sql::chaine_vers_sql($filename) . "
            AND tba.ID=ba.TypeID
            ORDER BY  ba.TypeID, ba.Langue DESC, LENGTH(ba.titre), ba.Titre";




//var_dump($sql);
        $tba = $this->get_array_obj($sql);
        //var_dump($tba);

        $bandesannonces = array();
        //on converti les stdclass en bande annonce
        foreach ($tba as $ba) {
            $b = new BandeAnnonce();
            $b->InitFromStdClass($ba);
            array_push($bandesannonces, $b);
        }
    }

    public function bande_annonce_get_from_code($filename, $code) {
        //TODO
    }

    public function bande_annonce_existe($filename, $code) {
        $sql = 'SELECT COUNT(*) as Count
FROM  BandeAnnonce ba 
WHERE ba.Filename LIKE ' . sql::chaine_vers_sql($filename) . '
AND ba.Code= ' . SQL::chaine_vers_sql($code);

        $nb = $this->get_value($sql);
        //var_dump($sql);
        //var_dump('bande_annonce_existe '.$nb);

        return $nb > 0;
    }

    public function bande_annonce_get_by_id($id_ba) {
        $ba = new BandeAnnonce();

        $sql = "SELECT
LENGTH(ba.titre), 
0+(ba.titre),
  ba.*, tba.Nom as Type FROM BandeAnnonce ba ,BandeAnnonceType tba
            WHERE ba.ID = $id_ba
            AND tba.ID=ba.TypeID
            ORDER BY ba.Langue DESC,LENGTH(ba.titre), ba.Titre";

        $o = $this->get_obj($sql);
        //on converti les stdclass en bande annonce
        $ba->InitFromStdClass($o);
        return $ba;
    }

    public function bande_annonce_supprimer_by_id($id_ba) {
        $sql = "DELETE FROM BandeAnnonce WHERE ID=$id_ba";
        //var_dump($sql);
        $this->execute($sql);
    }

    public function bande_annonce_supprimer_by_filename($filename) {
        $sql = "DELETE FROM BandeAnnonce WHERE Filename=" . SQL::chaine_vers_sql($filename);
        //var_dump($sql);
        $this->execute($sql);
    }

    public function bande_annonce_ajouter(BandeAnnonce $ba) {
        //echo('<hr><hr>');
        //var_dump($ba);
        //met à jour l'id du type de bande annince
        $id = $this->bande_annonce_type_get_id_from_nom($ba->type);
        if ($id == null) {
            $id = $this->bande_annonce_type_ajouter($ba->type);
        }
        $ba->typeid = $id;


        //enregistrement de la bande annonce
        $sql = sprintf("INSERT INTO BandeAnnonce (Filename,TypeID,Titre,Url,ThumbnailUrl,Code,Embed,Langue)
                            VALUES (                %s,     %s,   %s,   %s,      %s,      %s,   %s,   %s)", sql::chaine_vers_sql($ba->filename), $ba->typeid, sql::chaine_vers_sql($ba->titre), sql::chaine_vers_sql($ba->url), sql::chaine_vers_sql($ba->thumbnailurl), sql::chaine_vers_sql($ba->code), sql::chaine_vers_sql(trim($ba->embed)), sql::chaine_vers_sql($ba->langue));

        //var_dump(trim($ba->embed));


        $this->execute($sql);
    }

    public function genre_ajouter($nom_genre) {
        $sql = sprintf("INSERT INTO Genre (Nom) VALUES (%s)", SQL::chaine_vers_sql($nom_genre));
        $this->execute($sql);
    }

    public function get_genres() {
        $sql = "SELECT g.ID as ID, IFNULL(g.Nom,'') as Nom, COUNT(*)  as Count
FROM $this->table_details_sans_doublons v
LEFT JOIN Genre g  ON v.GenreID1=g.ID
GROUP BY g.ID
ORDER BY g.Nom";

        //var_dump($sql);


        $genres = $this->get_array_obj($sql);

        //var_dump($genres);
        return $genres;
    }

    public function genres_get_liste_autorise() {
        $sql = "SELECT * FROM Genre g
WHERE g.Autorise!=0
ORDER BY g.Nom";


        $genres = $this->get_array_obj($sql);

        return $genres;
    }

    public function genres_get_liste_blanche() {
        $sql = "SELECT * FROM Genre g
WHERE g.ListeBlanche!=0
ORDER BY g.Nom";


        $genres = $this->get_array_obj($sql);

        return $genres;
    }

    public function genres_get_liste_non_blanche() {
        $sql = "SELECT * FROM Genre g
WHERE g.ListeBlanche==0
ORDER BY g.Nom";


        $genres = $this->get_array_obj($sql);

        return $genres;
    }

    public function genres_get_liste_non_autorise() {
        $sql = "SELECT * FROM Genre g
WHERE g.Autorise=0
ORDER BY g.Nom";


        $genres = $this->get_array_obj($sql);

        return $genres;
    }

    public function genres_autoriser_by_id($id) {
        $requete = "UPDATE Genre SET Autorise=1 WHERE ID=$id";

        $this->execute($requete);
    }

    public function genres_interdire_by_id($id) {
        $requete = "UPDATE Genre SET Autorise=0 WHERE ID=$id";

        $this->execute($requete);
    }

    public function genres_set_liste_blanche_by_id($id) {
        $requete = "UPDATE Genre SET ListeBlanche=1 WHERE ID=$id";

        $this->execute($requete);
    }

    public function genres_set_liste_non_blanche_by_id($id) {
        $requete = "UPDATE Genre SET ListeBlanche=0 WHERE ID=$id";

        $this->execute($requete);
    }

    public function get_annees_production($alias_annee_sortie = 'AnneeSortie') {
        $sql = "SELECT IFNULL( v.AnneeSortie,'--') as $alias_annee_sortie , COUNT(v.GenreID1) as Count
FROM  $this->table_details_sans_doublons v
GROUP BY v.AnneeSortie
ORDER BY v.AnneeSortie";
        $annees = $this->get_array_obj($sql);

        //var_dump($annees);
        return $annees;
    }

    function nb_films() {
        $nbFims = $this->get_value("SELECT COUNT(*) as Count
FROM  $this->table_details_sans_doublons v");
        return $nbFims;
    }

    function get_liste_titres() {
        $titres = $this->get_vector("SELECT DISTINCT d.Titre FROM $this->table_details_sans_doublons d
ORDER By  d.Titre   
");
        //var_dump($titres);
        return $titres;
    }

    
    private function get_cond_derniers_lus_ip(){
        if(config::tri_par_ip_derniers_lus()){
            $ip = Helper_system::nav_ip();
            return "AND dl.IP='$ip'";
        }
        return COND_JOINTURE_DEF;
    }
    
    function get_n_derniers_ajouts($n_derniers = 10) {

        //$ip = Helper_system::nav_ip();
        $jointure_par_ip = $this->get_cond_derniers_lus_ip();
        $sql = "SELECT v.*,  dl.DHCreation as DateLu, CASE  WHEN dl.DHCreation is not null THEN 1 ELSE 0 END  as Lu
        FROM $this->table_details_sans_doublons v
        LEFT JOIN DernierLu dl
        ON v.Filename=dl.Filename $jointure_par_ip
        ORDER BY v.DHCreation DESC
        LIMIT " . $n_derniers;


        //var_dump($sql);

        $result = $this->get_array_obj($sql);
        $myvod_liste = array();

        foreach ($result as $film) {
            $detail = new MyVOD_Details();
            $detail->InitFromStdClass($film);
            array_push($myvod_liste, $detail);
        }




        return $myvod_liste;
    }

    //returne true si la fiche existe
    function fiche_get_details($filename_OR_ID, MyVOD_Details &$detail) {


        $champ_where = "";
        if (ctype_digit($filename_OR_ID)) {
            $champ_where = 'ID';
        } else {
            $champ_where = 'l.[Filename]';
        }

        //$ip = Helper_system::nav_ip();
        $jointure_par_ip = $this->get_cond_derniers_lus_ip();
        $group_by_id='';
        if($jointure_par_ip==COND_JOINTURE_DEF){
            $jointure_par_ip = '';
            $group_by_id=COND_JOINTURE_DEF;
        }
        $sql = "SELECT l.*, MAX(dl.DHCreation) as DateLu, COUNT(dl.DHCreation) as Lu
FROM $this->table_details_sans_doublons l
LEFT JOIN DernierLu dl
ON l.Filename=dl.Filename $jointure_par_ip
WHERE $champ_where=" . sql::chaine_vers_sql($filename_OR_ID)."
    $group_by_id";

        //$detail=new MyCinemaDetails;
        //var_dump($filename_OR_ID);
        //var_dump($sql);
        $detailtmp = $this->get_obj($sql);
        
        //var_dump($detailtmp);

        if ($detailtmp->ID != null) {
            $detail = new MyVOD_Details();
            $detail->InitFromStdClass($detailtmp);

            //on récupère les fiches liées (table liaison)
            $fichiers_liees = array();
            self::liaison_get_liste_from_filename($detail->Filename, $fichiers_liees);

            foreach ($fichiers_liees as $f) {
                array_push($detail->t_liaisons, $f->Filename2);
            }

            //récupération des bandes annonces
            $detail->BandesAnnonces = array();
            $this->bande_annonce_get_liste_from_filename($detail->Filename, $detail->BandesAnnonces);
            $detail->TrierBandesAnnonces();
            //var_dump($detail->BandesAnnonces);
        }


        return ($detailtmp->ID != null);
    }

    function get_idx_suivant_by_ajout($id) {
        return self::_get_idx_generique_by_ajout($id, 1);
    }

    function get_idx_precedent_by_ajout($id) {
        return self::_get_idx_generique_by_ajout($id, -1);
    }

    private function _get_idx_generique_by_ajout($id, $sens) {
        $comp = $sens < 0 ? '<' : '>';
        $tri = $sens < 0 ? 'DESC' : 'ASC';
        $sql = "SELECT d.ID 
FROM $this->table_details_sans_doublons d
WHERE
d.DHCreation$comp(SELECT DHCreation FROM Video WHERE ID=$id )
ORDER BY d.DHCreation $tri
LIMIT 1";
        return $this->get_value($sql);
    }

    function get_idx_suivant($id) {

        return self::_get_idx_generique($id, 'idx_suivants');
    }

    function get_idx_precedent($id) {

        return self::_get_idx_generique($id, 'idx_precedents');
    }

    private function _get_idx_generique($id, $nom_tableau_session) {

        //si pas de session démarré on initialise les tableaux (ne devrait pas arriver)
        if ((session_status() != PHP_SESSION_ACTIVE) || (empty($_SESSION[$nom_tableau_session]))) {
            self::get_liste();
        }


        if (isset($_SESSION[$nom_tableau_session][$id])) {
            return $_SESSION[$nom_tableau_session][$id];
        }
        return false;
    }

    function get_liste($recherche = '', $rech_titre = false, $rech_acteurs = false, $rech_realisateur = false, $rech_synopsis = false, $rech_nom_fichier = false, $array_genre_filtre = false, $array_annee_filtre = false, $type_tri = 0, $sens_tri = '', $filtre_qualite = 0, $array_public_filtre = false, $filtre_recherche_parental = 0, $rech_genres_etendus = false, $array_nationalite_filtre = false, $taille_fichier_mini = 0, $taille_fichier_maxi = 0, $filtre_jamais_vu = 0) {

        $attach_cache = false;
        //démarre la session si pas démarrée
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        //$ip = Helper_system::nav_ip();
        $jointure_par_ip = $this->get_cond_derniers_lus_ip();
        $group_by_id='';
        if($jointure_par_ip==COND_JOINTURE_DEF){
            $jointure_par_ip = '';
            $group_by_id=COND_JOINTURE_DEF;
        }
        //construction de la requête en fonction des filtres
        $sqlSelect = "SELECT v.* ,  dl.DHCreation as DateLu, CASE  WHEN dl.DHCreation is not null THEN 1 ELSE 0 END  as Lu
    FROM(
        SELECT * FROM (
            SELECT * FROM $this->table_details_sans_doublons l
            %1
            ) v
        %3
        ORDER BY %2 )v
    LEFT JOIN DernierLu dl
    ON v.Filename=dl.Filename  $jointure_par_ip
        ";

        $where = '';


        //var_dump($rech_titre.'  '.$rech_acteurs.'  '.$rech_synopsis.'  '.$rech_nom_fichier);

        $filtre = trim($recherche);
        $where2 = '';

        if (($rech_titre != false || $rech_acteurs != false || $rech_realisateur != false ||  $rech_synopsis != false || $rech_nom_fichier != false) && (strlen($filtre) > 0)) {

            //il y a une recherche (filtre sur titre, acteurs ou synopsis)
            //recherche regexp
//                $ascii_string= strtr($string,utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ"),
//                                "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");
            //échapement des '.' qui est un joker en regexp
            $filtre = str_replace('.', '\.', $filtre);
            //idem pour les parenthèses
            $filtre = str_replace('(', '\(', $filtre);
            $filtre = str_replace(')', '\)', $filtre);
            $filtre = str_replace('?', '\?', $filtre);
            $filtre = str_replace('!', '\!', $filtre);

            $filtre = str_replace('*', '.*', $filtre);
            $filtre = str_replace(array('ae', 'AE'), '(Æ|ae)', $filtre);
            $filtre = str_replace(array('oe', 'OE'), '(œ|oe)', $filtre);
            $filtre = str_replace('a', '[ÀÁÂÃÄÅàáâãäåAa]', $filtre);
            $filtre = str_replace('o', '[ÒÓÔÕÖØòóôõöøOo]', $filtre);
            $filtre = str_replace('e', '[ÈÉÊËèéêëEe]', $filtre);
            $filtre = str_replace('c', '[ÇçCc]', $filtre);
            $filtre = str_replace('i', '[ÌÍÎÏìíîïIi]', $filtre);
            $filtre = str_replace('u', '[ÙÚÛÜùúûüUu]', $filtre);
            $filtre = str_replace('y', '[ÿYy]', $filtre);
            $filtre = str_replace('n', '[ÑñNn]', $filtre);

            $filtre = str_replace(' ', '.*', $filtre);


            // var_dump($recherche . ' - ' . $filtre);
            //échapement des simples cotes

            $filtre = trim(str_replace("'", "''", $filtre));  //sql::chaine_vers_sql($recherche);




            if ($rech_titre != false) {
                sql::add_where_OR($where2, " (Titre regexp '" . $filtre . "' 
				OR TitreOriginal regexp '" . $filtre . "')");
            }
            if ($rech_acteurs != false) {
                sql::add_where_OR($where2, "Acteurs regexp '" . $filtre . "'");
            }
            
            if ($rech_realisateur != false) {
                sql::add_where_OR($where2, "Realisateur regexp '" . $filtre . "'");
            }
            
            if ($rech_synopsis != false) {
                sql::add_where_OR($where2, "Synopsis regexp '" . $filtre . "'");
            }
            if ($rech_nom_fichier != false) {
                sql::add_where_OR($where2, "FileName regexp '" . $filtre . "'");
            }
            $where2 = str_replace("WHERE", "WHERE (", $where2) . ')';
            //var_dump($where2);
        }
        $sqlSelect = str_replace('%3', $where2, $sqlSelect);

        //les genres 
        //var_dump($array_genre_filtre);
        if ($array_genre_filtre !== false) {
            $where_genre = '(';
            foreach ($array_genre_filtre as $s) {

                if ($s === '') {
                    $where_genre = '( l.GenreNom1 IS NULL OR ';
                    break;
                }
            }
            $liste_genre = "'" . join("','", $array_genre_filtre) . "'";

            if ($rech_genres_etendus) {
                //en recherche genre étendu,on recherche aussi sur les gentres 2 et 3
                $where_genre.="l.GenreNom1 IN (" . $liste_genre . "))";
                $where_genre_tmp = $where_genre;
                for ($i = 2; $i <= 3; $i++) {
                    $where_genre.=' OR ' . str_replace('GenreNom1', 'GenreNom' . $i, $where_genre_tmp);
                }
            } else {
                $where_genre.="l.GenreNom1 IN (" . $liste_genre . "))";
            }



            sql::add_where_AND($where, $where_genre);
        }






        //les années de sortie    
        if ($array_annee_filtre !== false) {
            $where_annee = '(';

            //detection si on a une valeure non entier
            //c'est dans le cas qu'on a '' ou '--' qui sera interprété par is null
            foreach ($array_annee_filtre as $s) {

                if (is_numeric($s) === false) {
                    $where_annee = '( l.AnneeSortie IS NULL OR ';
                    break;
                }
            }

            $liste_annee = "'" . join("','", $array_annee_filtre) . "'";
            $where_annee.="l.AnneeSortie IN (" . $liste_annee . ") )";

            sql::add_where_AND($where, $where_annee);
        }

        //le type de publique
        if ($array_public_filtre !== false) {
            $liste_public = "'" . join("','", $array_public_filtre) . "'";
            $where_public = 'l.TypePublic IN (' . $liste_public . ')';

            sql::add_where_AND($where, $where_public);
        }

        //la nationalité
        if ($array_nationalite_filtre !== false) {
            $where_nationalite = '';
            foreach ($array_nationalite_filtre as $n) {
                if ($n == '' || $n == '--') {
                    sql::add_where_OR($where_nationalite, "l.Nationalite = ''", '');
                } else {
                    $n = str_replace("'", "''", $n);
                    sql::add_where_OR($where_nationalite, "l.Nationalite LIKE '%$n%'", '');
                }
            }
            $where_nationalite = "($where_nationalite)";
            sql::add_where_AND($where, $where_nationalite);
        }


        //Qualité (SD ou HD)
        if ($filtre_qualite != 0) {
            //define('FILTRE_QUALITE_TOUS',0);
            //define('FILTRE_QUALITE_SD',1);
            //define('FILTRE_QUALITE_HD',2);
            if ($filtre_qualite == 1) {
                sql::add_where_AND($where, "l.HD720=0 AND l.HD1080=0");
            }
            if ($filtre_qualite == 2) {
                sql::add_where_AND($where, "(l.HD720!=0 OR l.HD1080!=0)");
            }
        }

        //taille du fichier (fait appel à cache.db
        //3 cas de figures
        // - min renseigné
        // - max renseigné
        // - min et max renseignés

        if ($taille_fichier_mini > 0 || $taille_fichier_maxi > 0) {
            $attach_cache = true;
            $where_taille = '(l.Filename IN (
        SELECT f.file_name 
        FROM cache.file f' . "\n";
            $t_min_o = $taille_fichier_mini * 1024 * 1024;
            if ($taille_fichier_mini >= 1000) {
                $t_min_o = $t_min_o / 1000 * 1024;
            }
            $t_max_o = $taille_fichier_maxi * 1024 * 1024;
            if ($taille_fichier_maxi >= 1000) {
                $t_max_o = $t_max_o / 1000 * 1024;
            }



            if ($taille_fichier_maxi == 0) {
                //pas de taille max
                $where_taille.=" WHERE f.size > $t_min_o";
            } else {
                //taille min et max renseignés
                //vérifie l'ordre
                if ($taille_fichier_mini > $taille_fichier_maxi) {
                    $t_tmp = $t_min_o;
                    $t_min_o = $t_max_o;
                    $t_max_o = $t_tmp;
                }
                $where_taille.=" WHERE f.size BETWEEN $t_min_o AND $t_max_o";
            }

            $where_taille.="\n))";

            sql::add_where_AND($where, $where_taille);

            //var_dump($taille_fichier_mini);
            //var_dump($taille_fichier_maxi);
        }



        //Controle parental
        if ($filtre_recherche_parental != 0) {
            //define('FILTRE_RECHERCHE_PARENTAL_TOUS', 0);
            //define('FILTRE_RECHERCHE_PARENTAL_NON', 1);
            //define('FILTRE_RECHERCHE_PARENTAL_OUI', 2);

            if ($filtre_recherche_parental == 1) {
                sql::add_where_AND($where, "l.Autorise=0");
            }
            if ($filtre_recherche_parental == 2) {
                sql::add_where_AND($where, "l.Autorise!=0");
            }
        }

        $sqlSelect = str_replace('%1', $where, $sqlSelect);

        //tri par titre par défaut
        if ($sens_tri == '') {
            //$sens_tri = $type_tri > 0 ? 'DESC' : 'ASC';
            $sens_tri = ($type_tri > 0) && ($type_tri < 4) ? 'DESC' : 'ASC';
        }
        $order_by = "UPPER(Titre) $sens_tri, UPPER(Filename) $sens_tri";
        //tri par ajout (donc par rowid)
        if ($type_tri == TYPE_TRI_D_AJOUT) {
            //ORDER BY l.DHCreation DESC 
            $order_by = "DHCreation $sens_tri";
        }
        //tri par date de sortie
        if ($type_tri == TYPE_TRI_D_SORTIE) {
            $order_by = "AnneeSortie $sens_tri, DateSortie $sens_tri";
        }
        //tri par notation
        if ($type_tri == TYPE_TRI_NOTATION) {
            $order_by = " IFNULL((NotePresse+ NoteSpec)/2,(IFNULL(NotePresse, NoteSpec))) $sens_tri";
        }
        //tri par durée
        if ($type_tri == TYPE_TRI_DUREE) {
            $order_by = "DureeSec $sens_tri";
        }
        //tri par date de sortie ASC
        if ($type_tri == TYPE_TRI_D_SORTIE_ASC) {
            $order_by = "AnneeSortie ASC, DateSortie ASC";
        }
        //var_dump($order_by);

        $sqlSelect = str_replace('%2', $order_by, $sqlSelect);


        /*filtre sur jamais lu*/
        if ($filtre_jamais_vu != 0) {
            $where_jamis_vu = 'dl.Filename IS NULL';
            $sqlSelect.=" 
                  WHERE $where_jamis_vu ";
            //sql::add_where_AND($where, $where_jamis_vu);
        }


        //lance le attach cache si besoin car les tailles des fichiers sont dans le fichier de cache
        if ($attach_cache) {
            //var_dump('attach');
            //TODO : trouver le chemin de cache.db
            $this->execute("ATTACH DATABASE 'C:\wamp\www\MyVOD\data\cache.db' as cache");
        }
        
        $sqlSelect.="\n".$group_by_id;
        
        //pour débugger la requête
        /*
        echo('<pre>');
        print_r($sqlSelect);
        echo('</pre>');
        */
    
        //récupération des résultats
        $result = $this->get_array_obj($sqlSelect);

        //on met le résultat dans des tableaux (film précédent et suivant)

        $precedents = array();
        $suivants = array();
        $idPrec = null;

        $myvod_liste = array();

        foreach ($result as $films) {
            //récupère l'id actuel
            $id = $films->ID;

            if (!empty($idPrec)) {
                $precedents[$id] = $idPrec;
                $suivants[$idPrec] = $id;
            }
            //on met le précédent pour le prochain tour de boucle
            $idPrec = $id;

            //converti le standard class en myvod
            $detail = new MyVOD_Details();
            $detail->InitFromStdClass($films);
            array_push($myvod_liste, $detail);
        }

        //on enregistre les 2 tableaux en session
        $_SESSION['idx_precedents'] = $precedents;
        $_SESSION['idx_suivants'] = $suivants;
        //var_dump($sqlSelect);
         //var_dump($myvod_liste);

        return $myvod_liste;
    }

    public function get_genres_nom_et_id() {

        $sql = 'SELECT g.Nom,g.ID FROM Genre g';
        $res = $this->get_array_arr($sql);
        $genres = array();
        foreach ($res as $r) {
            $genres[$r[0]] = $r[1];
        }
        return $genres;
    }

    function get_liste_type_film() {
        $result = $this->get_vector('SELECT DISTINCT v.TypeFilm FROM Video v
ORDER BY UPPER(v.TypeFilm)');
        return $result;
    }

    function type_public_get_liste_autorise() {
        $result = $this->get_vector('SELECT Nom FROM TypePublicAutorise ORDER BY Nom');
        return $result;
    }

    function type_public_get_liste_non_autorise() {
        $result = $this->get_vector('SELECT DISTINCT v.TypePublic FROM Video v
WHERE v.TypePublic NOT IN(
SELECT Nom FROM TypePublicAutorise
)
ORDER BY v.TypePublic');
        return $result;
    }

    function type_public_ajouter_autorisation($type_publique) {


        $requete = "INSERT OR IGNORE INTO TypePublicAutorise (Nom)
                   VALUES (" . sql::chaine_vers_sql($type_publique, "''") . ")";


        $this->execute($requete);
    }

    function type_public_enlever_autorisation($type_publique) {

        $sqlDelete = 'DELETE FROM "TypePublicAutorise"
                WHERE Nom LIKE ' . sql::chaine_vers_sql($type_publique, "''");

        $this->execute($sqlDelete);
    }

    function get_liste_nationalites() {
        $result = $this->get_vector('SELECT DISTINCT v.Nationalite FROM ' . $this->table_details_sans_doublons . ' v
ORDER BY UPPER(v.Nationalite)');
        return $result;
    }

    function get_liste_nationalites_uniques() {
        $r1 = $this->get_liste_nationalites();
        $r2 = array();
        //pour chaques éléments de r1, on remplir r2
        foreach ($r1 as $r) {
            //découpe les chaines quand il y a plusieurs nationnalités
            $r3 = explode(",", $r);
            foreach ($r3 as $t) {
                if ($t == '') {
                    $t = '--';
                }
                array_push($r2, trim($t));
            }
        }
        //tri et suppression des doublons
        $r2 = array_unique($r2);
        sort($r2);
        return $r2;
    }

    function get_publictypes($alias_count = 'nb') {


        $result = $this->get_array_obj("SELECT DISTINCT l.TypePublic as Nom, COUNT(*) as $alias_count
FROM $this->table_details_sans_doublons l
GROUP BY  UPPER(l.TypePublic)
ORDER BY  UPPER(l.TypePublic)");

        return $result;
    }

    public function fiche_enregistrer(MyVOD_Details $myvod_detail) {
        //var_dump('ENREGISTRE');
        //enregistre les genres

        static $genres;
        if ($genres == null) {
            $genres = self::get_genres_nom_et_id();
            //var_dump($genres);
        }


        //test du genre s'il existe pour récupérer le numéro
        for ($i = 1; $i <= 3; $i++) {
            $genre_nom_tmp = $myvod_detail->{"GenreNom$i"};
            //var_dump($genre_nom_tmp);
            if ($genre_nom_tmp != '') {
                //test si le genre existe
                if (isset($genres[$genre_nom_tmp]) == false) {
                    //$this->ajouter_genre($nom_genre)
                    //var_dump("ajout de " . $myvod_detail->{"GenreNom$i"});
                    $this->genre_ajouter($genre_nom_tmp);
                    $genres = self::get_genres_nom_et_id();
                }

                $myvod_detail->{"GenreID$i"} = $genres[$genre_nom_tmp];
            }
        }

        //enregistrement de la fiche
        $myvod_detail->BandeAnnonceUrl = trim($myvod_detail->BandeAnnonceUrl);
        $myvod_detail->BandeAnnonceEmbed = trim($myvod_detail->BandeAnnonceEmbed);

        $sql = sprintf("INSERT OR IGNORE INTO Video (
            ID,
Filename,
TitleKey,Titre,TitreOriginal,
TypeFilm,
DateSortie,AnneeSortie,
DureeSec,Nationalite,
TypePublic,GenreID1,GenreID2,GenreID3,
Realisateur,Acteurs,
NotePresse,NoteSpec,
Synopsis,
Affiche,MovieLink,
NumFicheAllocine,NumFicheDvdFr,
UrlImageSource,BandeAnnonceUrl,
BandeAnnonceCode,BandeAnnonceEmbed,
Remarques,
DHCreation,
HD720,HD1080,
MessageModif,
NumFicheTmdb,
NbSaisons,NbEpisodes)
VALUES(
(SELECT ID FROM Video WHERE Filename = %s),
%s,
%s,%s,%s,
%s,%s,
%s,%s,
%s,%s,%s,
%s,%s,
%s,%s,
%s,
%s,%s,
%s,%s,
%s,%s,
%s,%s,
%s,%s,
%s,
ifnull((SELECT DHCreation FROM Video WHERE Filename = %s),datetime('now')),
%s,%s,
%s,
%s,
%s,%s
)", sql::chaine_vers_sql($myvod_detail->Filename), sql::chaine_vers_sql($myvod_detail->Filename), sql::chaine_vers_sql($myvod_detail->TitleKey, "''"), sql::chaine_vers_sql($myvod_detail->Titre, "''"), sql::chaine_vers_sql($myvod_detail->TitreOriginal, "''"), sql::chaine_vers_sql($myvod_detail->TypeFilm, "''"), sql::date_vers_sql($myvod_detail->DateSortie), sql::entier_vers_sql($myvod_detail->AnneeSortie), sql::entier_vers_sql($myvod_detail->DureeSec), sql::chaine_vers_sql($myvod_detail->Nationalite, "''"), sql::chaine_vers_sql($myvod_detail->TypePublic, "'Tous publics'"), sql::entier_vers_sql($myvod_detail->GenreID1), sql::entier_vers_sql($myvod_detail->GenreID2), sql::entier_vers_sql($myvod_detail->GenreID3), sql::chaine_vers_sql($myvod_detail->Realisateur, "''"), sql::chaine_vers_sql($myvod_detail->Acteurs, "''"), sql::float_vers_sql($myvod_detail->NotePresse), sql::float_vers_sql($myvod_detail->NoteSpec), sql::chaine_vers_sql($myvod_detail->Synopsis, "''"), sql::chaine_vers_sql($myvod_detail->Affiche, "''"), sql::chaine_vers_sql($myvod_detail->MovieLink, "''"), sql::chaine_vers_sql($myvod_detail->NumFicheAllocine), sql::chaine_vers_sql($myvod_detail->NumFicheDvdFr), sql::chaine_vers_sql($myvod_detail->UrlImageSource, "''"), sql::chaine_vers_sql($myvod_detail->BandeAnnonceUrl, "''"), sql::chaine_vers_sql($myvod_detail->BandeAnnonceCode, "''"), sql::chaine_vers_sql($myvod_detail->BandeAnnonceEmbed, "''"), sql::chaine_vers_sql($myvod_detail->Remarques, "''"), sql::chaine_vers_sql($myvod_detail->Filename), $myvod_detail->HD720, $myvod_detail->HD1080, 
                sql::chaine_vers_sql($myvod_detail->MessageModif, "''"),sql::chaine_vers_sql($myvod_detail->NumFicheTmdb),
                sql::entier_vers_sql($myvod_detail->NbSaisons), sql::entier_vers_sql($myvod_detail->NbEpisodes));


//update date heure création pour la 1ere fois
        self::execute($sql);


        // UPDATE si l'enregistrement existe
        $sql = "UPDATE Video 
    SET
            TitleKey = " . sql::chaine_vers_sql($myvod_detail->TitleKey, "''") . ",
            Titre = " . sql::chaine_vers_sql($myvod_detail->Titre, "''") . ",
            TitreOriginal = " . sql::chaine_vers_sql($myvod_detail->TitreOriginal, "''") . ",
            TypeFilm = " . sql::chaine_vers_sql($myvod_detail->TypeFilm, "''") . ",
            DateSortie = " . sql::date_vers_sql($myvod_detail->DateSortie) . ",
            AnneeSortie = " . sql::entier_vers_sql($myvod_detail->AnneeSortie) . ",
            DureeSec = " . sql::entier_vers_sql($myvod_detail->DureeSec) . ",
            Nationalite = " . sql::chaine_vers_sql($myvod_detail->Nationalite, "''") . ",
            TypePublic = " . sql::chaine_vers_sql($myvod_detail->TypePublic, "'Tous publics'") . ",
            GenreID1 = " . sql::entier_vers_sql($myvod_detail->GenreID1) . ",
            GenreID2 = " . sql::entier_vers_sql($myvod_detail->GenreID2) . ",
            GenreID3 = " . sql::entier_vers_sql($myvod_detail->GenreID3) . ",
            Realisateur = " . sql::chaine_vers_sql($myvod_detail->Realisateur, "''") . ",
            Acteurs = " . sql::chaine_vers_sql($myvod_detail->Acteurs, "''") . ",
            NotePresse = " . sql::float_vers_sql($myvod_detail->NotePresse) . ",
            NoteSpec = " . sql::float_vers_sql($myvod_detail->NoteSpec) . ",
            Synopsis = " . sql::chaine_vers_sql($myvod_detail->Synopsis, "''") . ",
            Affiche = " . sql::chaine_vers_sql($myvod_detail->Affiche, "''") . ",
            MovieLink = " . sql::chaine_vers_sql($myvod_detail->MovieLink, "''") . ",
            NumFicheAllocine = " . sql::chaine_vers_sql($myvod_detail->NumFicheAllocine) . ",
            NumFicheDvdFr = " . sql::chaine_vers_sql($myvod_detail->NumFicheDvdFr) . ",
            UrlImageSource = " . sql::chaine_vers_sql($myvod_detail->UrlImageSource, "''") . ",
            BandeAnnonceUrl = " . sql::chaine_vers_sql($myvod_detail->BandeAnnonceUrl, "''") . ",
            BandeAnnonceCode = " . sql::chaine_vers_sql($myvod_detail->BandeAnnonceCode, "''") . ",
            BandeAnnonceEmbed = " . sql::chaine_vers_sql($myvod_detail->BandeAnnonceEmbed, "''") . ",
            Remarques = " . sql::chaine_vers_sql($myvod_detail->Remarques, "''") . ",
            HD720 = " . $myvod_detail->HD720 . ",
            HD1080 = " . $myvod_detail->HD1080 . ",
            MessageModif = " . sql::chaine_vers_sql($myvod_detail->MessageModif, "''") . ",
            NumFicheTmdb = " . sql::chaine_vers_sql($myvod_detail->NumFicheTmdb) . ",
            NbSaisons = " . sql::entier_vers_sql($myvod_detail->NbSaisons) . ",
            NbEpisodes = " . sql::entier_vers_sql($myvod_detail->NbEpisodes) . ",
            DHModification = datetime('now', 'localtime')
    WHERE Filename = " . sql::chaine_vers_sql($myvod_detail->Filename) . ";";

        self::execute($sql);

/*
        var_dump($myvod_detail->NumFicheTmdb);
        var_dump($sql);
        exit();
        */
        //purge des bandes annonces dans la bdd (évite si le nombre diminue après une recherche de se retrouver avec une ba qui ne va pas)
        //LC : 14/08/2018
        $this->bande_annonce_supprimer_by_filename($myvod_detail->Filename);

        //enregistre les bandes annonces
        $ba = new BandeAnnonce(); //pour autocompletion
        foreach ($myvod_detail->BandesAnnonces as $ba) {
            //if($ba->id==''){
            //sécu : maj du filename
            $ba->filename = $myvod_detail->Filename;

            //test s'il existe (LC : 14/08/2018 : plus besoin car purgé en amont)
            /*
              if ($this->bande_annonce_existe($ba->filename, $ba->code) == false) {
              $this->bande_annonce_ajouter($ba);
              }
             */
            $this->bande_annonce_ajouter($ba);
            //}
        }
    }

    function update_filename($old_filename, $new_filename, $update_dh_creation = false) {
        $set_dh_cration = '';
        if ($update_dh_creation) {
            $set_dh_cration = ", DHCreation=datetime('now', 'localtime') ";
        }

        $sql = "UPDATE Video 
          SET Filename=" . sql::chaine_vers_sql($new_filename) . $set_dh_cration .
                "  WHERE Filename=" . sql::chaine_vers_sql($old_filename);
        //LC 11/10/2018 : pas besoin de mettre à jour la date heure modification (on verra + tard)
        //, DHModification = datetime('now', 'localtime')
        self::execute($sql);
    }

    function update_affiche($old_affiche, $new_affiche) {


        $sql = "UPDATE Video 
          SET Affiche=" . sql::chaine_vers_sql($new_affiche) .
                " WHERE Affiche=" . sql::chaine_vers_sql($old_affiche);


        self::execute($sql);
    }

    function update_affiche_by_id($filename_OR_ID, $nom_affiche, $url_affiche) {
        $champ_where = "";
        if (ctype_digit($filename_OR_ID)) {
            $champ_where = 'rowid';
        } else {
            $champ_where = 'Filename';
        }

        $sql = "UPDATE  Video 
          SET Affiche=" . sql::chaine_vers_sql($nom_affiche) .
                ', UrlImageSource=' . sql::chaine_vers_sql($url_affiche) .
                " WHERE $champ_where=" . sql::chaine_vers_sql($filename_OR_ID);


        self::execute($sql);
    }

    function get_liste_doublons_sur_titre() {
        /* //requete 1ere version (pas de filtre sur les films en 3 cd)
          $sql_select = "SELECT * FROM Video v
          WHERE v.Titre IN (
          SELECT v1.Titre FROM Video v1
          WHERE v1.Filename NOT IN (
          SELECT l.Filename1 FROM Liaison l
          )
          GROUP BY v1.Titre HAVING COUNT(*)>1
          ORDER BY v1.Titre
          )ORDER BY v.Titre, v.Filename";
         */
        /*
        //requete case sensitive
        $sql_select = "SELECT * FROM Video v
/*récup des titres* /
WHERE v.Titre IN (
    SELECT v1.Titre FROM Video v1
    WHERE v1.Filename NOT IN (
        SELECT l.Filename2 FROM Liaison l
    )
GROUP BY v1.Titre HAVING COUNT(*)>1
-- ORDER BY v1.Titre
)
/*Filtre de nouveau pour les films en 3CD (rare)* /
AND v.Filename NOT IN (SELECT l.Filename2 FROM Liaison l)
ORDER BY v.Titre, v.Filename";
*/
        //requete case insensitive (LC: 27/11/2018)
        $sql_select = "SELECT * FROM Video v
/*récup des titres*/
WHERE UPPER(v.Titre) IN (
    SELECT UPPER(v1.Titre) FROM Video v1
    WHERE v1.Filename NOT IN (
        SELECT l.Filename2 FROM Liaison l
    )
GROUP BY UPPER(v1.Titre) HAVING COUNT(*)>1
-- ORDER BY v1.Titre
)
/*Filtre de nouveau pour les films en 3CD (rare)*/
AND v.Filename NOT IN (SELECT l.Filename2 FROM Liaison l)
ORDER BY v.Titre, v.Filename";
        
        
        
        
        $result = $this->get_array_obj($sql_select);

        $myvod_liste = array();
        foreach ($result as $films) {
            //converti le standard class en myvod
            $detail = new MyVOD_Details();
            $detail->InitFromStdClass($films);
            array_push($myvod_liste, $detail);
        }


        return($myvod_liste);
    }

    function get_liste_doublons_sur_movieLink() {
        $sql_select = "SELECT * FROM Video v 
WHERE v.MovieLink IN (
     SELECT  v2.MovieLink
    FROM  Video v2
    WHERE  v2.MovieLink IS NOT NULL
    GROUP BY v2.MovieLink
    HAVING COUNT(*)>1
)
AND length(v.MovieLink)>0";



        $result = $this->get_array_obj($sql_select);

        return($result);
    }

    function fiche_supprimer($index) {

        $sql = "DELETE FROM Video 
            WHERE ID = $index";
        $this->execute($sql);
    }

    function liste_ajouter($nom_fichier) {
        //rq : sécu (pense bête) test si le fichier n'est pas ds la black liste
        if (self::blacklist_existe($nom_fichier) == FALSE) {

            $new_film = new MyVOD_Details();
            $new_film->Filename = $nom_fichier;
            $new_film->Titre = $nom_fichier;
            $new_film->TitleKey = $nom_fichier;
            $new_film->TitreOriginal = $nom_fichier;

            $this->fiche_enregistrer($new_film);
        }
    }

    /**
     * 
     * Gestion de la blackliste (Ignore)
     * 
     * 
     * 
     */
    function blacklist_existe($nom_fichier) {
        $requete = 'SELECT COUNT(*) FROM "Ignore" 
                WHERE Filename LIKE ' .
                sql::chaine_vers_sql($nom_fichier);


        $n = $this->get_value($requete);

        return ($n != 0);
    }

    function blacklist_ajouter($nom_fichier) {
        /*
          $requete = 'SELECT COUNT(*) FROM "Ignore"
          WHERE Filename LIKE '."'$nom_fichier'";
          $n = $this->get_value( $requete);
          //insertion s'il n'existe pas déjà
          if ($n == 0) {
         */
        if ($this->blacklist_existe($nom_fichier) == false) {
            $requete = "INSERT INTO [Ignore] (Filename)
                   VALUES (" . sql::chaine_vers_sql($nom_fichier) . ")";
            //var_dump($requete);
            $this->execute($requete);
        }
    }

    function blacklist_enlever($nom_fichier) {

        $sqlDelete = 'DELETE FROM "Ignore"
                WHERE Filename LIKE ' . sql::chaine_vers_sql($nom_fichier);

        $this->execute($sqlDelete);
    }

    function blacklist_get_liste_fichiers() {


        $sqlSelect = 'SELECT i.Filename FROM "Ignore" i 
ORDER BY UPPER(i.Filename)';

        $result = $this->get_vector($sqlSelect);
        return $result;
    }

    /*
     * 
     * Gestion des liaisons
     * 
     */

    function liaison_ajouter(LiaisonFichier $liaison) {
        $f1 = sql::chaine_vers_sql($liaison->Filename1);
        $f2 = sql::chaine_vers_sql($liaison->Filename2);
        $sql = "INSERT INTO Liaison (Filename1, Filename2)
                VALUES ($f1,$f2)";


        $this->execute($sql);
    }

    function liaison_supprimer(LiaisonFichier $liaison) {
        $f1 = sql::chaine_vers_sql($liaison->Filename1);
        $f2 = sql::chaine_vers_sql($liaison->Filename2);
        $sql = "DELETE FROM Liaison 
            WHERE Filename1 = $f1
                AND Filename2 = $f2";
        $this->execute($sql);
    }

    function liaison_get_liste_from_filename($filename, &$liaisons) {
        $liaisons = array(); //new LiaisonFichier();

        $sql = "SELECT l.Filename2 FROM Liaison l 
            WHERE l.Filename1 like " . sql::chaine_vers_sql($filename) . "
            ORDER BY  l.Filename2";


        $liaisons = $this->get_array_obj($sql);
    }

    function liaison_get_liste(&$liaisons) {
        $liaisons = array(); //new LiaisonFichier();

        $sql = "SELECT Liaison.*,v.Titre, v.Affiche  
            FROM Liaison  
            LEFT JOIN Video v ON liaison.Filename1 = v.Filename
            ORDER BY Filename1 , Filename2";

        $liaisons = $this->get_array_obj($sql);
    }

    /*
     * 
     * Gestion des dernières vidéo visionnées
     * 
     */

    function dernier_lu_ajouter($filename, $ip_navigateur) {
        /*
          $this->execute("DELETE FROM DernierLu
          WHERE IP='$ip_navigateur' AND Filename = '$filename'");
         */
        try {
            //purge si on a cliqué plusieurs fois de suite (1 heure, ca fait de la marge).
            $this->execute("DELETE
                FROM HistoLu 
                WHERE DHCreation >= datetime('now', 'localtime', '-1 hours')
                AND IP=" . sql::chaine_vers_sql($ip_navigateur) . "
                AND FileName=" . sql::chaine_vers_sql($filename));



            $this->execute("INSERT INTO HistoLu (IP, Filename)
                VALUES(" . sql::chaine_vers_sql($ip_navigateur) . "," . sql::chaine_vers_sql($filename) . ")");
        } catch (Exception $e) {
            //on ne fait rien car un fichier qui est lu dans la même seconde n'est pas considéré
            //comme nouvellement lu
        }
    }

    function dernier_lu_get_liste($ip_navigateur='', $nb = 0) {
        //var_dump($nb);
        $filtre_ip = '';
        if(strlen($ip_navigateur)){
            $filtre_ip = " AND dl.IP = '$ip_navigateur'";
        }
        $sql = "SELECT d.* , dl.DHCreation as DateLu, 1 as Lu
            FROM $this->table_details_sans_doublons d
            INNER JOIN DernierLu dl ON d.Filename=dl.Filename
            WHERE 1 $filtre_ip
            GROUP BY d.ID
            ORDER BY dl.DHCreation DESC";
        //var_dump($nb);
        if ($nb > 0) {
            $sql.="\n" . "LIMIT $nb";
        }
        //var_dump($sql);

        $result = $this->get_array_obj($sql);
        $derniers_lus = array();
        foreach ($result as $films) {


            //converti le standard class en myvod
            $detail = new MyVOD_Details();
            $detail->InitFromStdClass($films);
            array_push($derniers_lus, $detail);
        }
        //var_dump($derniers_lus);
        return $derniers_lus;
    }

    /**
     * 
     * Ouverture de la base de données. Mise à jour initiale
     * 
     */
    public function maj_initiale() {
        require_once 'myvod_db_maj.php';
        parent::begin_trans();
        parent::maj_initiale();
        parent::commit();
        
        //MyVOD_DB_MAJ::maj_initiale($this);
      
    }

    function __construct() {
        $this->fic_sqlite = self::repertoire_data() . '/data.db';
        $this->open();
        $this->maj_suivantes();
        $this->table_details_sans_doublons = self::$vue_table_video;
    }

    //2015-03-06 : tranféré dans sqlite_db
    /*
      public  function open() {
      //$this->fic_sqlite = dirname(dirname(dirname(__DIR__))) . '/data/data.db';
      $this->fic_sqlite = self::repertoire_data() . '/data.db';
      $fichier_exists = 0;
      //c'est la 1ère fois qu'on ouvre
      if ($this->db_handle == null) {
      //test si le fichier existe
      $fichier_exists = file_exists($this->fic_sqlite);
      //$this->cnx = $this->open($this->fichier_db);
      parent::open();
      //fait la mise à jour initiale si le fichier n'existait pas
      if ($fichier_exists == false) {
      require_once 'myvod_db_maj.php';

      MyVOD_DB_MAJ::maj_initiale($this);
      }
      }
      }
     */
}

//////////////////////////
/*
$MyVOD_DB =new MyVOD_DB();



$ret=new MyVOD_Details();
var_dump($MyVOD_DB-> get_liste_doublons_sur_movieLink());
 var_dump($ret);
 */