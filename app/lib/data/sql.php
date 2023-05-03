<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sql
 *
 * @author Laurent
 */
class sql {
    public static function chaine_vers_sql($s,$valeurSiNull='NULL') {
        if ($s==null){return $valeurSiNull;}
        return "'".str_replace("'", "''", $s)."'";
    }
    
    
    public static function entier_vers_sql($i) {
        if ($i==null){return 'NULL';}
        return ''. $i;
    }
    
    public static function float_vers_sql($i) {
        if ($i==null){return 'NULL';}
        return ''. str_replace(',','.',$i);
    }
    
    
    /**
     * prend une date yyyy/mm/dd ou yyyy-mm-dd
     * @param type $d
     * @return string chaine formatée sql
     */
    public static function date_vers_sql($d) {
        //var_dump($d);
        if ($d==null){return 'NULL';}
        return "DATE('". str_replace('/', '-', $d) ."')";
    }
    
    

    public static function add_where_OR(&$where, $condition_unitaire, $prefix_1ere_fois = 'WHERE ') {
        if (strlen($where) == 0) {
            $where.=$prefix_1ere_fois . $condition_unitaire;
        } else {
            $where.="\n OR " . $condition_unitaire;
        }
    }

    public static function add_where_AND(&$where, $condition_unitaire) {
        if (strlen($where) == 0) {
            $where.='WHERE ' . $condition_unitaire;
        } else {

            $where = str_replace('WHERE', 'WHERE (', $where);

            $where.=")\n AND " . $condition_unitaire;
        }
    }
    
    
    
}
