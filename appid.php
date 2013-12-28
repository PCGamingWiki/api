<?php
/**
 * Potential improvements:
 * - Caching
 * - return back to referrer if error?
*/

// Error reporting (comment for production):
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

// Config
$config = array(
	'api_url' => 'http://pcgamingwiki.com/wiki/Special:Ask/-5B-5BSteam-20AppID::%APPID%-5D-5D/format%3Djson', // Format of URL to get APPID data as json.
);


// Main code
if (!isset($_GET['appid'])) {
	die("Error: No appid provided.");
}
else {
	$appid = $_GET['appid'];

	// Construct api_url for appid
	$url = str_replace('%APPID%', $appid, $config['api_url']);

	// Fetch data
	$hcurl = curl_init( $url );
	curl_setopt( $hcurl, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $hcurl, CURLOPT_TIMEOUT, 5 );
	curl_setopt( $hcurl, CURLOPT_CONNECTTIMEOUT, 5 );

	$data = curl_exec( $hcurl );
	curl_close( $hcurl );

	// Read data as JSON
	$json = json_decode( $data, true );
	if (!$json) {
		die("Error: Received invalid data from Semantic MediaWiki.");
	}

	$results = $json['results'];
	if (count($results) == 0) {
		die("Error: No results from Semantic MediaWiki.");
	}

	// Get first results url
	$first_result = reset($results);
	$article_url = $first_result["fullurl"];

	// Redirect pls valve
	header('Location: ' . $article_url);
	die();
}
