<?php
	require_once("config.php");
	require_once("includes/header.html");
?>		
	<div id="main">			
		<h1>Overview of parsed mail headers</h1>
		The following is a list of all the mail headers that this script can recognise, and the information we have on it.<br />
		
		<?php
			$sqlHeaderGroups = "SELECT * FROM header_group ORDER BY priority ASC";
			$resultHeaderGroups = mysql_query($sqlHeaderGroups) or die (mysql_error());
			
			if (mysql_num_rows($resultHeaderGroups) > 0) {				
				while ($rowGroup = mysql_fetch_object($resultHeaderGroups)) {					
					// Get all regex's for this group
					$sqlHeaders = "SELECT * FROM header WHERE groupid = ". $rowGroup->groupid;
					$resultHeaders = mysql_query($sqlHeaders);
					if (mysql_num_rows($resultHeaders) > 0) {
						echo "<h2>". $rowGroup->name ."</h2>";
						echo "<table width='100%' style='border: 1px solid gray; cell-padding: 3px'>";
						echo "	<tr>";
						echo "		<th width='20%'>Header</th>";
						echo "		<th width='35%'>Matching RegEx</th>";
						echo "		<th width='45%'>Explanation</th>";
						echo "	</tr>";
						while ($rowHeader = mysql_fetch_object($resultHeaders)) {
							echo "	<tr>";
							if ($rowHeader->headername != $rowHeader->truename && strlen($rowHeader->truename) > 0) {
								// Show an acronym
								echo "		<td valign='top'><acronym title='". $rowHeader->truename ."'>". $rowHeader->headername ."</acronym></td>";
							} else {
								// Show only the header name
								echo "		<td valign='top'>". $rowHeader->headername ."</td>";
							}
							echo "		<td valign='top' style='font-size: 11px';>". $rowHeader->preg_match ."</td>";
							echo "		<td valign='top'>". $rowHeader->explanation ."</td>";
							echo "	</tr>";
							//echo "- <b>". $rowHeader->headername ."</b>: ". $rowHeader->explanation ."<br />";
						}
						echo "</table>";
					}					
				}
			}
		?>
		
	</div>
<?php
	require_once("includes/footer.html");
?>