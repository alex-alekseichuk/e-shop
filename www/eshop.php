<?php
$time_start = microtime(1);
global $appRoot;
$appRoot = $_SERVER['DOCUMENT_ROOT'] . '/';

require_once($appRoot.'build/baseconnect.php5');
//require_once('blocks.inc');
//require_once('dbfunc.inc');

/*
//Если это просмотр конкретной новости
if(!empty($_GET['id']))
{
	$articles = getNews(1, $sub, 'arts', $_GET['id']);
	//Title страницы должен быть такой же как заголовок новости
	$optimization_data['page_title'] = $articles[0]['title'];
}
*/














include_once("include/core.php");
include_once("include/lang.php");
include_once("include/storage.php");



class CLoggedPage extends CPage
{
	public function init()
	{
		if (empty($_COOKIE['INF_USER_LOGIN']))
		{
			redirect("?p=cat");
		}

		parent::init();
	}
}


class CRegularPage extends CPage
{
	public function __construct($pageName, $pageAttrs)
	{
		parent::__construct("html/public/page.html", $pageAttrs);

		$this->add(new CHtmlBlock("content", "html/" . $pageName . ".html"));
	}

}




class CPublicApp extends CWebApp
{

	public function __construct()
	{
//			"static"		=> Array("title" => "Static page",		),
//			"login"			=> Array("title" => "Вход клиента",			"class" => "CLoginPage",	"file" => "login.php", )
		$this->m_pages = Array (

			"cat"			=> Array("title" => "Каталог товаров",		"class" => "CCatPage",		"file" => "cat.php"),
			"orders"		=> Array("title" => "Корзина",				"class" => "COrdersPage",	"file" => "orders.php"),
			"item"			=> Array("title" => "Товар",				"class" => "CItemPage",		"file" => "item.php", ),


		);

	}

	public function createPage($pageName)
	{
		if (isset($this->m_pages[$pageName]))
		{
			$pageAttrs = $this->m_pages[$pageName];

			if (! isset($pageAttrs["class"]))
				$pageAttrs["class"] = "CRegularPage";

			return $this->createPageInstance($pageName, $pageAttrs, "include/public/");
		}

		return null;
	}

	

}
                        
$g_app = new CPublicApp();

$g_p = get_param("p", "cat");

$g_page = $g_app->createPage($g_p);

if ($g_page == null)
{
	echo "Page not found";
	exit;
}

$g_page->init();
$g_page->action();






require_once($appRoot.'build/top.php5');

$g_page->parse();

require_once($appRoot.'build/bottom.php5');
require_once($appRoot.'build/basecloseconnect.php5');
//$end_start = microtime(1);
//$time = $time_end - $time_start;
//echo "Генерация страницы: $time секунд\n";

?>