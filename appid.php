<?php
$config = array(
	'api_pcgw' => 'http://pcgamingwiki.com/w/api.php?action=askargs&conditions=Steam%20AppID::%APPID%&format=json',
	'api_steam' => 'http://store.steampowered.com/api/appdetails/?appids=%APPID%&filters=basic',
);

if ( !isset( $_GET['appid'] ) || $_GET['appid'] == "" )
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

	// Get first results url
	$first_result = reset( $results );
	$article_url = $first_result["fullurl"];
}

if ( count( $results ) == 1 )
{
	// Redirect
	header( 'Location: ' . $article_url );
	die();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>PCGamingWiki</title>
		<meta charset="utf-8">
		<meta name=viewport content="width=device-width, initial-scale=0.75">
		<link rel="shortcut icon" href="/images/WikiFavicon.png" />
		<style type="text/css">
		<?php include_once("style.css"); ?>
		</style>
	</head>
	<body>
		<div class="content">
			<?php
			if ( count( $results ) == 0 )
			{
				$url = str_replace( '%APPID%', $appid, $config['api_steam'] );

				// Fetch data
				$hcurl = curl_init( $url );
				curl_setopt( $hcurl, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $hcurl, CURLOPT_TIMEOUT, 5 );
				curl_setopt( $hcurl, CURLOPT_CONNECTTIMEOUT, 5 );

				$data = curl_exec( $hcurl );
				curl_close( $hcurl );
				$json = json_decode( $data, true );
				
				$results = $json[$appid]['data'];
				if ( count( $results ) != 0 )
				{
					print '<p>No page for ' . $results['name'] . ' exists, would you like to create it?</p>';
					print '<a href="http://pcgamingwiki.com/w/index.php?title=' . $results['name'] . '&amp;action=edit"><div class="create-page-button">Create Page</div></a>';
				}
				else
				{
					print "No such AppID.";
				}
			}
			else if ( count( $results ) > 1 )
			{
				print "<p>Multiple pages found for AppID. Which did you request?</p>";
				for ($i = 0; $i < count( $results ); ++$i)
				{
					print "<p><a href=" . current( $results )['fullurl'] . ">" . current( $results )['fulltext'] . "</a></p>";
					next( $results );
				}
			}
			?>
		</div>

		<div class="nav">
			<form role="search" action="http://pcgamingwiki.com/w/index.php" method="get">
				<input type="search" name="search" placeholder="Search PCGamingWiki">
				<button type="submit">
					<svg class="icon" height="20px" width="20px" viewBox="0 0 100 100" >
						<use xlink:href="#search-icon"></use>
					</svg>
				</button>
			</form>
		</div>

		<div class="referrer">
			<p>Prefer to go back? Click <a href="<?php print $_SERVER['HTTP_REFERER']; ?>">here</a>.</p>
		</div>

		<svg height="0" width="0" viewBox="0 0 100 100" >
			<g id="search-icon">
				<path d="M84.7,96.38L62.6,74.1c-6.1,4-13.3,6.3-21.1,6.3c-21.3,0-38.6-17.3-38.6-38.6c0-21.3,17.2-38.6,38.6-38.6
					c21.3,0,38.6,17.3,38.6,38.6c0,7.4-2.1,14.4-5.7,20.3L96.6,84.3c1.2,1.2,0.2,4-2.2,6.4l-3.3,3.3C88.7,96.4,85.9,97.4,84.7,96.3z
					M67.2,41.8c0-14.2-11.5-25.8-25.8-25.8S15.7,27.6,15.7,41.8c0,14.2,11.6,25.8,25.8,25.8S67.2,56.1,67.2,41.8z"/>
			</g>
		</svg>
	</body>
</html>