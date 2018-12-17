<?php
	if(isset($_POST['url'])){
		$urls = $_POST['url'];
		if(count($urls) >= URL_LENGTH){
			$findAndCompare = true;
			foreach($urls as $url){
				if(!filter_var($url, FILTER_VALIDATE_URL)){
					$findAndCompare = false;
					echo 'La URL "'.$url.'" non &egrave; valida.';
				}
			}
			if($findAndCompare){
				require_once('findAndCompare.php');
				new findAndCompare(array($urls[0], $urls[1]));
			}
		}
	}
?>