<?php
/**
Artists.php
Return list of artists based on filters
Minimum PHP version required: 5.5

**/
$filtered_artists = [];
$artists = [];
$KEYS_TO_FILTER = [
	'name' => FILTER_SANITIZE_ENCODED,
	'dj_name' => FILTER_SANITIZE_ENCODED,
	'all_styles' => FILTER_SANITIZE_ENCODED];//php 7 can make this a defined

//enable CORS and JSON
 header("Access-Control-Allow-Origin: *");
 header('Content-Type: application/json');


//get CSV to artists array - from Gdrive?
if (($handle = fopen(__DIR__. "/Code Example Data - Sheet1.csv", "r")) !== FALSE) {
	$headers = fgetcsv($handle, 0, ',');
    while (($row = fgetcsv($handle, 0, ",")) !== FALSE) {
        $num = count($row);
        $artists[] = array_combine($headers, $row);
	}
    fclose($handle);
}

if (!is_array($artists) || count($artists) == 0 ){
	return json_encode($filtered_artists);
}

//sort artists by rating
usort($artists, function($a, $b){
	if ($a['rating'] == $b['rating']){
		return 0;
	}
	return ($a['rating'] < $b['rating']) ? 1 : -1;//rating desc
	//todo for PHP 7, use spaceship operator $b['rating'] <=> $a['rating']
});

//sum styles into all_styles, first_name + last_name into name
foreach ($artists as &$artist){
	$artist['all_styles'] = implode(', ', [$artist['style_1'], $artist['style_2'], $artist['style_3']]);
	$artist['name'] = implode(' ', [$artist['first_name'], $artist['last_name']]);
}
/*
Full text filter
*/
$filters = filter_input_array(INPUT_GET, $KEYS_TO_FILTER);//only allow filtering by those fields
$filters = array_filter($filters, function($filter_value){ //remove unset $_GET variables
	return !is_null($filter_value);
});

 foreach ($artists as $artist){
	 $match = true;
	 foreach ($filters as $key_to_filter => $value_to_filter){
		 if (stripos($artist[$key_to_filter], $value_to_filter) === false){
			 $match = false;
			 break;
		 }
	 }
	 if ($match){
		 array_unshift($filtered_artists, $artist);
	 }
 }

//todo: remove all_styles from returned result to improve performance
//return json array of matched artists
echo json_encode($filtered_artists);
