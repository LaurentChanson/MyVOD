//Modif LC 11/10/2018 : les textarea sont à 30px par défaut

//redimentionnement automatique des text Area
function FitToContent(id, maxHeight)
{
    var text = id && id.style ? id : document.getElementById(id);
    if (!text)
        return;
    text.style.height="30px";
    var adjustedHeight = text.clientHeight;
    if (!maxHeight || maxHeight > adjustedHeight)
    {
        adjustedHeight = Math.max(text.scrollHeight, adjustedHeight);
        if (maxHeight)
            adjustedHeight = Math.min(maxHeight, adjustedHeight);
        if (adjustedHeight != text.clientHeight){
                text.style.height = (adjustedHeight + 2) + "px";
            console.log("hauteur de "+ text.id+" ="+ text.style.height);
        }
    }
}

function valider_formulaire() {

    //html bande annonce
    var html_ba = document.getElementById("ba_embed").value;    //ba_embed
    html_ba = html_ba.trim();
    if (html_ba.length > 0) {
        //url bande annonce
        var url = document.getElementById("ba_link").value;       //ba_link
        url = url.trim();
        if (url.length == 0) {
            alert("La Bande annonce (html) est remplie alors qu'il manque son URL.\n" +
                    "Veuillez remplir l'URL de la bande annonce.");
            return false;
        }
    }
    //valide le formulaire
    return true;

}


//changement du lien du bouton "Rechercher sur allo ciné"
function refreshLiensRechercheAllocine() {
    //récupère le texte du champ txt_recherche_allocine
    var txt = document.getElementById("txt_recherche_web").value;
    //window.alert(txt);
    //applique le lien sur les 2 boutons
    document.getElementById("a_recherche_allocine").href = "recherche-web.php?recherche_web=" + txt + "&type_recherche=<?= type_recherche::RECHERCHE_ALLOCINE ?>";

    document.getElementById("a_recherche_dvdfr").href = "recherche-web.php?recherche_web=" + txt + "&type_recherche=<?= type_recherche::RECHERCHE_DVDFR ?>";
}






function page_affiche_lien_id_si_entree(id_lien,event) {
    if (event.keyCode == 13) {
        u = document.getElementById(id_lien).href;
        //setTimeout( "location.href = document.getElementById('a_recherche_dvdfr').href;", 0 );
        
        //window.old_alert(u);
        page_affiche_lien_id(u);
        return false;
    }
    return true;
}

function page_affiche_lien_id(url) {
    document.getElementById('bt_recherche_allocine').click();
    //location.href = url;
    //setTimeout("location.href = '" + url + "';", 0);
}