
//C'est pour gérer les affiches quand on clique dessus
//
////http://blog.raventools.com/create-a-modal-dialog-using-css-and-javascript/
//http://stackoverflow.com/questions/16778336/modal-dialog-without-jquery


var overlay_affiche = false;    //permet de savoir si l'overlay est affiché ou non
var overley_fixed = false;      //permet de savoir si overley fixed ou non qui est affiché
//
//mappe l'appui touche du clavier car une div ne peut
window.onkeydown = function (evt) {
    if (overlay_affiche) {
        overley_keydown(evt);
    } else {
        //window.alert('test');   
    }

}




function overlay_ferme() {
    //passe en mode non fixed pour éviter que la page soit agrandie pour rien en hauteur
    if(overley_fixed){
        //alert("unfixed");
        switch_fixed_non_fixed();
    }
    
    
    var fixed = document.getElementById("overlay_fixed");
    var non_fixed = document.getElementById("overlay_non_fixed");
    fixed.style.visibility = "hidden";
    non_fixed.style.visibility = "hidden";
    overlay_affiche = false;
    
}

function overlay() {
    var fixed = document.getElementById("overlay_fixed");
    var non_fixed = document.getElementById("overlay_non_fixed");

    fixed.style.visibility = (fixed.style.visibility == "visible") ? "hidden" : "visible";
    non_fixed.style.visibility = "hidden";
    //met le fond jusqu'au bas de page
    //el.style.height = document.body.clientHeight + "px";
    //alert();
    //fixed.focus();
    //alert();
    //document.getElementById('bt_overlay').focus();
    overlay_affiche = true;
}
function switch_fixed_non_fixed() {
    var el = document.getElementById("overlay_fixed");
    var e2 = document.getElementById("overlay_non_fixed");
    var img2 = document.getElementById("overlay_non_fixed_img");

    e2.style.visibility = el.style.visibility;
    el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
    //img2.style.overflow="hidden;";


    //si e2 (celui qui n'est pas fixé) devient visible, on met l'ascenseur en haut
    //met l'ascensseur en haut de la page
    if (e2.style.visibility == "visible") {
        //on RAZ le scroll si l'image est trop haute (ou ascenseur trop bas)
        var img_coin_bas = img2.clientHeight;
        var scroll_coin_haut = window.scrollY;
        if (img_coin_bas < scroll_coin_haut) {
            window.scrollTo(0, 0);
        }
        //hauteur du fond
        var h = document.body.clientHeight;
        e2.style.minHeight = h + "px";
        overley_fixed = true;
        
        //e2.style.height = document.body.e2.style.overflow == "visible");

        //document.getElementById('bt_overlay_non_fixed').focus();
    } else {
        overley_fixed = false;
        //document.getElementById('overlay_fixed').focus;
        //document.getElementById('bt_overlay').focus();
    }
    e2.style.overflow = "" + e2.style.visibility;

}

function img_click(event) {
    switch_fixed_non_fixed();

    // event est l'objet Event passé en paramètre
    // evite la propagation d'evènement javascript
    if (event.stopPropagation) {
        event.stopPropagation();
    }
    event.cancelBubble = true;
}

function overley_keydown(evt) {
    evt = evt || window.event;
    var isEscape = false;
    if ("key" in evt) {
        isEscape = (evt.key == "Escape" || evt.key == "Esc");
    } else {
        isEscape = (evt.keyCode == 27);
    }
    if (isEscape) {
        //alert("Escape");
        overlay_ferme();
    }
}

