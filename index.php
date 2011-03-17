<?php
	require_once("config.php");
	require_once("includes/header.html");
?>		
	<div id="main">			
		<h1>Analyze mail headers</h1>
		Copy/paste your mailheaders into the form below, and click on <i>parse</i> to let the magic flow. 
		We'll analyze your mail headers, and output them in a more readable manner. Or at least, we'll try. This _is_ a beta.<br />
		<br />
		If you don't know what mailheaders are, or where you can find them, you're in the wrong place.<br />
		<br />
		<form method="post" action="parse.php">
			<textarea name="headers" cols="90" rows="15"></textarea><br />
			<input type="submit" name="cmdParse" value="Parse these headers" />
		</form>
	</div>
<?php
	require_once("includes/footer.html");
?>