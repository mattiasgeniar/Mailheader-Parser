<?php
	// General functions
	function plainFormatHeaders ($headers) {
		// Strip all special things
		while (strstr($headers, "  "))	// Loop until all double spaces are catched
			$headers = str_replace("  ", " ", $headers);		// Double space => single space
			
		$headers = str_replace("\r", "\n", $headers);			// Remove \r's

		// Remove all double new lines
		while (strstr($headers, "\n\n")) // Loop until all double new lines are catched
			$headers = str_replace("\n\n", "\n", $headers);

		// Remove all double spaces (who does this anyway?)
                while (strstr($headers, "  ")) // Loop until all double spaces are catched
                        $headers = str_replace("  ", " ", $headers);

		$headers = str_replace("\n\t", " ", $headers); 		// No enter+tabs
		$headers = str_replace("\n ", " ", $headers); 		// No enter+space
		$headers = str_replace("\nby ", " by ", $headers);	// A line beginning with "by" is appended to the previous
		$headers = str_replace("\nfor ", " for ", $headers); 	// Same goes for lines beginning with "for"
		$headers = str_replace("\nid ", " id ", $headers);	// And 'id'
		$headers = str_replace(",\n", ", ", $headers);		// Remove the comma + newline
		$headers = str_replace(";\n", ", ", $headers);		// Remove the pointcomma + newline
		// The following line _needs_ single quotes, no double quotes
		$headers = str_replace(';\n', "; ", $headers);		// Remove the ; and newline
		$headers = str_replace("\n(", " (", $headers);
		
		return $headers;
	}
	
	function getTravelPath ($headers) {
		// In its most simple form, the "received" header is like:
		// Received:( from )?(.*) by (.*); (.*)
		// $1 => from, $2 => by, $3 => date
		
		$preg_match = "|Received: ( from )?(.*)by (.*);(.*)|i";
		$results_complete = "";
		preg_match_all($preg_match, $headers, $results_complete, PREG_SET_ORDER);	

		// Sometimes, headers dont come as 'Received: from xx by xx' but as 'Received: by xx'
		/*$preg_match = "|Received: by (.*);(.*)|i";
		$results_partial = "";
		preg_match_all($preg_match, $headers, $results_partial, PREG_SET_ORDER);
		if (is_array($results_partial) && count($results_partial) > 0) {
			foreach ($results_partial as $result_partial) {
				$results_complete[] = 
			
			}
		}
		die();*/
		
		return $results_complete;
	}
	
	function parseMailHopFrom ($hop) {		
		// It could be like: "smtp.nucleus.be ([188.93.153.68])"
		$arrResult = array();
		$preg_match = "|(.*)\(\[(.*)\]\)|i";
		$result = "";
		if (preg_match($preg_match, $hop, $result)) {
			$arrResult["domain"] = $result[1];
			$arrResult["resolved"] = $result[2];
			
			return $arrResult;
		}
		
		// Or: "smtp.nucleus.be (188.93.153.68)"
		$arrResult = array();
		$preg_match = "|(.*)\((.*)\)|i";
		$result = "";
		if (preg_match($preg_match, $hop, $result)) {
			$arrResult["domain"] = $result[1];
			$arrResult["resolved"] = $result[2];
			
			return $arrResult;
		}
		
		// Or it could be like: "web01.nucleus.be (web01.nucleus.be [77.73.96.106])";
		$arrResult = array();
		$preg_match = "|(.*)\((.*) \[(.*)\]\)|i";
		$result = "";
		if (preg_match($preg_match, $hop, $result)) {
			$arrResult["domain"] = $result[1];
			$arrResult["hostname"] = $result[2];
			$arrResult["resolved"] = $result[3];
			
			return $arrResult;
		}
		
		// Return the value
		return $arrResult;
	}
	
	function parseMailHopBy ($hop) {	
		// Ideally: "web01.nucleus.be (8.13.8/8.13.8) with ESMTP id oA2KpHEQ019481"
		$arrResult = array();
		$preg_match = "|(.*)\((.*)\)(.*)?|i";
		$result = "";
		if (preg_match($preg_match, $hop, $result)) {
			$arrResult["domain"] = $result[1];
			$arrResult["resolved"] = $result[2];
			
			if (preg_match("|[with] (.*)|i", $result[1], $result_sec)) {				
				$arrResult["mailsystem"] = $result_sec[1];
				
				// Remove the "with <sender>" from the domain
				$arrResult["domain"] = str_replace("with ". $arrResult["mailsystem"], "", $arrResult["domain"]);				
			}
			
			return $arrResult;
		}
		
		// Possibly: "smtp.nucleus.be with SMTP"
		$arrResult = array();
		$preg_match = "|(.*) ([with])* (.*)|i";
		$result = "";
		if (preg_match($preg_match, $hop, $result)) {
			$arrResult["domain"] = $result[1];
			$arrResult["mailsystem"] = $result[2];
			
			return $arrResult;
		}
	}
	
	function getSimpleHeader($headers, $type) {
		// Possible: 
		// Type: Content
		$preg_match = "|^". $type .": (.*)|mi";
		$result = "";
		if (preg_match($preg_match, $headers, $result)) {
			return $result[1];
		}
	}
	
	function doPregMatch ($preg_match, $data) {
		$result = "";
		if (preg_match($preg_match, $data, $result)) {
			return $result[1];
		}
	}
	
	function getValueMappings() {
		$sqlValue = "SELECT * FROM valuemap ORDER BY PRIORITY";
		$resultValue = mysql_query($sqlValue);
		
		$arrValueMaps = array();
		while ($row = mysql_fetch_object($resultValue)) {
			if (!array_key_exists($row->headerid, $arrValueMaps))
				$arrValueMaps[$row->headerid] = array();
				
			$arrValueMaps[$row->headerid][] = array(
					"valuemapid" => $row->valuemapid,
					"comparison" => $row->comparison,
					"value" => $row->value,
					"map" => $row->map,
			);
		}
		
		return $arrValueMaps;
	}
	
	function matchHeaderValueMap ($headerid, $header_value, $arrValueMap) {
		if (array_key_exists($headerid, $arrValueMap)) {
			$arrHeaderValueMap = $arrValueMap[$headerid];
			
			if (is_array($arrHeaderValueMap) && count($arrHeaderValueMap) > 0) {
				foreach ($arrHeaderValueMap as $map) {
					
				}
			}
		} else {
			return false;
		}
	}
?>
