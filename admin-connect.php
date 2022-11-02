<?php
require './commun.php';

//redirect admin
require_once './lib/check-admin.php';

//redirect après reconnexion

$url=Helper_redirection::get_derniere_page();
if($url==''){
    $url='admin.php';
}else{
    $url=str_replace(array('?deconnect=true','&deconnect=true'),'',$url);
}

Helper_redirection::redirige($url);
