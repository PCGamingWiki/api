<?php
$config = array(
	'api_pcgw_store' => 'http://pcgamingwiki.com/w/api.php?action=askargs&conditions=GOGcom%20page::%APPID%&format=json',
	'api_pcgw_forum' => 'http://pcgamingwiki.com/w/api.php?action=askargs&conditions=GOGcom%20forum::%APPID%&format=json',
);

if ( !isset( $_GET['page'] ) || $_GET['page'] == "" || !isset( $_GET['forum'] ) || $_GET['forum'] == "" )
{
	die( "Error: No appid provided." );
}
else if ( isset( $_GET['page'] ) || $_GET['page'] != "" )
{
	$appid = $_GET['page'];

	// Construct api_url for appid
	$url = str_replace( '%APPID%', $appid, $config['api_pcgw_store'] );

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
else if ( isset( $_GET['forum'] ) || $_GET['forum'] != "" )
{
	$appid = $_GET['forum'];

	// Construct api_url for appid
	$url = str_replace( '%APPID%', $appid, $config['api_pcgw_store'] );

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
		<link rel="stylesheet" href="style.css" />
	</head>
	<body>
		<div class="content">
			<?php
			if ( count( $results ) == 0 )
			{
				$title = $_GET['title'];
				print '<p>No page for ' . $title . ' exists, would you like to create it?</p>';
				print '<a href="http://pcgamingwiki.com/w/index.php?title=' . $title . '&amp;action=edit"><div class="create-page-button">Create Page</div></a>';
			}
			else if ( count( $results ) > 1 )
			{
				print "<p>Multiple pages found for value. Which would you like to see?</p>";
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
				<path d="M84.74,96.278L62.607,74.147c-6.063,3.969-13.313,6.296-21.138,6.296c-21.33,0-38.579-17.289-38.579-38.619
					c0-21.29,17.247-38.578,38.579-38.578c21.294,0,38.576,17.288,38.576,38.578c0,7.446-2.097,14.386-5.724,20.264L96.61,84.37
					c1.183,1.15,0.19,4.011-2.178,6.372l-3.318,3.328C88.746,96.431,85.885,97.423,84.74,96.278z M67.227,41.824
					c0-14.233-11.524-25.758-25.759-25.758S15.673,27.591,15.673,41.824c0,14.235,11.561,25.795,25.795,25.795
					S67.227,56.062,67.227,41.824z"/>
			</g>
		</svg>
	</body>
</html>