<?php
// Error reporting (comment for production):
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

// Config
$config = array(
	'api_pcgw' => 'http://pcgamingwiki.com/w/api.php?action=askargs&conditions=Steam%20AppID::%APPID%&format=json',
);

// Main code
if ( !isset( $_GET['appid'] ) )
{
	die( "Error: No appid provided." );
}
else
{
	$appid = $_GET['appid'];

	// Construct api_url for appid
	$url = str_replace( '%APPID%', $appid, $config['api_pcgw'] );

	// Fetch data
	$hcurl = curl_init( $url );
	curl_setopt( $hcurl, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $hcurl, CURLOPT_TIMEOUT, 5 );
	curl_setopt( $hcurl, CURLOPT_CONNECTTIMEOUT, 5 );

	$data = curl_exec( $hcurl );
	curl_close( $hcurl );

	// Read data as JSON
	$json = json_decode( $data, true );
	if ( !$json )
	{
		die( "Error: Received invalid data from MediaWiki." );
	}

	$results = $json['query']['results'];

	if ( count($results) != 1 )
	{
		header( 'Location: http://pcgamingwiki.com/api/user.php?appid=' . $appid );
		die();
	}

	// Get first results url
	$first_result = reset( $results );
	$article_url = $first_result["fullurl"];

	// Redirect
	header( 'Location: ' . $article_url );
	die();
}