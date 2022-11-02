<?php
    // Charger le fichier.
    require_once "../api-allocine-helper.php";
    
    // Créer un objet AlloHelper.
    $allohelper = new AlloHelper;

    

    
    
    // Définir les paramètres
    $motsCles = "The Dark Knight";
    //$motsCles="hioefilhofihoefzeihozefiho";
    $page = 1;
    
    
    $code=115362;
    $profile = 'small';
    
    //'small', 'medium', 'large', 1 pour 'small', 2 pour 'medium', 3 pour 'large'
    
    
    try
    {
        
        
        
            // Some presets
    
        
        
        
        
        // Envoi de la requête
        $movie = $allohelper->movie( $code, $profile );
        
        // Afficher le titre
//        echo "Titre du film: ", $movie->title, PHP_EOL;
//        
//        // Afficher toutes les données
//        print_r($movie->getArray());
        
    }
    catch( ErrorException $error )
    {
        // En cas d'erreur
        echo "Erreur n°", $error->getCode(), ": ", $error->getMessage(), PHP_EOL;
    }
    
    
    
    
    
    
    
    
    
    
    
    echo('<hr>');
    
    // Il est important d'utiliser le bloc try-catch pour gérer les erreurs.
    try
    {
        // Envoi de la requête avec les paramètres, et enregistrement des résultats dans $donnees.
        $donnees = $allohelper->search( $motsCles, $page );
        
         print_r($donnees['movie'][0]);
        
        // Affichage des informations sur la requête
        echo "<pre>", print_r($allohelper->getRequestInfos(), 1),  "</pre>";
        
        // Pas de résultat ?
        if ( count( $donnees['movie'] ) < 1 )
        {
            // Afficher un message d'erreur.
            echo '<p>Pas de résultat pour "' . $motsCles . '"</p>';
        }
        
        else
        {
            // Pour chaque résultat de film.
            foreach ( $donnees['movie'] as $film )
            {
                // Afficher le titre.
                echo "<h2>" . $film['title'] . "</h2>";
            }
            
            
        }
        
        
        
        
        
    }
    
    // En cas d'erreur.
    catch ( ErrorException $e )
    {
        // Affichage des informations sur la requête
        echo "<pre>", print_r($allohelper->getRequestInfos(), 1), "</pre>";
        
        // Afficher un message d'erreur.
        echo "Erreur " . $e->getCode() . ": " . $e->getMessage();
    }
    
    
    
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
?>
