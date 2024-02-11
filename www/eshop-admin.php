<?

include_once("include/core.php");
include_once("include/lang.php");
include_once("include/storage.php");


class CLoggedPage extends CPage
{
	public function init()
	{
		if (get_session("_admin") != "admin")
		{
			redirect("?p=login");
		}

		parent::init();
	}
}


class CAdminApp extends CWebApp
{
	public function __construct()
	{
		$this->m_pages = Array (
			"orders"	=> Array("title" => "Заказы",				"class" => "COrdersPage",	"file" => "orders.php", ),

			"cats"		=> Array("title" => "Каталог товаров",		"class" => "CCatsPage",		"file" => "cats.php", ),
			"cat"		=> Array("title" => "Категория",			"class" => "CCatPage",		"file" => "cat.php", ),
			"item"		=> Array("title" => "Товар",				"class" => "CItemPage",		"file" => "item.php", ),

			"login"		=> Array("title" => "Вход Администратора",	"class" => "CLoginPage",	"file" => "login.php", )

		);

	}



	public function createPage($pageName)
	{
		if (isset($this->m_pages[$pageName]))
		{
			$pageAttrs = $this->m_pages[$pageName];

			if (! isset($pageAttrs["class"]))
				$pageAttrs["class"] = "CRegularPage";

			return $this->createPageInstance($pageName, $pageAttrs, "include/admin/");
		}

		return null;
	}


}
                        
$g_app = new CAdminApp();

$g_p = get_param("p", "orders");

$g_page = $g_app->createPage($g_p);

if ($g_page == null)
{
	echo "Page not found";
	exit;
}

$g_page->init();
$g_page->action();
$g_page->parse();


?>