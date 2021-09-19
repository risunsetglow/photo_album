<?php
function convert_float($str)
{
	$val = explode('/', $str) ;
	return (isset($val[1])) ? $val[0] / $val[1] : $str;
}

function get_datetime($exif) {
	$srcarray = preg_split("/[\s:]+/", $exif['EXIF']['DateTimeOriginal']);
	$dstarray['year'] = intval($srcarray[0]);
	$dstarray['month'] = intval($srcarray[1]);
	$dstarray['day'] = intval($srcarray[2]);
	$dstarray['hour'] = intval($srcarray[3]);
	$dstarray['minute'] = intval($srcarray[4]);
	$dstarray['second'] = intval($srcarray[5]);

	return $dstarray;
}

function get_gps($exif) {
	if (isset($exif['GPS']['GPSLatitude']) && isset($exif['GPS']['GPSLongitude'])) {
		$lat = convert_float($exif['GPS']['GPSLatitude'][0]) + convert_float($exif['GPS']['GPSLatitude'][1])/60 + convert_float($exif['GPS']['GPSLatitude'][2])/3600;
		if ($exif['GPS']['GPSLatitudeRef'] == 'S') {
			$lat *= -1;
		}
		$lon = convert_float($exif['GPS']['GPSLongitude'][0]) + convert_float($exif['GPS']['GPSLongitude'][1])/60 + convert_float($exif['GPS']['GPSLongitude'][2])/3600;
		if ($exif['GPS']['GPSLongitudeRef'] == 'W') {
			$lon *= -1;
		}
		return array($lat, $lon);
	} else {
		return;
	}
}

function get_address($lat, $lon) {
	$data = json_decode(@file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&language=ja&key=AIzaSyBj8Jx2ncUC1A2HyMuC9nWUhdApOrfSF7M'), true);
    return $data['results'][0]['formatted_address'];
}

function get_city($lat, $lon) {
	$data = json_decode(@file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&language=ja&key=AIzaSyBj8Jx2ncUC1A2HyMuC9nWUhdApOrfSF7M'), true);
	foreach($data['results'][0]['address_components'] as $value) {
		if ($value['types'][0] == 'locality') {
			return $value['long_name'];
		} elseif ($value['types'][0] == 'administrative_area_level_1') {
			return $value['long_name'];
		}
	}
}

function get_country($lat, $lon) {
	$data = json_decode(@file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&language=ja&key=AIzaSyBj8Jx2ncUC1A2HyMuC9nWUhdApOrfSF7M'), true);
	foreach($data['results'][0]['address_components'] as $value) {
		if ($value['types'][0] == 'country') {
			return $value['long_name'];
		}
	}
}

function get_city_and_country($lat, $lon) {
	$find_city = false;
	$data = json_decode(@file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&language=ja&key=AIzaSyBj8Jx2ncUC1A2HyMuC9nWUhdApOrfSF7M'), true);
	foreach($data['results'][0]['address_components'] as $value) {
		if ($value['types'][0] == 'locality') {
			$city = $value['long_name'];
			$find_city = true;
		} elseif ($value['types'][0] == 'administrative_area_level_1' && !$find_city) {
			$city = $value['long_name'];
		}
		if ($value['types'][0] == 'country') {
			$country = $value['long_name'];
		}
	}
	return $city . "," . $country;
}
?>
