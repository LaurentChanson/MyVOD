<?php


//Recup_Affiche::recuperer_fichier('http://fr.web.img6.acsta.net/medias/nmedia/18/79/51/17/19633720.jpg', '.');

class Recup_Affiche{
    
    //exemple http://fr.web.img6.acsta.net/medias/nmedia/18/79/51/17/19633720.jpg
    // ou
    //http://vignette1.wikia.nocookie.net/akira/images/0/0e/Akira-Poster-akira-13827694-1013-1500.jpg/revision/latest?cb=20131117120052
    static function recuperer_fichier($url,$dossier_destination='.',$nouveau_nom=null){
        //enlève la partie de droite s'il y a un point d'interrogation dans l'url
        $pos=strpos($url,'?');
        $url0=$url;
        if($pos!==false){
            $url0=substr($url,0,$pos);
        }
        //nettoie la partie apres l'extension
        //".jpg/revision/latest" deviendre ".jpg"
        $pos=strpos($url0,'.jpg');
        if($pos!==false){
            $url0=substr($url0,0,$pos+4);
        }
        
        //récupère le nom du fichier
        if($nouveau_nom==null){
            $nom_fichier=urldecode(basename($url0));
            //LC : 06/07/2018 : Fixe si le nom de fichier contient des "%20" et cie
            //var_dump($nom_fichier);
            
            //exit(0);
            
        }else{
            $nom_fichier=$nouveau_nom;
        }
        
        try {
            
            //récupère le fichier
            $fic=@file_get_contents($url);
            
            //var_dump($fic);
            
            
            if($fic==FALSE){
                return FALSE;
            }

            //vérifie si le fichier existe déjà ou non
            $fich_dest=$dossier_destination.'/'.$nom_fichier;
            //on renomme le futur fichier s'il existe
            $i=0;
            
            if(file_exists ($fich_dest)){
                //unlink($fich_dest);
                $extension=strrchr($nom_fichier,'.');
                
                
                while (file_exists ($fich_dest)){
                    $i++;
                    $new_name=  substr($nom_fichier, 0,  strlen($nom_fichier)-strlen($extension))."($i)".$extension;
                    $fich_dest=$dossier_destination.'/'.$new_name;
                    //var_dump($new_name);
                }
                $nom_fichier=$new_name;
                
                
                
                //var_dump($nom_fichier);
            }

            

            //enregistre sur le disque
            $fp = fopen($fich_dest, 'w');
            fwrite($fp, $fic);
            fclose($fp);

            return $nom_fichier;
            
        } catch (Exception $ex) {
            var_dump($ex);
            return FALSE;
        }
    }
    
    
    
    
}