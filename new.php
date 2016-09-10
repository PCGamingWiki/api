<?php
$config = array(
	'pcgw_api_gogstore' => 'http://pcgamingwiki.com/w/api.php?action=askargs&conditions=GOGcom%20page::%API%&format=json',
	'pcgw_api_gogforum' => 'http://pcgamingwiki.com/w/api.php?action=askargs&conditions=GOGcom%20forum::%API%&format=json',
	'pcgw_api_winehq' => 'http://pcgamingwiki.com/wiki/Special:Ask/-5B-5BWineHQ-20AppID::%API%-5D-5D/format%3Djson',
	'pcgw_api_steamappid' => 'http://pcgamingwiki.com/w/api.php?action=askargs&conditions=Steam%20AppID::%API%&format=json',
	'steam_api' => 'http://store.steampowered.com/api/appdetails/?appids=%API%&filters=basic',
);

function fetch_data( $info, $api ) {
	$url = str_replace( '%API%', $_GET[$info], $config[$api] );

	$hcurl = curl_init( $url );
	curl_setopt( $hcurl, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $hcurl, CURLOPT_TIMEOUT, 5 );
	curl_setopt( $hcurl, CURLOPT_CONNECTTIMEOUT, 5 );

	$data = curl_exec( $hcurl );
	curl_close( $hcurl );

	$json = json_decode( $data, true );

	if ( !$json ) {
		die( "Error: Received invalid data from MediaWiki." );
	}

	return $data;
}

function get_title() {
	if ( !empty( $_GET['title'] ) ) {
		return $_GET['title'];
	}
	else {
		fetch_data( $_GET['steamappid'], 'steam_api')
	}
}

if( !empty( $_GET['steamappid'] ) ) {
	$results = fetch_data( 'steamappid', 'pcgw_api_steamappid' )['query']['results'];
}
else if( !empty( $_GET['gogpage'] ) ) {
	$results = fetch_data( 'gogpage', 'pcgw_api_gogstore' )['query']['results'];
}
else if( !empty( $_GET['gogforum'] ) ) {
	$results = fetch_data( 'gogforum', 'pcgw_api_gogforum' )['query']['results'];
}
else if( !empty( $_GET['winehq'] ) ) {
	$results = fetch_data( 'winehq', 'pcgw_api_winehq' )['query']['results'];
}
else {
	die( "Error: No data provided." );
}

if( count( $results ) == 1 ) {
	$first_result = reset( $results );
	$article_url = $first_result["fullurl"];	

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
			if( count( $results ) == 0 ) {
				$title = get_title();
				print '<p>No page for ' . $title . ' exists, would you like to create it?</p>';
				print '<a href="http://pcgamingwiki.com/w/index.php?title=' . $title . '&amp;action=edit"><div class="create-page-button">Create Page</div></a>';
			}
			else if( count( $results ) > 1 ) {
				print "<p>Multiple pages found for value. Which would you like to see?</p>";
				for ($i = 0; $i < count( $results ); ++$i) {
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

		<svg height="0" width="0" viewBox="0 0 100 100">
			<g id="search-icon">
				<path d="M84.7,96.38L62.6,74.1c-6.1,4-13.3,6.3-21.1,6.3c-21.3,0-38.6-17.3-38.6-38.6c0-21.3,17.2-38.6,38.6-38.6
					c21.3,0,38.6,17.3,38.6,38.6c0,7.4-2.1,14.4-5.7,20.3L96.6,84.3c1.2,1.2,0.2,4-2.2,6.4l-3.3,3.3C88.7,96.4,85.9,97.4,84.7,96.3z
					M67.2,41.8c0-14.2-11.5-25.8-25.8-25.8S15.7,27.6,15.7,41.8c0,14.2,11.6,25.8,25.8,25.8S67.2,56.1,67.2,41.8z"/>
			</g>
		</svg>
	</body>
</html>