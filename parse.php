<?php
	require_once("config.php");
	require_once("functions.php");
	require_once("includes/header.html");
?>		
	<div id="main">			
		<div id="header_title" style="width: 300px; float: left; ">
			<h1>Analyzing results</h1>
		</div>
		
		
		<?php
			if (isset($_POST['cmdParse']) && strlen($_POST['headers']) > 100) {
				$headers = htmlspecialchars($_POST['headers']);				
				$plainHeaders = plainFormatHeaders($headers);
				$travelPath = getTravelPath($plainHeaders);
				$arrValueMaps = getValueMappings();
				
				// Start outputting
			?>
				<div id="header_right" style="width: 400px; float: right;  text-align: right">
					<a href="#" onclick="popup('popUpDiv');">Not working as expected? Have a tip?</a>
					&nbsp; 
					<a href="#" onclick="popup('popUpDiv');">
						<img src="images/question.png" border="0" style="vertical-align: middle">
					</a>
					
				</div>
				
				<div id="blanket" style="display: none">
				
				</div>
				<div id="popUpDiv" style="display: none;">
					<div style="width: 400px; float: left;">
						<h1>Send in a bugreport</h1>
					</div>
					<div style="width: 150px; float: right">
						<a href="#" onclick="popup('popUpDiv');">[X] close this window</a>			
					</div>
					<br /><br />
					If the output is not what you expected, review the information below and click on "submit" so I can further troubleshoot this bug.
					Please <u>remove any private data</u> you do not want to share.
					<br />
					<form method="post" action="bugreport.php">
						<table width="100%" border="0">
							<tr>
								<td colspan="2"><h2>Your mailheaders</h2></td>
							</tr>
							<tr>
								<td colspan="2"><textarea name="bugreport_headers" cols="80" rows="6"><?php echo $headers; ?></textarea></td>
							</tr>
							<tr>
								<td colspan="2"><h2>Remarks</h2><i>Optionally tell me what problems you experienced.<i></td>
							</tr>
							<tr>
								<td colspan="2"><textarea name="bugreport_remarks" cols="80" rows="4"></textarea>							
							</tr>	
							<tr>
								<td colspan="2"><h2>Optional fields</h2></td>
							</tr>
							<tr>
								<td>Your email</td>
								<td><input type="text" name="bugreport_email" size="30"> <i>(if you want me to get back to you)</i></td>
							</tr>							
							<tr>
								<td>&nbsp;</td>
								<td><input type="submit" name="bugreport_submit" value="Submit remark"></td>
							</tr>
						</table>
					</form>
				</div>
			
				<br />
				<h2>Travel path of mail</h2>
				<?php
					if (is_array($travelPath) && count($travelPath) > 0) {
						echo "<table width='90%' style='border: 1px solid gray; margin-top: 15px'>";
						echo "<tr>
								<th>Hop</th>
								<th>From</th>
								<th>To</th>
								<th>Delay</th>
								<th>Time</th>
							  </tr>";
							  
						$timingPrev = 0;
						for ($i = count($travelPath)-1; $i >= 0; $i--) {							
							$mailhopFrom = parseMailHopFrom ($travelPath[$i][2]);
							$mailhopBy = parseMailHopBy ($travelPath[$i][3]);
							
							$strDateCurrent = $travelPath[$i][4];
							$timeCurrent = strtotime($strDateCurrent);
							$hopDelay = "";
							if ($timingPrev == 0) {
								// First hop
								$hopDelay = "";
								$timingPrev = $timeCurrent;
							} else {
								// Compare to previous hop
								$hopDelay = $timeCurrent - $timingPrev;
								$timingPrev = $timeCurrent;
							}
							
							echo "<tr>
									<td>#". (count($travelPath) - $i) ."</td>";
									
							// The "FROM" line"
							if (is_array($mailhopFrom) && array_key_exists("resolved", $mailhopFrom) && strlen($mailhopFrom["resolved"]) > 0) {
								$mailhopFrom["domain"] = str_replace("from ", "", $mailhopFrom["domain"]);
								echo "	<td><acronym title=\"". $mailhopFrom["resolved"] ."\">". $mailhopFrom["domain"] ."</acronym></td>";
							} else {
								if (!array_key_exists("domain", $mailhopFrom) || strlen($mailhopFrom["domain"]) == 0)
									$mailhopFrom["domain"] = "unknown";
									
								$mailhopFrom["domain"] = str_replace("from ", "", $mailhopFrom["domain"]);
								echo "	<td>". $mailhopFrom["domain"] ."</td>";
							}
							
							// The "TO" line
							if (is_array($mailhopBy) && array_key_exists("resolved", $mailhopBy) && strlen($mailhopBy["resolved"]) > 0) {
								echo "	<td><acronym title=\"". $mailhopBy["resolved"] ."\">". $mailhopBy["domain"] ."</acronym></td>";
							} else {
								echo "	<td>". $mailhopBy["domain"] ."</td>";
							}
							
							// The rest
							$hopDelayText = secs_to_h($hopDelay);
							echo "	<td><acronym title=\"". $hopDelay ."s\">". $hopDelayText ."</acronym></td>
									<td>". date("H:i:s", $timeCurrent) ."</td>
								   </tr>";
							//echo "From: ". $mailhopFrom["domain"] .", By: ". $mailhopBy["domain"] ."<br />";
						}
						echo "</table>";
					} else {
						echo "Hmm. It looks as though I couldn't decypher the mailhops this e-mail passed.<br />
								If you believe the headers are correct, please click <a href=\"#\" onclick=\"popup('popUpDiv');\">this link</a> to <a href=\"#\" onclick=\"popup('popUpDiv');\">file a bugreport</a> which I can process. The form is pre-filled, so it takes less than 1 second!<br />
								<br />
								I need your input to make this application better!<br />";
					}
				?>
				
				<?php
					$arrMatchedHeaders = array();
					$plainHeadersFormatted = $plainHeaders;
					$sqlHeaderGroups = "SELECT * FROM header_group ORDER BY priority ASC";
					$resultHeaderGroups = mysql_query($sqlHeaderGroups) or die (mysql_error());
					
					if (mysql_num_rows($resultHeaderGroups) > 0) {
						while ($rowGroup = mysql_fetch_object($resultHeaderGroups)) {
							// Get all regex's for this group
							$sqlHeaders = "SELECT * FROM header WHERE groupid = ". $rowGroup->groupid;
							$resultHeaders = mysql_query($sqlHeaders);
							
							// Some variables for enabling/disabling the output
							$groupHasMatches = false;
							$arrHeaderMatches = array();
							
							if (mysql_num_rows($resultHeaders) > 0) {
								while ($rowHeader = mysql_fetch_object($resultHeaders)) {
									$valueMatch = doPregMatch($rowHeader->preg_match, $plainHeaders);
									if (strlen($valueMatch) > 0) {
										$groupHasMatches = true;
										$arrHeaderMatches[] = array("headerid" => $rowHeader->headerid,
																	"headername" => $rowHeader->headername,
																	"explanation" => $rowHeader->explanation,
																	"value" => $valueMatch);
																	
										// Keep track of what matched
										$plainHeadersFormatted = preg_replace($rowHeader->preg_match, '<font color="red">'. $rowHeader->truename .'</font>: <b><font color="darkgreen">${1}</font></b>', $plainHeadersFormatted);
									}									
								}
								
								if ($groupHasMatches) {
									// Show the group and the found variables
									echo "<div style='border: 1px dotted gray; margin-top: 15px; padding: 4px; '>";
									echo "<h2>". $rowGroup->name ."</h2><br />";
									foreach ($arrHeaderMatches as $match) {
										// See if we can map the value
										$valueMapped = matchHeaderValueMap($match["headerid"], $match["value"], $arrValueMaps);
									
										echo "<b><acronym title=\"". $match["explanation"] ."\">". $match["headername"] ."</acronym></b>: ". $match["value"] ."<br />";
									}
									//echo "<hr style='border: 1px solid gray' />";
									echo "</div>";
								}
							}
						}

						// For the normal "Received: blabla" headers, just place them in blue
						// so they stand out
						$plainHeadersFormatted = preg_replace("|^Received: (.*)|mi", '<font color="blue">Received: ${1} </font>', $plainHeadersFormatted);
					}
				?>
				
				<br /><br />
				<div style='border: 1px dotted gray; width:100%'>
					<h2>Formatted headers</h2>
					These are the headers you submitted, but formatted in a more orderly fashion. The headers we uncovered, are highlighted in color.<br />In <font color="blue">blue are the mailhops</font> that passed. <font color="red">Red is the header</font> and <font color="darkgreen">green is the value</font> of that header.<br /><br />
					<div style='background-color: #EFEFEF; overflow: auto; width: 100%;'>
						<?php echo nl2br($plainHeadersFormatted) ?>
					</div>
				</div>
			<?php
			} else {
				echo "<br /><br />Sorry, I detect invalid input.";
			}
		?>
		<br /><a href="<?php echo CP_PRODUCT_URL; ?>">Input new headers</a>.
	</div>
<?php
	require_once("includes/footer.html");
?>
