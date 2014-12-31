<!DOCTYPE html>
<html>
	<head>
		<title>PCGamingWiki</title>
		<meta charset="utf-8"> 
		<style type="text/css">
			body, html {
				padding: 50px 10px;
				margin: 0;
				text-align: center;
				background-color: #E5ECF9;
				font: 400 16px 'Helvetica Neue', Helvetica, Arial, sans-serif;
				overflow-x: hidden;
			}

			* {
				box-sizing: border-box;
			}

			a {
				text-decoration: none;
			}

			a:hover {
				text-decoration: underline;
			}

			.nav {
				margin-top: 75px;
			}

			input[type=search] {
				height: 30px;
				width: 300px;
				background-color: transparent;
				border: 1px solid #1E3561;
				border-radius: 3px 0px 0px 3px;
				margin: 0;
				padding: 0px 6px;
				vertical-align: top;
				font-size: 16px;
				font-weight: 400;
				color: #1E3561;
			}

			::-webkit-input-placeholder {
				color: #1E3561;
			}

			::-moz-placeholder {
				color: #1E3561;
			}

			button {
				margin: 0;
				transform: translateX(-6px);
				border-radius: 0px 3px 3px 0px;
				background-color: #397ffb;
				border: 1px solid #397ffb;
				height: 30px;
				width: 60px;
				vertical-align: top;
			}

			svg {
				fill: #FFF;
			}

			.create-page-button {
				background-color: #397ffb;
				border-top-color: #397ffb;
				border-bottom-color: #1b61dd;
				box-shadow: inset 0px 0px 0px #1b61dd;
				color: #FFF;
				border-radius: 5px;
				width: 150px;
				height: 40px;
				font-size: 16px;
				padding: 10px 0px;
				margin-left: auto;
				margin-right: auto;
				transition: box-shadow 300ms ease, background-color 300ms ease; 
			}

			.create-page-button:hover {
				box-shadow: inset 0px -5px 0px #1b61dd;
				text-decoration: none;
			}

			.create-page-button:active {
				
				box-shadow: inset 0px 0px 0px #1b61dd;
			}

			.create-page-button a {
				color: #FFF;
				text-decoration: none;
			}
		</style>
	</head>
	<body>
	<div class="content">
		<?php
		// Config
		$config = array(
			'api_pcgw' => 'http://pcgamingwiki.com/w/api.php?action=askargs&conditions=Steam%20AppID::%APPID%&format=json',
			'api_steam' => 'http://store.steampowered.com/api/appdetails/?appids=%APPID%&filters=basic',
		);

		// Main code
		if ( !isset( $_GET['appid'] ) )
		{
			print "<p>Error: No appid provided.</p>";
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
				print "Error: Received invalid data from Semantic MediaWiki.";
			}
			else
			{
				$results = $json['query']['results'];

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
						print '<p>No page for the game ' . $results['name'] . ' exists, would you like to create it?</p>';
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