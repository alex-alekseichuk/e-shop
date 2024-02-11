<?php

	// Настройки для подключения к БД на localhost

	define("HostName", "localhost");
	define("UserName", "root");
	define("Password", "");
	define("dbName", "nebu");


	define("LINKS_PARTS", "infit_ru_links_catalog");
	define("LINKS", "infit_ru_links");

	mysql_connect(HostName, UserName, Password);
	mysql_select_db(dbName);


	/*Запрос на выборку информации робо всех товарах из БД*/
	$result = @mysql_query("select * from ".CATALOG." order by priority desc");

	function catalog_menu($sql_result) {
		print '<table cellpadding = "0" cellspacing = "0" width = "190" align = "right" id = "main_menu">';
		while($info = @mysql_fetch_array($sql_result)) {
			print '<tr><td height = "20"><a href="/catalog/'.$info['name_for_url'].'.html"><img src="http://www.ingalatory.ru/img/li-dot.gif" width="8" height="7" border="0">'.$info['name'].'</a></td></tr>';
			print '	<tr><td background = "http://www.ingalatory.ru/img/line_bgr.gif"><img src = "http://www.ingalatory.ru/img/0.gif" height = "3" width = "1"></td></tr>';
		}
		print '</table>';
	}

	function NoCache() {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
	}

	if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])  === false)
		setCookie('referer', $_SERVER['HTTP_REFERER']);

include($_SERVER['DOCUMENT_ROOT'].'/functions/ip_statistics.php');

$NOW = time();

$full_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$result_ =
	mysql_query(
		'SELECT
			*
		FROM
			ip_statistics_pages
		WHERE
			url="'.$full_url.'"');


?>