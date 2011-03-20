<?php
	// Debug
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');

	// Product
	define('CP_PRODUCT_SHORT', 'Extended Mail Header Parser');
	define('CP_PRODUCT_URL', 'http://mailheader.mattiasgeniar.be/');

	// General site
	define('CP_SITE_TITLE', 'Extended Mail Header Parser - Readability in your mail headers');	
	define('CP_SITE_SLOGAN', 'Making your mail headers readable again since 2010. Yes, it\'s new. Yes, it\'s a beta.');
	define('CP_LOGO_ALT', 'Extended Mail Header Parser');
	
	// General site - Meta Tags (useless?)
	define('CP_META_DESCRIPTION', 'Copy/Paste your mail headers, and we will output it in a readable format, with highlights on the most important pieces.');
	define('CP_META_KEYWORDS', 'Email, Mailheader, Headers, Parse, Email headers');
	
	// Database
	define('DB_USERNAME', 'mailheader');
	define('DB_PASSWORD', 'Epan59123_DnfnRwnxlE');
	define('DB_SERVERNAME', 'localhost');
	define('DB_DATABASE', 'mailheader');
	
	$dbconn = mysql_connect(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD) or die (mysql_error());
	$dbselected = mysql_select_db(DB_DATABASE, $dbconn) or die (mysql_error());
?>
