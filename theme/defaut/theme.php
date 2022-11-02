<?php


class theme_config{
    
    public static $repertoire="theme/defaut/"; 
    public static $href_css;
    public static $repertoire_img;
    
    
}

//theme_config::$href_css=theme_config::$repertoire."bootstrap/bootstrap-slate.css";
theme_config::$href_css=theme_config::$repertoire."myvod.css";
//utilisé pour le favicon et le beandeau
theme_config::$repertoire_img=theme_config::$repertoire."img/";