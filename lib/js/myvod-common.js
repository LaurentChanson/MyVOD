var remonter_page_timeOut; //utilisé dans la fonction remonter_page();


window.onscroll = cacher_fleche_scroll;
window.onload = cacher_fleche_scroll;
window.onresize = cacher_fleche_scroll;

//ajoute un traitement en cours quand on change de page
//https://stackoverflow.com/questions/3522090/event-when-window-location-href-changes
window.addEventListener("beforeunload", function (event) {
    //var hf=window.history.forward();
    //window.alert(hf);
    setTimeout(afficher_traitement_en_cours, 1);

});



/*fix pb ancre firefox*/

function cacher_fleche_scroll_et_fixe_ancre() {

    //récupère s'il y a un dieze dans l'url
    var url_courant = document.location.href;
    var indice = url_courant.lastIndexOf("#");
    //window.alert(indice);
    if (indice > 4) {
        //indice++;
        var ancre = url_courant.substring(indice);
        //window.alert(ancre);
        //s'il y a une ancre, on l'active
        window.location.href = ancre;
    }
    cacher_fleche_scroll();
}


/**
 * Cache la fleche en fonction du scroll
 * @returns {undefined}
 */
function cacher_fleche_scroll() {
    dock_menu();
    //rend vible ou non la flèche  
    var h = get_scrollY();
    var visible = "hidden";
    if (h > 200) {
        visible = "visible";
    }
    window.document.getElementById("fleche_remonter").style.visibility = visible;

}

/**
 * retourne le scrollY du navigateur
 * IE n'est pas compatible avec window.scrollY (comme par hasard...)
 * @returns scrollY
 */
function get_scrollY() {
    var sy = window.scrollY;  //pas compatible IE
    if (isNaN(sy)) {
        return window.pageYOffset;
    }
    return sy;
}




/**
 * permet de "plaquer" le menu en haut de la page
 * @returns {undefined}
 */
function dock_menu() {

    //window.alert ("merse");
    var m = document.getElementById("menu");
    var b = document.getElementById("bandeau");
    var h = get_scrollY() - b.clientHeight;
    //alert("scrollY="+h+", bandeau height="+ b.clientHeight);

    if (h <= 0) {
        //m.style.top = "0px";
        //m.style.position="";
        if (m.className != "") {
            m.className = "";
            //m.style.top = "0px"
        }
        //m.style.backgroundColor="transparent";
    } else {
        //
        //m.style.position="fixed";
        if (m.className != "menu-fixed") {
            m.className = "menu-fixed";
        }

    }
    //fixe la hauteur du container du menu qui quand le menu passe en fixed, passe à 0.
    //console.log("hauteur menu : " + m.clientHeight);

    var pm = document.getElementById("menu_parent");

    pm.style.height = m.clientHeight + "px";



    //m.style.top = h + "px";

    //document.body.style.backgroundColor = "red"; 
    //alert(document.body.style.backgroundColor);
    //m.style.backgroundColor="inherit";



}

/**
 * Cette fonction et appelée quand on clique sur la flèche remonter
 * @returns {undefined}
 */
function remonter_page() {
    var pas = window.innerHeight / 4 * -1;
    //alert(window.innerHeight); 
    if (document.body.scrollTop !== 0 || document.documentElement.scrollTop !== 0) {
        window.scrollBy(0, pas);
        remonter_page_timeOut = setTimeout('remonter_page()', 1);
    } else {
        clearTimeout(remonter_page_timeOut);
    }
}


/**
 * Fixe le top de la side barre (dans l'index, detail et details modif)
 * @returns {undefined}
 */
function fixed_side_bar_hd() {

    cacher_fleche_scroll();

    //return;

    //position de l'ascenseur
    var scroll_y = get_scrollY();
    //largeur de la page
    var w = window.innerWidth;
    var e = document.getElementById("side_bar");



    //hauteur du bandeau
    var h_beandeau = document.getElementById("bandeau").clientHeight;
    var h = e.clientHeight;
    var h_body = document.getElementById("body_content").clientHeight;
    //console.log(h_body);
    var h_menu = document.getElementById("menu").clientHeight;

    var offset_top = document.getElementById("body_content").offsetTop - h_beandeau - h_menu;


    //console.log("h menu="+h_menu);

    var mode_normal = w < 1200 ? true : false;
    var depassement_hauteur = false;

    //Large display, 1200px (bootstrap)
    var y = scroll_y - h_beandeau - offset_top;
    if (y < 0) {
        y = 0;
        mode_normal = true;
    }

    //détection et fait une butée en bas
    if (y + h > h_body) {
        y = h_body - h;
        depassement_hauteur = true;
        //alert('sdgsg'+y);
    }
    //console.log(y + '  -'+e.style.marginTop+'-');

    if ((mode_normal == true) || (mode_normal == false && depassement_hauteur == true)) {
        if (y < 0)
            y = 0;
        //on est en mode normal
        if (e.className != "") {
            e.className = "";
        }
        if (mode_normal == true) {
            set_margin_top(e, "");
        } else {
            set_margin_top(e, y + "px");
        }
        /*
         if(e.style.marginTop!= y + "px"){
         e.style.marginTop = y + "px";
         }*/
    } else {
        //on passe en fixed
        if (e.className != "side_bar-fixed") {
            e.className = "side_bar-fixed";
            e.style.marginTop = "";
        }
    }

    //pour le debug, c'est pratique
    //console.log( e.offsetTop+  '  '+y + '  ' +h_body + '   '+ h + ' mode normal='+mode_normal + ', dépassmt='+depassement_hauteur);

}

function set_margin_top(e, margin_top) {
    //return;
    if (e.style.marginTop == margin_top)
        return;
    e.style.marginTop = margin_top;
}



/*
 * Permet d'activer la "side bar fixed"
 */
function active_fixed_side_bar_hd() {
    window.onscroll = fixed_side_bar_hd;
    window.onload = fixed_side_bar_hd;
    window.onresize = fixed_side_bar_hd;
}



/**
 * Remonte la page au niveau du menu
 * @returns {undefined}
 */
function remonte_page_menu() {
    //la position au chargement de la page

    setTimeout(function () {
        //var e=document.getElementById('menu');
        //e.scrollIntoView(true);

        //ci dessus marche pas sous opéra 12
        var h_beandeau = document.getElementById("bandeau").clientHeight;

        window.scrollTo(0, h_beandeau);
        // alert("h="+h_beandeau);


    }, 0);
}



/**
 *      Utilises pour l'extinction (nouveau raccourcis dans le menu
 */


function demande_extinction() {
    /*inputBox("Veillez entrer le mot de passe admin pour éteindre le serveur :" ,
     function () {
     test_mot_de_passe_pour_extinction(document.getElementById('input_valeur_attendue').value);
     });*/
    inputBox("Veillez entrer le mot de passe admin pour éteindre le serveur :",
            function (valeur_input) {
                test_mot_de_passe_pour_extinction(valeur_input);
            }, true);
}

function test_mot_de_passe_pour_extinction(val_retour) {
    $.get("./ajax/extinction.php?mdp=" + val_retour, function (data) {
        alert_html(data);
    });
}



//fait un appel serveur et renvoie un texte
function getTexteAjax(url) {
    //https://openclassrooms.com/courses/ajax-et-l-echange-de-donnees-en-javascript/l-objet-xmlhttprequest-1
    var xhr = getXMLHttpRequest(); // Voyez la fonction getXMLHttpRequest() définie dans la partie précédente
    //alert(url.replace('#','%23'));
    xhr.open("GET", url.replace('#', '%23'), false);
    xhr.send(null);

    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
        return xhr.responseText; // C'est bon \o/
    }
    return '';
}


//https://openclassrooms.com/courses/ajax-et-l-echange-de-donnees-en-javascript/l-objet-xmlhttprequest-1
function getXMLHttpRequest() {
    var xhr = null;
    if (window.XMLHttpRequest || window.ActiveXObject) {
        if (window.ActiveXObject) {
            try {
                xhr = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
        } else {
            xhr = new XMLHttpRequest();
        }
    } else {
        alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
        return null;
    }
    return xhr;
}



/**
 *      Utilisés pour la demande de suppression de fiche
 */


function supprimer_fiche_avec_confirmation(id_fiche, titre, nom_fichier) {
    //demande de confirmation
    /*
     //modif LC le 21/12/2015 : on passe par une confirm non modale
     if (confirm("Voulez-vous supprimer la fiche numéro " + id_fiche + " correspondant au titre :\n'" + nom_fiche + "' ?")) { // Clic sur OK
     window.location = '?action=<?= ACTION_SUPPRIMER_FICHE_BDD; ?>&param=' + id_fiche;
     }
     */
    var clbk = function () {
        supprimer_fiche_after_confirm(id_fiche);

    };
    confirm("Voulez-vous supprimer la fiche correspondant au fichier <strong>'" + nom_fichier + "'</strong>\n(Titre : <strong>'" + titre + "'</strong>, Numéro " + id_fiche + ") ?", clbk);
}


function supprimer_fiche_after_confirm(id_fiche) {

    //window.location = "http://www.google.fr" + id_fiche;
    //define("ACTION_SUPPRIMER_FICHE_BDD", 'delete_fiche');
    var ACTION_SUPPRIMER_FICHE_BDD = 'delete_fiche';
    window.location = 'traitements.php?action=' + ACTION_SUPPRIMER_FICHE_BDD + '&param=' + id_fiche;
}






/**
 *      a voir si toujours d'actualité
 */
function executer(page) {
    var http;
    http = new XMLHttpRequest();
    http.open('GET', page, true);
    http.send(null);
}

function popup(page) {

    window.open(page, 'resizable=no, location=no, width=200, height=100, menubar=no, status=no, scrollbars=no, menubar=no');

}