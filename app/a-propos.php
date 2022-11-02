<?php
require './commun.php';

$txt = file_get_contents('versions.txt');

$txt_versions = nl2br($txt);


require 'template/a-propos.phtml';
