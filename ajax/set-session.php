<?php

header('Content-Type: text/html; charset=utf-8');

require_once '../commun.php';

//Le but de cetta est de mettre en session une variable
$key =  Helper_var::get_var('key', 'misc');
$value =  Helper_var::get_var('value', '');
$_SESSION[$key] = $value;

//var_dump($value);

echo "$key=>'$_SESSION[$key]'";