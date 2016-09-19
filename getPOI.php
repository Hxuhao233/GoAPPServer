<?php
/**
 * 查找POI
 */
class getPOI{

	public static function getPOIData($data){
		//data : query&location
		$locationAndPlace = explode("&", $data);
		$url="http://api.map.baidu.com/place/v2/search?query=".$locationAndPlace[0]."&location=".$locationAndPlace[1]."&radius=2000&output=json&ak=0R3eARdSVA3qYvyy1WwwuPLA";

		//$curl = curl_init();
		//curl_setopt($curl, CURLOPT_URL, $url);
		$POIdata = file_get_contents($url);
		var_dump($POIdata);
		return $POIdata;
	}

}
?>