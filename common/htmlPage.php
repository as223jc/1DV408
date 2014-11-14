<?php

class htmlPage {
	public function showHtml($body){
		echo '<!DOCTYPE HTML SYSTEM>
		<html>
		  <head>
		    <title></title>
			<link rel="stylesheet" type="text/css" href="style/style.css">
		    <meta http-equiv=\'content-type\' content=\'text/html; charset=utf8\'>
		  </head>
		  <body>
		  	' . $body . '
		  </body>
		</html>';
	}
}
