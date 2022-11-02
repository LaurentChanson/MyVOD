<?php

require_once __DIR__ .'/../template/tpl.phtml';
require_once 'functions-helper.php';

class message {

    public static function afficher_et_stop() {
        //var_dump('test');
        //require_once '/../template/message.phtml'; //PB PHP7
        
        require_once __DIR__. '/../template/message.phtml';
        exit();
    }

    public static function ajouter_alerte_ok($message) {
        $_SESSION['message_success'] = Helper_var::session_var('message_success', '') . $message.'<br>';
    }

    public static function ajouter_alerte_ko($message) {
        $_SESSION['message_alert'] = Helper_var::session_var('message_alert', '') . $message.'<br>';
    }
    public static function ajouter_alerte_info($message) {
        $_SESSION['message_info'] = Helper_var::session_var('message_info', '') . $message.'<br>';
    }
    public static function ajouter_alerte_warning($message) {
        $_SESSION['message_warning'] = Helper_var::session_var('message_warning', '') . $message.'<br>';
    }
    public static function render() {
        $msg = Helper_var::session_var('message_alert', '');
        if (strlen($msg)) {
            echo('<br>');
            html_bootstrap_alert_danger($msg);
        }
        $msg = Helper_var::session_var('message_success', '');
        if (strlen($msg)) {
            echo('<br>');
            html_bootstrap_alert_success($msg);
        }
        $msg = Helper_var::session_var('message_info', '');
        if (strlen($msg)) {
            echo('<br>');
            
            html_bootstrap_alert_info($msg);
        }
        $msg = Helper_var::session_var('message_warning', '');
        if (strlen($msg)) {
            echo('<br>');
            html_bootstrap_alert_warning($msg);
        }
        self::raz();
    }

    public static function raz() {
        unset($_SESSION['message_success']);
        unset($_SESSION['message_alert']);
        unset($_SESSION['message_info']);
        unset($_SESSION['message_warning']);
    }

}
