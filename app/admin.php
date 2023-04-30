<?php
require './commun.php';

//redirect admin
require_once './lib/check-admin.php';
//pour l'utilisation du disque
require_once 'lib/chart/chart.php';


//construction formulaire
//http://bootsnipp.com/forms

$mdp = '';
if (isset($_POST['mdp'])) {
    $mdp = $_POST['mdp'];
}
$action_off = false;
if (isset($_POST['action_off'])) {
    $action_off = $_POST['action_off'];
}
//il y a eu une action du formulaire
if ($action_off !== false) {
    if ($mdp != config::mot_de_pass_admin()) {

        message::ajouter_alerte_ko('Mauvais mot de passe');
    } else {
        message::ajouter_alerte_ok( "Extinction en cours...<br>
		Patientez quelques minutes puis actualiser la page pour vérifier l'arrêt du serveur.");

        
        Helper_system::eteindre_machine();
     
    }
}


$ip_serveur = Helper_system::serv_ip();
$ip_navigateur = Helper_system::nav_ip();
$curl_actif = Helper_system::curl_actif() ? 'oui' : 'non';
$apache_as_service = Helper_system::apache_as_service() ? 'non' : 'oui';
$open_ssl_actif = Helper_system::openssl_actif() ? 'oui' : 'non';

//Utilisation du disque

//détection de l'espace libre
$dir = config::repertoireFilmsLocal();
$dir_space = $dir;
if(file_exists($dir)==false ){
    message::ajouter_alerte_ko("Le répertoire '$dir' n'existe pas ou manquant.");
    $dir_space = dirname($dir_space);
}
//test si dir existe


$df = disk_free_space($dir_space); 
$dt = disk_total_space($dir_space);

$du = $dt - $df;

//transformation en texte
$sdt = taille_fichier_en_texte($dt);
$sdu = taille_fichier_en_texte($du);
$sdf = taille_fichier_en_texte($df);

//conversion en Gb
$nb_virg = 2;
$tot = round($dt/1024/1024/1024,$nb_virg);
if($tot>3000){$nb_virg = 0;}

$dt=round($dt/1024/1024/1024,$nb_virg);
$df=round($df/1024/1024/1024,$nb_virg);
$du=round($du/1024/1024/1024,$nb_virg);

$series = new ChartSeries();
$series->name = "Total $sdt<br><div style=\"text-transform: none; \">(libre $sdf)</div>";
$chartserie = array();
$d = new ChartSeriesData();
$d->name = "utilisé <br>$sdu";
$d->y=$du;
array_push($chartserie, $d);

$d = new ChartSeriesData();
$d->name = "libre <br>$sdf";
$d->y=$df;
array_push($chartserie, $d);
$series->data = $chartserie;





require './template/admin.phtml';

/**
 * FIN
 */


