/*
 * Modif LC 25/07/2018
 * 09/10/2018 : Gestion de la tabulation
 * 11/10/2018 : Ajout sécurité pour éviter d'inclure 2 fois le fichier
 * 18/10/2018 : Ajouts méthodes pour customiser les toolsTip
 * 23/10/2018 : Amélioration des toolsTip sous Chrome + activation sur le focus
 * 15/11/2018 : Amélioration des toolsTip quand l'onglet vient d'avoir le focus
 * 19/11/2018 : Amélioration des toolsTip quand l'onglet vient d'avoir le focus
 */
// New Alert
//alert('This is a <strong>new</strong> alert!');

// Old Alert:
//old_alert('This is an old, boring alert.');

//Evite 
if (alert_js_once === undefined) {
    //alert(alert_js_once);


    window.old_alert = window.alert;
    window.old_confirm = window.confirm;

// La toute première chose est de modifier la fonction alert. Ainsi, on appellera non plus la méthode du navigateur mais une fonction personnalisée
    window.alert = function (text, titre) {
        msgBox(text, titre);

    };

    window.confirm = function (message, titre, fonction) {
        msgBox(message, titre, fonction);
    };
    window.confirm = function (message, fonction) {
        msgBox(message, null, fonction);
    };

}

var alert_js_once = true;

//-------------------------------

function input_valeur_attendue_onkeydown(e) {
    if (e.keyCode == 13) {
        document.getElementById("alert_bt_valider").click();
    }
}

function inputBox(message, fonction) {
    return inputBox(message, fonction, false);
}

function inputBox(message, fonction, mot_de_passe) {
    var type_input = mot_de_passe ? 'password' : 'text';


    var input = '<br><br><input type="' + type_input + '" name="input_valeur_attendue"  \n\
    id="input_valeur_attendue" class="form-control" value="" \n\
    autofocus onkeydown="input_valeur_attendue_onkeydown(event)"\n\
    style="width:100%">';

    var fonction2 = function () {
        fonction(document.getElementById('input_valeur_attendue').value);
    };


    msgBox_(message + input, null, fonction2, true, false);
    document.getElementById('input_valeur_attendue').focus();
}



function alert_html(message) {
    msgBox(message, null, null, true);
}
function alert_html(message, titre) {
    msgBox(message, titre, null, true);
}
function msgBox(text, titre, fonction, mode_html) {
    msgBox_(text, titre, fonction, mode_html, true);
}

function msgBox_(text, titre, fonction, mode_html, bt_ok_annuler) {
    var mode_confirm = typeof (fonction) != "undefined" && fonction != null;
    if (typeof (mode_html) == 'undefined') {
        mode_html = false;
    }
    var bt_a_focuser = 'alert_bt_close';

    //création du fond
    var div = document.createElement("div");
    var overlay = document.body.insertBefore(div, document.body.firstChild);
    overlay.setAttribute("class", "alertOverlay");
    overlay.setAttribute("id", "alert-overlay");

    // Creation de la boite
    var div2 = document.createElement("div");
    var box = overlay.appendChild(div2);
    box.setAttribute("class", "modal-dialog alertBox modal-content modal-lg");//modal-sm
    box.setAttribute("id", "modal-dialog");
    /*
     //creation sous box
     var sbox = document.createElement("div");
     sbox = box.appendChild(sbox);
     sbox.setAttribute("class", " modal-content ");
     box = sbox;
     */

    // Creation titre
    var title = box.appendChild(document.createElement("div"));
    title.setAttribute("class", "modal-header");

    /* --------- modif LC 25/07/2018 ------------*/
    //fermeture quand on clique dans l'overlay
    overlay.onclick = function ()
    {
        //enlève le fond
        //event.stopPropagation();
        document.body.removeChild(div);

    };

    //fermeture quand on fait echap
    //https://stackoverflow.com/questions/3369593/how-to-detect-escape-key-press-with-pure-js-or-jquery?rq=1
    overlay.onkeydown = function (evt) {
        evt = evt || window.event;
        var isEscape = false;
        if ("key" in evt) {
            isEscape = (evt.key == "Escape" || evt.key == "Esc");
        } else {
            isEscape = (evt.keyCode == 27);
        }
        if (isEscape) {
            //alert("Escape");
            document.body.removeChild(div);
        }
    }



    //évite la fermeture de la popup quand on clique dessus
    div2.onclick = function (e)
    {
        //évite la farmeture de la popup quand on clique dessus
        e.stopPropagation();
    };
    /* --------- modif LC 25/07/2018 ------------*/


    // Bouton fermant (croix)
    var img = document.createElement("button");
    img.setAttribute("type", "button");
    img.innerHTML = "&#215;";


    var close = box.appendChild(img);
    close.setAttribute("title", "Fermer");
    close.setAttribute("class", "alertBtClose close");
    close.setAttribute("id", "alert_bt_close");
    close.onclick = function ()
    {
        //ferme la fenêtre (sera fait avec le fond)
        //document.body.removeChild(div2);
        //enlève le fond
        document.body.removeChild(div);

    };
    /*LC : 09/10/2018*/
    close.onkeydown = function (e) {
        //touche tabulation (09/10/2018)
        if (e.keyCode == 9 && e.shiftKey == true) {
            var bt_ok_valider = document.getElementById('alert_bt_valider');
            //console.log('TAB');
            bt_ok_valider.focus();
            //https://hiddedevries.nl/en/blog/2017-01-29-using-javascript-to-trap-focus-in-an-element
            e.preventDefault();

        }
    };
    // Creation du contenu
    var content = box.appendChild(document.createElement("div"));
    content.setAttribute("class", "modal-body");
    // Insertion contenus (titre & texte)
    if (typeof (titre) == "undefined" || titre == null)
        titre = "MyVOD";

    title.innerHTML = "<h4 class=\"modal-title alertTitle\">" + titre + "</h4> ";
    if (mode_html == false) {
        text = text.replaceAll("\n", "<br>");
        text = text.replaceAll("  ", "&nbsp;&nbsp;");
    }
    content.innerHTML = text;

    var footer = box.appendChild(document.createElement("div"));
    footer.setAttribute("class", "modal-footer");

    if (mode_confirm) {

        //bouton NON du bas

        var bt_non = footer.appendChild(document.createElement("button"));
        bt_non.setAttribute("class", "btn btn-default");
        bt_non.setAttribute("type", "button");
        bt_non.innerHTML = bt_ok_annuler ? "Non" : "Annuler";
        bt_non.setAttribute("id", "alert_bt_annuler");
        var action_annuler = function ()
        {
            document.body.removeChild(div);
        };
        bt_non.onclick = action_annuler;
        bt_non.onkeydown = function (e) {
            if (e.keyCode == 13) {
                action_annuler();
            }
        }
        bt_a_focuser = 'alert_bt_annuler';
    }



    //bouton OK du bas
    var bt_ok = footer.appendChild(document.createElement("button"));
    bt_ok.setAttribute("class", "btn btn-default");
    bt_ok.setAttribute("type", "button");
    bt_ok.setAttribute("id", "alert_bt_valider");
    bt_ok.innerHTML = mode_confirm && bt_ok_annuler ? "  Oui  " : "OK";

    var action_valider = function ()
    {
        //ferme la fenêtre (sera fait avec le fond)
        //document.body.removeChild(div2);
        //enlève le fond
        if (mode_confirm) {
            fonction();
        }
        document.body.removeChild(div);
    };
    bt_ok.onclick = action_valider;
    //gestion du bouton entrer et tabulation
    bt_ok.onkeydown = function (e) {
        //touche entré
        if (e.keyCode == 13) {
            action_valider();
        }
        //touche tabulation (09/10/2018)
        if (e.keyCode == 9 && e.shiftKey == false) {

            //console.log('TAB');
            //var bt_croix=document.getElementById('alert_bt_close');
            close.focus();
            //https://hiddedevries.nl/en/blog/2017-01-29-using-javascript-to-trap-focus-in-an-element
            e.preventDefault();

        }
    };

    //placement de la "fenêtre"
    //prend 0 si valeur négative

    //div2.style.marginTop = -(div2.offsetHeight / 2) + "px";
    var top = div.offsetHeight / 2 - div2.offsetHeight / 2 - 20;
    if (top < 0)
        top = 0;
    div2.style.top = top + "px";

    //hauteur de l'overlay
    div.style.height = window.innerHeight + "px";

    //met le focus sur le bouton valider (ou annuler en fonction du contexte)

    document.getElementById(bt_a_focuser).focus();

    if (tooltip_initialise) {
        //showtooltip();



        //alert("Hello"); 


        showtooltip_with_sel('.alertOverlay *[title]');

        //showtooltip_with_sel( '.alertOverlay > *[title]','#modal-dialog');
        //showtooltip_with_sel( '*[title]','body');
        //modal-dialog

    }

} //function msgBox_(text, titre, fonction, mode_html,bt_ok_annuler) {



//ajoute 'replaceAll' à la classe String 
// Replaces all instances of the given substring.
String.prototype.replaceAll = function (
        strTarget, // The substring you want to replace
        strSubString // The string you want to replace in.
        ) {
    var strText = this;
    var intIndexOfMatch = strText.indexOf(strTarget);

    // Keep looping while an instance of the target string
    // still exists in the string.
    while (intIndexOfMatch != -1) {
        // Relace out the current instance.
        strText = strText.replace(strTarget, strSubString);

        // Get the index of any next matching substring.
        intIndexOfMatch = strText.indexOf(strTarget);
    }

    // Return the updated string with ALL the target strings
    // replaced out with the new substring.
    return(strText);
}


//------------------------------------------------------
//pour activer les tooltips customisés
//------------------------------------------------------
var tooltip_initialise = false;

function showtooltip() {
    showtooltip_with_sel('*[title]');
}

//pour les paramètres
//https://www.tutorialrepublic.com/twitter-bootstrap-tutorial/bootstrap-tooltips.php


function showtooltip_with_sel(selecteur) {
    var delay_show = 500;

    $(selecteur).tooltip({
        /*body parmet d'être sur que le toolTip est au dessus de tout*/
        container: 'body',
        animation: true,
        trigger: 'hover', //ou que 'hover' ? focus?
        placement: function (context, element) {
            //en fonction de la position de la souris, on retourne 'top' ou 'bottom'
            var hauteur_fenetre = window.innerHeight;
            var scroll_y = $(window).scrollTop();
            var position_top = $(element).offset().top;
            var hauteur_element = element.offsetHeight;
            //console.log('top '+position_top+ ' scrolltop  ' +scroll_y + ' ' + hauteur_fenetre);
            //console.log(''+(position_top + hauteur_element - scroll_y));
            //si on est bas de la fenetre, on le met en haut
            if (position_top + hauteur_element - scroll_y >= hauteur_fenetre - 100) {
                return 'top';
            }
            return 'bottom';

        }, //'bottom',
        html: true,
        delay: {show: delay_show, hide: 0}
    });

    //ferme tous les toolTips
    $(selecteur).mouseenter(function () {
        //$(selecteur).on('show.bs.tooltip',function(){
        //cache les autres toolTips que celui sélectionné
        var elements_a_cacher = $(selecteur).not(this);
        //console.log(elements_a_cacher);

        $(elements_a_cacher).tooltip('hide');

    });

    //exemple de mesure de temps
    //http://code18.blogspot.com/2010/04/calculer-le-temps-dexecution-dun-script.html
    var window_focus_time = new Date().getTime();
    
    //cache le toolTip quand on clique sur le bouton
    $(selecteur).click(function () {
        $(this).tooltip('hide');
        window_focus_time = new Date().getTime();
        //pour debugger, on clique et tous les tools tip s'affichent
        //$(selecteur).tooltip('show');
        //console.log('click');
    });

    //evite d'avoir le tooltip quand on réactive le navigateur
    $(window).focus(function () {
        window_focus_time = new Date().getTime();
        //cache tous les éléments
        $(selecteur).tooltip('hide');
        //console.log('window.focus');
        //console.log(window_focus_time);
    });




    //ferme la bulle quand le focus se pert (s'il y a la souris dessus un autre)
    $(selecteur).focusout(function () {
        $(this).tooltip('hide');
        //$(this).tooltip('show');
        //console.log('focusout');
    });
    
    //ouvre la bulle quand on récupère le focus
    $(selecteur).focus(function () {
        //fait un test si l'onglet vient d'etre activé ou non
        var duree_depuis_doc_focus=new Date().getTime()-window_focus_time;
        if(duree_depuis_doc_focus>delay_show){
            //récupère le 'this' car il change dans le 'setTimeout
            var element_a_tooltiper=this;
            setTimeout(function(){
                //refait le test quand on déplie un panel
                duree_depuis_doc_focus=new Date().getTime()-window_focus_time;
                if(duree_depuis_doc_focus>delay_show){
                    $(element_a_tooltiper).tooltip('show');
                }
                //console.log('focus');
                //console.log(duree_depuis_doc_focus);
            }, delay_show);
        }
        //console.log('focus');
        //alert('merde');
        //$(this).tooltip('show');
    });

    /*
     $(selecteur).mouseleave(function(){
     $(this).tooltip('hide');
     //alert('merde');
     //$(this).tooltip('show');
     });
     
     $(selecteur).mouseenter(function(){
     $(selecteur).tooltip('hide');
     //$(this).tooltip('show'); 
     var element_tooltip=this;
     setTimeout(function(){ 
     $(element_tooltip).tooltip('show'); 
     }, 500);
     
     //alert('merde');
     //$(this).tooltip('show');
     });
     
     */

    /*
     $(selecteur).on('show.bs.tooltip', function(e){
     $(selecteur).tooltip('hide');
     $(e).tooltip('show');
     console.log(element);
     //$(this).tooltip('show');
     });
     */



    /*
     var fermer_tous_tooltips = function(){
     $(selecteur).tooltip('hide');
     //alert('merde');
     //$(this).tooltip('show');
     };
     */
    /*
     $(selecteur).mousemove(function(){
     $(selecteur).tooltip('hide');
     
     setTimeout(function(){
     $(this).tooltip('show'); 
     },1000);
     // show_tool_tip(this);
     
     //alert('merde');
     
     });
     */
    /*
     $(selecteur).mouseleave( function(){
     $(selecteur).tooltip('hide');
     //alert('merde');
     //$(this).tooltip('show');
     });
     */

    //ferme la bulle quand le focus se pert (s'il y a la souris dessus un autre)
    /*
     $(selecteur).focusout(function(){
     $(selecteur).tooltip('hide');
     //alert('merde');
     //$(this).tooltip('show');
     });
     */







    //console.log('showtooltip_with_sel');

    tooltip_initialise = true;
}

