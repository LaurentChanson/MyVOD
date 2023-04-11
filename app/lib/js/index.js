/*
 * événements du textbox rechercher
 * LC 26/07/2018 : ajout pour les tailles de fichiers
 * LC 26/09/2018 : ajout pour aller vers ancre
 * LC 10/10/2018 : petite modif dans la fonction popup_detail (ajout sécu si ne trouve pas l'élément img)
 */


function goto_ancre(ancre) {
    
    //récupère l'url courante
    var url = self.location.href;
    //test si l'url contient "#ancre" à la fin
    var index_of_diese = url.lastIndexOf(ancre);
    if (index_of_diese>0){
        var ancre_detecte = url.substr(index_of_diese);
        if(ancre_detecte === ancre){
            //alert(ancre_detecte);
            //console.log(ancre);
            window.location.href = ancre;
            
        }
        
    }
    
    
    /*
    
    if (url.indexOf(ancre) > 0) {
        //alert(self.location.href);
        //permet d'aller directement avant d'attendre la fin du chargement de la page
        window.location.href = ancre;
    }
    */
}



function search_query_input() {
    var input = document.getElementById('search_query');
    var val = input.value;
    var list = input.getAttribute('list'),
            options = document.getElementById(list).childNodes;

    for (var i = 0; i < options.length; i++) {
        if (options[i].innerText === val) {
            // An item was selected from the list!
            // yourCallbackHere()
            raz_all_sauf_recherche();
            //validation du formulaire
            ValiderFormulaire();
            break;
        }
    }

}



function search_query_dblclick() {
    var e = document.getElementById('search_query');
    e.select();
}



function search_query_onkeyup(event) {
    var my_field = document.getElementById('search_query');
    //détection de la touche entré
    if (event.keyCode == 13) {
        event.preventDefault();
        if (my_field.value.length != 0) {
            //console.log(my_field.value);
            ValiderFormulaire();
        }
    }
}


//quand le texte change, on coche les cases des genres et annees
function search_query_onchange() {
    var search_query = document.getElementById('search_query');
    //on coche si la taille du texte est >0
    if (search_query.value.length != 0) {
        raz_autres_panels();
    }
}


function raz_all_sauf_recherche() {

    raz_autres_panels();//coche les différentes case des années, genres et type de publique

    //coche la case du titre et décoche synopsis, acteurs & nom de fichier
    document.getElementById('rech_titre').checked = 'checked';
    document.getElementById('rech_synopsis').checked = '';
    document.getElementById('rech_acteurs').checked = '';
    document.getElementById('rech_nom_fichier').checked = '';
    document.getElementById('rech_realisateur').checked = '';
    /*
    //toutes qualités coches
    document.getElementById('qualite_tous').checked = 'checked';
    
    // basculé dans "raz_filtre_taille_et_divers"
    //idem pour le filtre parental
    var fp = document.getElementById('filtre_recherche_parental_tous');
    //en mode admin, il n'y a pas de filtre parental
    if (fp !== null) {
        fp.checked = 'checked';
    }
*/

}

function raz_filtre_taille_et_divers() {
    //les tailles
    document.getElementById('taille_min').selectedIndex = 0;
    document.getElementById('taille_max').selectedIndex = 0;
    //filtre sur les films que j'ai jamais vu
    document.getElementById('filtre_jamais_vu').checked = '';
    
    //toutes qualités coches
    document.getElementById('qualite_tous').checked = 'checked';
    
    //idem pour le filtre parental
    var fp = document.getElementById('filtre_recherche_parental_tous');
    //en mode admin, il n'y a pas de filtre parental
    if (fp !== null) {
        fp.checked = 'checked';
    }
}


//cette fonction est appelée quand on clique sur la croix
function raz_champ_recherche() {
    //on vide la zone ou on écrit
    var champ = document.getElementById("search_query");
    champ.value = "";
    champ.focus();
    raz_all_sauf_recherche();
}

function raz_autres_panels() {
    //on coche tout
    CocherGroupCheckBox('annee_filtre_', nbannees);
    CocherGroupCheckBox('genre_filtre_', nbgenres);
    CocherGroupCheckBox('public_filtre_', nbpublics);
    CocherGroupCheckBox('nationalite_filtre_', nbnationalites);
    //les tailles
    raz_filtre_taille_et_divers();
}

function RaffraichitNbGenresCoches() {

    var nbCoches = 0;
    for (var i = 0; i < nbgenres; i++) {
        if (document.getElementById("genre_filtre_" + i).checked)
            nbCoches++;
    }

    document.getElementById("titre_genre").innerHTML = "Genres " + nbCoches + "/" + nbgenres;

    return nbCoches;

}

function RaffraichitNbAnneesCoches() {

    var nbCoches = 0;
    for (var i = 0; i < nbannees; i++) {
        if (document.getElementById("annee_filtre_" + i).checked)
            nbCoches++;
    }

    document.getElementById("titre_annee").innerHTML = "Années de production " + nbCoches + "/" + nbannees;

    return nbCoches;

}

function RaffraichitNbPublicCoches() {

    var nbCoches = 0;
    for (var i = 0; i < nbpublics; i++) {
        if (document.getElementById("public_filtre_" + i).checked)
            nbCoches++;
    }

    document.getElementById("titre_public").innerHTML = "Publiques " + nbCoches + "/" + nbpublics;

    return nbCoches;

}

function RaffraichitNbNationalitesCoches() {

    var nbCoches = 0;
    for (var i = 0; i < nbnationalites; i++) {
        if (document.getElementById("nationalite_filtre_" + i).checked)
            nbCoches++;
    }

    document.getElementById("titre_nationalite").innerHTML = "Nationalités " + nbCoches + "/" + nbnationalites;

    return nbCoches;

}

function MAJ_recherche_deplie() {
    //met à jour le champ caché l'état du 'collapse'
    //var f=
    maj_champ_hidden_flag_collapse("recherche_deplie", "collapse0");
    //document.getElementById('bt_collapse_0_chevron-down').style="display: "+(f?"inline":"none")+";";
    //document.getElementById('bt_collapse_0_chevron-left').style="display: "+(f?"none":"inline")+";";
}

function MAJ_genre_deplie() {
    maj_champ_hidden_flag_collapse("genre_deplie", "collapse1");
}

function MAJ_annee_deplie() {
    maj_champ_hidden_flag_collapse("annee_deplie", "collapse2");
}

function MAJ_public_deplie() {
    maj_champ_hidden_flag_collapse("public_deplie", "collapse3");
}

function MAJ_nationalite_deplie() {
    maj_champ_hidden_flag_collapse("nationalite_deplie", "collapse4");
}
function MAJ_avance_deplie() {
    maj_champ_hidden_flag_collapse("avance_deplie", "collapse5");
}
function MAJ_export_deplie() {
    maj_champ_hidden_flag_collapse("export_deplie", "collapse6");
}
function maj_champ_hidden_flag_collapse(champ_a_indiquer, champ_collapse) {
    //met a jour le champ hidden avec l'état du collapse
    var e = document.getElementById(champ_a_indiquer);
    var class_name = document.getElementById(champ_collapse).className;
    var flag = (class_name == 'panel-collapse collapse in') ? 0 : 1;
    e.value = flag;
    //change la flèche du bouton ( > ou \/)

    document.getElementById('bt_' + champ_collapse + '_chevron-down').style.display = (flag ? "inline" : "none");
    document.getElementById('bt_' + champ_collapse + '_chevron-left').style.display = (flag ? "none" : "inline");

    return flag;
}




function CocherGroupCheckBox(prefix, nb) {
    CoDeCocherGroupCheckBox(true, prefix, nb);
}

function DecocherGroupCheckBox(prefix, nb) {
    CoDeCocherGroupCheckBox(false, prefix, nb);
}

function CoDeCocherGroupCheckBox(trueSiCoche, prefix, nb) {
    //nb est le nombre de genre
    for (var i = 0; i < nb; i++) {
        CocherCheckBox(prefix + i, trueSiCoche);
    }
    RaffraichitNbGenresCoches();
    RaffraichitNbAnneesCoches();
    RaffraichitNbPublicCoches();
    RaffraichitNbNationalitesCoches();
}

function CocherCheckBox(nom_du_champ, trueSiCoche) {
    document.getElementById(nom_du_champ).checked = trueSiCoche;
}

function FiltrerParGenre(index_genre_filre) {
    var groupCheckBox = 'genre_filtre_';

    //on décoche tout du groupe
    DecocherGroupCheckBox(groupCheckBox, nbgenres);
    //on recoche le bon
    CocherCheckBox(groupCheckBox + index_genre_filre, true);

    //on coche les annees
    CocherGroupCheckBox('annee_filtre_', nbannees);
    //on coche le public
    CocherGroupCheckBox('public_filtre_', nbpublics);
    //on coche les nationalités
    CocherGroupCheckBox('nationalite_filtre_', nbnationalites);
    //les tailles
    raz_filtre_taille_et_divers();

    RaffraichitNbGenresCoches();
    //on vide le textbox
    document.getElementById("search_query").value = "";
    //on valide le formulaire
    ValiderFormulaire();
    //$(action).click();
}

function FiltrerParAnnee(index_genre_filre) {


    //on décoche tout du groupe
    DecocherGroupCheckBox('annee_filtre_', nbannees);
    //on recoche le bon
    CocherCheckBox('annee_filtre_' + index_genre_filre, true);

    //on coche les genres
    CocherGroupCheckBox('genre_filtre_', nbgenres);
    //on coche le public
    CocherGroupCheckBox('public_filtre_', nbpublics);
    //on coche les nationalités
    CocherGroupCheckBox('nationalite_filtre_', nbnationalites);
    //les tailles
    raz_filtre_taille_et_divers();

    RaffraichitNbAnneesCoches();
    //on vide le textbox
    document.getElementById("search_query").value = "";
    //on valide le formulaire
    ValiderFormulaire();
    //$(action).click();
}


function FiltrerParPublic(index_filre) {


    //on décoche tout du groupe
    DecocherGroupCheckBox('public_filtre_', nbpublics);
    //on recoche le bon
    CocherCheckBox('public_filtre_' + index_filre, true);

    //on coche les genres
    CocherGroupCheckBox('genre_filtre_', nbgenres);
    //on coche les annees
    CocherGroupCheckBox('annee_filtre_', nbannees);
    //on coche les nationalités
    CocherGroupCheckBox('nationalite_filtre_', nbnationalites);
    //les tailles
    raz_filtre_taille_et_divers();

    RaffraichitNbPublicCoches();
    //on vide le textbox
    document.getElementById("search_query").value = "";
    //on valide le formulaire
    ValiderFormulaire();
    //$(action).click();
}

function FiltrerParNationalite(index_filre) {


    //on décoche tout du groupe
    DecocherGroupCheckBox('nationalite_filtre_', nbnationalites);
    //on recoche le bon
    CocherCheckBox('nationalite_filtre_' + index_filre, true);

    //on coche les genres
    CocherGroupCheckBox('genre_filtre_', nbgenres);
    //on coche les annees
    CocherGroupCheckBox('annee_filtre_', nbannees);
    //on coche le public
    CocherGroupCheckBox('public_filtre_', nbpublics);
    //les tailles
    raz_filtre_taille_et_divers();

    RaffraichitNbNationalitesCoches();
    //on vide le textbox
    document.getElementById("search_query").value = "";
    //on valide le formulaire
    ValiderFormulaire();
    //$(action).click();
}

//est appelé par les
function ValiderFormulaireSiCriteresNonRaz(){
    //window.alert("recherche"+champsRecherchesIsRAZ());
    //console.log(ChampsRecherchesIsRAZ());
    if(!ChampsRecherchesIsRAZ()){
        //window.alert("recherche");
        ValiderFormulaire();
    }
}

function ChampsRecherchesIsRAZ(){
    
    //si champ de recherche est non vide, retourne faux
    if (document.getElementById("search_query").value.length > 0){
        return false;
    }
    
    //regarde si les genres sont tous cochés
    if(!GroupCheckBoxIsRAZ('genre_filtre_', nbgenres)){
        return false;
    }
    
    //idem pour les publiques
    if(!GroupCheckBoxIsRAZ('public_filtre_', nbpublics)){
        return false;
    }
    
    //année de prod
    if(!GroupCheckBoxIsRAZ('annee_filtre_', nbannees)){
        return false;
    }
    
    //idem pour les nationnalités
    if(!GroupCheckBoxIsRAZ('nationalite_filtre_', nbnationalites)){
        return false;
    }
    
    
    //tailles de fichiers
    if(document.getElementById('taille_min').selectedIndex > 0){
        return false;
    }
    if(document.getElementById('taille_max').selectedIndex > 0){
        return false;
    }
    
    return true;
}

function GroupCheckBoxIsRAZ( prefix, nb){
    if(nb===0){return true;}
    //parcours des éléments du groupe
    for (var i = 0; i < nb; i++) {
        //si au moins un de décoché retourne false
        //console.log(prefix+i+' ' +document.getElementById(prefix+i).checked);
        if(document.getElementById(prefix+i).checked == false){
            return false;
        }
    }
    return true;
}



//valide le formulaire principal (est appelé par les checkbox dans la zone verte)
function ValiderFormulaire2() {
    //on recopie les valeurs des trie 2 dans les ckeck box tri
    var i = 0;
    for (i = 0; i < 6; i++) {
        document.getElementsByName("tri")[i].checked = document.getElementsByName("tri2")[i].checked;
    }

    ValiderFormulaire();
}


//valide le formulaire principal en lancant la recherche de fiche
function ValiderFormulaire() {
    var e = document.getElementById("action");
    // obligé de passer par un timer car sinon il y a un bug d'affichage sous Opéra 12.
    setTimeout(function () {
        e.click();
    }, 100);

}

//met l'arriere plan en fond
function souris_rentre_img_gallerie(img_src) {
    setTimeout(function () {
        var e = document.getElementById('img_background');
        e.setAttribute("src", img_src);
        e.style.visibility = "visible";
        //console.log('enter '+img_src);
    }, 1);

    //document.body.style.backgroundImage = "url('" + src + "')";
    //alert(src);
}
//enleve l'arriere plan
function souris_sort_img_gallerie() {
    setTimeout(function () {
        var e = document.getElementById('img_background');
        e.style.visibility = "hidden";
        //console.log('sortie ');
    }, 1);
    //document.body.style.backgroundImage = "url('')";
}

//popup detail, elle permet d'afficher
function popup_details(id) {
    //récup de l'affiche
    var img = document.getElementById("miniature_" + id);
    //modif LC : 10/10/2018 (évite un message console en mode liste)
    if(img===null){return;}
    var img_html = '<img class="affiche_popup" src="' + img.src + '" alt="' + img.alt + '"/>';

    //récup du titre
    var titre = document.getElementById("titre_" + id).innerText;


    //récupère l'élément texte à afficher
    var elmt = document.getElementById("film_" + id);
    var html = elmt.children[0].innerHTML;
    //var html = elmt.innerHTML;
    //remplace la balise small
    html = html.replaceAll("<small", "<div");
    html = html.replaceAll("</small", "</div");
    //grossit le titre en bleu
    html = html.replace("<strong>", '<h2 style="margin-top:0px;"><strong>');
    html = html.replace("</strong>", "</h2></strong>");

    html = html.replace("Résumé : ", "<br>Résumé : <br>");

    //inhibe les interractions javascript
    html = html.replaceAll("onclick=", "value_on_click=");
    //MyVOD
    alert_html('<div>' +
            img_html +
            '<div>' +
            '\n' +
            '<div style="text-align: justify;">' +
            html +
            '</div>',
            "MyVOD : Détail de : " + titre);

    //en test pour superposition
    //afficher_traitement_en_cours();

                        $('*[title]').tooltip({
                    animation: false,
                    placement: 'bottom'
                });

}