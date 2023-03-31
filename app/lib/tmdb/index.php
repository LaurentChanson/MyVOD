<?php
//https://github.com/glamorous/TMDb-PHP-API
include('TMDb.php');

	$key='6fcbc54107bbf30cfe5b2b967374424e';

    // Default English language
    //$tmdb = new TMDb($key);

    // Set-up the class with your own language
    //$tmdb = new TMDb($key, 'fr');

    // If you want to load the TMDb-config (default FALSE)
    $tmdb = new TMDb($key, 'fr', TRUE);
	
	
	    // After initialize the class
    // First request a token from API
    $token = $tmdb->getAuthToken();
	
	$r = $tmdb->searchMovie('mad max');
	
	var_dump($r);
	
	$imbd_id=8810;
	
	//Get Movie with other return format than the default and with an IMDb-id
	$json_movie_result = $tmdb->getMovie($imbd_id);
	
	var_dump($json_movie_result);
	
	$c= $tmdb->getMovieCast($imbd_id);
	var_dump($c);
	
	
	//Filepath retrieved from a method (Backdrop image)
	$filepath = '/oNI8l7GL3fvDIP9mtBSMku4gQde.jpg';

	//Get image URL for the backdrop image in its original size
	$image_url = $tmdb->getImageUrl($filepath, TMDb::IMAGE_POSTER, 'original');
	
	var_dump($image_url);