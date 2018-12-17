<?php
	class findAndCompare{
		public function findAndCompare($urls = []){
			// Find links
			$data = [];
			foreach($urls as $url){
				$host = parse_url($url, PHP_URL_HOST);
				
				$html = $this->getHtml($url);
				$links = [];
				foreach($html->find('a') as $element){
					$href = $element->href;
					if($this->isInternalLink($host, $href)){
						$links[] = $href;
					}
				}
				
				$data[] = $links;
			}
			
			// Compare links
			$compared = [];
			foreach($data[0] as $url0){
				$path0 = $this->sanitizeUrlForCompare($url0);
				foreach($data[1] as $url1){
					$path1 = $this->sanitizeUrlForCompare($url1);
					similar_text($path0, $path1, $percent);
					
					$writeToArray = array_key_exists($url0, $compared) ? ($compared[$url0]['perc'] <= $percent) : true;
					
					if($writeToArray){
						$compared[$url0] = [
							'url0' => $this->sanitizeUrlPath($url0, $urls[0]),
							'url1' => $this->sanitizeUrlPath($url1, $urls[1]),
							'perc' => $percent
						];
					}
				}
			}
			
			// Rewrite perc value
			foreach($compared as &$comp){
				$comp['perc'] = number_format($comp['perc'], 2).'%';
			}
			
			// Download CSV
			$this->downloadCSV('report_'.time().'.csv', $compared);
		}
		
		public function getHtml($url){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$response = curl_exec($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			$body = substr($response, $header_size);
			curl_close($ch);
			
			return str_get_html($body);
		}
		
		public function isInternalLink($host, $href){
			if($href != '' && $href != '#' && $href != '/' && strpos($href, 'javascript:') === FALSE){
				$parseHost = parse_url($href, PHP_URL_HOST);
				if(($host == $parseHost || $parseHost == '') && $href[0] != '#'){
					$path = parse_url($href, PHP_URL_PATH);
					if($path != '' && $path != '/'){
						return true;
					}
				}
			}
			return false;
		}
		
		public function sanitizeUrlPath($path, $domain){
			$host = parse_url($domain);
			
			if(strpos($path, $host['host']) !== FALSE){
				if(strpos($path, '://') !== FALSE){
					return $path;
				}
				else{
					return $host['scheme'].':'.$path;
				}
			}
			else{
				if($path[0] == '/' && $path[1] == '/'){
					return $host['scheme'].'://'.$host['host'].substr($path, 1);
				}
				else if($path[0] == '/' && $path[1] != '/'){
					return $host['scheme'].'://'.$host['host'].$path;
				}
				else{
					return $path;
				}
			}
		}
		
		public function sanitizeUrlForCompare($url){
			$path = parse_url($url, PHP_URL_PATH);
			$basename = basename($path);
			$noext = strpos($basename, '.') === false ? $path : substr($path, 0, - strlen($basename) + strlen(explode('.', $basename)[0]));

			return strtolower(basename($noext));
		}
		
		public function downloadCSV($filename, $rows){
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="'.$filename.'"');

			$fp = fopen('php://output', 'wb');
			foreach($rows as $row){
				fputcsv($fp, $row);
			}
			fclose($fp);
			
			die;
		}
	}
?>