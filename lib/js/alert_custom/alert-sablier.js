/*
 LC : 24/03/2017
 Permet d'afficher un sablier au milieu de l'écran
 14/06/2018 : modifs class css
 */

var popup = null;

/*ouverture popup tratement en cours...*/
function afficher_traitement_en_cours() {
    //crée la popup si elle est nulle
    if (popup_is_null(popup)) {
        //création du fond
        popup = document.createElement("div");
        //ajoute au début
        //var overlay = document.body.insertBefore(popup, document.body.firstChild);
        
        //ajoute à la fin pour faire apparaitre le sablier par dessus
        //https://stackoverflow.com/questions/4793604/how-to-insert-an-element-after-another-element-in-javascript-without-using-a-lib
        var overlay = document.body.insertBefore(popup, document.body.nextSibling);
       
        overlay.setAttribute("class", "alertOverlay");


        // Création de la boite
        var div2 = document.createElement("div");
        var box = overlay.appendChild(div2);
        box.setAttribute("class", "modal-dialog alertBox sablier modal-content");
        //box.setAttribute("style", "width: 400px!important;");
        //création sous box
        var sbox = document.createElement("div");
        sbox = box.appendChild(sbox);
        sbox.setAttribute("class", " modal-content ");
        box = sbox;

        // Création du contenu
        var content = box.appendChild(document.createElement("div"));
        content.setAttribute("class", "modal-body alertTitle");
        //le texte
        content.innerHTML = '<br><center>Traitement en cours...<br>Veuillez patienter.</center><br>';
        //l'image animée
        content.innerHTML += '<center><i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i></center><br>';

    }


}
/*fermeture popup tratement en cours...*/
function fermer_traitement_en_cours() {
    //enlève la popup si elle est non nulle
    if (!popup_is_null(popup)) {
        document.body.removeChild(popup);
    }
    popup = null;
}

/*tester si popup est null ou non*/
function popup_is_null() {
    return popup == null;
}
