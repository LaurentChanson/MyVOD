<?php

class Helper_misc{
    static function chaine_pour_html($chaine_a_convertir,$replace_rc='&NewLine;'){
        $s=$chaine_a_convertir;
        
        $s=str_replace("\\", "\\\\", $s);
        
        $s=str_replace("\"", "&quot;", $s);
    
        $s=str_replace("\r\n", "\n", $s);
        $s=str_replace(array("\r","\n"), $replace_rc, $s);
        
        return ($s);
        
    }
    
    static function chaine_pour_js($chaine_a_convertir){
        $s=self::chaine_pour_html($chaine_a_convertir);
        
        $s=str_replace("'", "\'", $s);
        $s=str_replace('&NewLine;', '\n', $s);
        
        return ($s);
        
    }
    
}



class Helper_var {

    static function set_session($nom_variable, $valeur){
        $_SESSION[$nom_variable] = $valeur;
    }
    
    
    static function session_var($nom_variable, $valeur_defaut) {
        $ret = isset($_SESSION[$nom_variable]) ? $_SESSION[$nom_variable] : $valeur_defaut;
        return $ret;
    }

    static function get_var($nom_variable, $valeur_defaut) {

        //vérifie si le get existe et écrase
        return isset($_GET[$nom_variable]) ? urldecode($_GET[$nom_variable]) : $valeur_defaut;
    }

    static function post_var($nom_variable, $valeur_defaut) {

        //vérifie si le get existe et écrase
        return isset($_POST[$nom_variable]) ? $_POST[$nom_variable] : $valeur_defaut;
    }

    static function post_or_get_var($nom_variable, $valeur_defaut) {

        //vérifie si le get existe et écrase
        if (isset($_POST[$nom_variable])) {
            return $_POST[$nom_variable];
        }
        return self::get_var($nom_variable, $valeur_defaut);
    }

    static function post_get_or_session($nom_variable, $valeur_defaut) {

        //vérifie si la session existe
        $ret = isset($_SESSION[$nom_variable]) ? $_SESSION[$nom_variable] : $valeur_defaut;


        //vérifie si le post existe et écrase
        if (isset($_POST[$nom_variable])) {
            $ret = $_POST[$nom_variable];
            //vérifie si le get existe et écrase
        } else if (isset($_GET[$nom_variable])) {
            $ret = $_GET[$nom_variable];
        }



        //mise en session
        $_SESSION[$nom_variable] = $ret;
        //on renvoie le résultat
        return $ret;
    }

    static function post_or_session($nom_variable, $valeur_defaut, $debug = false) {

        //vérifie si la session existe
        $ret = isset($_SESSION[$nom_variable]) ? $_SESSION[$nom_variable] : $valeur_defaut;

        if ($debug)
            var_dump($ret);


        //vérifie si le post existe et écrase
        if (isset($_POST[$nom_variable])) {
            $ret = $_POST[$nom_variable];
            if ($debug)
                var_dump($ret);
        }
        //mise en session
        $_SESSION[$nom_variable] = $ret;
        //on renvoie le résultat
        return $ret;
    }

    static function get_or_session($nom_variable, $valeur_defaut) {
        //vérifie si la session existe
        $ret = isset($_SESSION[$nom_variable]) ? $_SESSION[$nom_variable] : $valeur_defaut;

        //vérifie si le get existe et écrase
        if (isset($_GET[$nom_variable])) {
            $ret = urldecode($_GET[$nom_variable]);
        }
        //mise en session
        $_SESSION[$nom_variable] = $ret;
        //on renvoie le résultat
        return $ret;
    }

}

class Helper_redirection {

    static $DERNIERE_PAGE = 'derniere_page_generee';

    static function referer_different_page_actuelle(){
        
        return(strpos(Helper_redirection::get_referer(),Helper_redirection::get_page_courante())===false);

    }
    
    
    
    static function get_page_courante() {
        return $_SERVER['PHP_SELF'];
    }

    static function get_referer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }

    static function set_derniere_page() {

        $derniere_page = self::get_referer();
        $page_courante = self::get_page_courante();

        //on stocke le nom de la page courante quand il y a eu un changement de page
        if (strpos($derniere_page, $page_courante) === false) {
            //var_dump("session de ".$derniere_page." courant : $page_courante");
            $_SESSION[self::$DERNIERE_PAGE] = $derniere_page;
        }
        // var_dump($derniere_page);
    }

    static function get_derniere_page() {
        //return isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
        //retourne celui en session (socké par set_derniere_page) ou
        //le "referer"
        $derniere_page= Helper_var::session_var(self::$DERNIERE_PAGE, self::get_referer());
        //si détail modif et la page courante est la même, on va vers détails.php
        //TODO
        //var_dump($derniere_page);
        return $derniere_page;
        
        
        
    }

    static function redirige_derniere_page() {
        if (strlen(self::get_derniere_page()) > 0) {
            self::redirige(self::get_derniere_page());
        }
    }

    static function redirige($page) {
        ob_clean();
        header('Location: ' . $page);
        exit;
    }

}

class Helper_system {
    //page courante sans extension
    static function page_courante_sans_ext(){
        return basename($_SERVER['SCRIPT_NAME'],'.php');
    }
    
    
    //IP du navigateur
    static function nav_ip() {
        $ip = $_SERVER['REMOTE_ADDR'];
        //le laisser car on se retrouvait avec des 127.0.0.1 et ::1 dans la table HistoLu
        if($ip=='::1'){
            $ip='127.0.0.1';
        }
        return $ip;
    }

    //IP du serveur
    static function serv_ip() {
        return $_SERVER['SERVER_ADDR'];
        ;
    }

    //en test sur la page details
    static function temps_ecoule($texte_en_plus = "") {
        global $startTime;
        if(isset($startTime)==false){
            $startTime=$_SERVER['REQUEST_TIME'];
        }
        //$start = $_SERVER['REQUEST_TIME'];
        $start=$startTime;
        $endTime = microtime(true);
        $elapsed = $endTime - $start;
        $s = number_format($elapsed * 1000, 0, '.', ' ');
        var_dump("$s ms : $texte_en_plus");
    }

    static function curl_actif() {
        //permet de savoir curl est chargé ou pas
        return (extension_loaded('curl'));
    }
    
    static function openssl_actif() {
        //permet de savoir curl est chargé ou pas
        return (extension_loaded('openssl'));
    }
    
    //http://www.killersites.com/community/index.php?/topic/2562-php-to-detect-browser-and-operating-system/

    static function user_agent() {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    static function user_agent_contient($texte){
        return strpos(self::user_agent(), $texte) ? true : false;
    }
    
    static function nav_OS_is_mobile() {
        //https://developer.mozilla.org/fr/docs/Web/HTTP/Detection_du_navigateur_en_utilisant_le_user_agent
        return strpos(self::user_agent(), 'Mobi') ? true : false;
    }
    
    static function nav_OS_is_android() {
        return strpos(self::user_agent(), 'Android') ? true : false;
    }

    static function nav_OS_is_windows() {
        return strpos(self::user_agent(), 'Windows') ? true : false;
    }

    static function serv_OS_is_windows() {
        return DIRECTORY_SEPARATOR == '\\';
    }

    static function nav_is_local() {

        return $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] ? true : false;
    }

    //variable de cache pour éviter de lancer la commande DOS à chaque fois
    private static $_apache_as_service = NULL;

    //retourne true si apache lancé en mode service
    static function apache_as_service() {


        if (self::$_apache_as_service == NULL) {
            //met en minuscule le résultat
            $s = mb_strtolower(shell_exec('net start'));
            self::$_apache_as_service = mb_strpos($s, 'apache') > 0;
        }
        return self::$_apache_as_service;
    }

    static function get_full_url() {

        $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http' : 'https';
        $host = $_SERVER['HTTP_HOST'];
        $script = $_SERVER['SCRIPT_NAME'];
        $params = $_SERVER['QUERY_STRING'];

        $currentUrl = $protocol . '://' . $host . $script . (strlen($params) > 0 ? '?' : '') . $params;
        return $currentUrl;
    }

    static function mettre_dans_corbeille_____($path) {
        //regarder FileSystem.DeleteFile
        //http://msdn.microsoft.com/en-us/library/ms127977.aspx
        //marche pas car il supprime difinitivement
        if (Helper_system::serv_OS_is_windows()) {
            $path = str_ireplace('/', '\\', $path);

            $cmd = dirname(__FILE__) . '\exe\corbeille.exe "' . $path . '"';
            var_dump($cmd);

            shell_exec($cmd);
        } else {
            var_dump($path);
            var_dump('suppression non implementée');
        }
    }

    
    static function eteindre_machine() {
        //note : vérifier que le service apache puisse interagir avec le bureau
        //http://vipulchaskar.blogspot.fr/2013/02/shut-down-windows-from-web-and-mobile.html

        $output = shell_exec('shutdown /s /t 00');
        return utf8_encode($output);
    }
    
    
    
    
    
    
}
