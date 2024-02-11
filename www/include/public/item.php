<?

include_once("common.php");

class CItemForm extends CForm
{
	protected $m_catId = 0;
	protected $m_itemId = 0;

	function __construct($name)
	{
		parent::__construct($name, null, 
			Array(
				"n"	=> Array("title" => "Кол-во",		"value" => "1",	"type" => "int", "min" => 1, "max" => 100),
			)
		);
	}

	public function init()
	{
		global $g_db;
		parent::init();

		$this->m_userId = $_COOKIE['INF_USER_ID']; //get_session("_userId");

		$this->m_itemId = get_param("itemId", $this->m_itemId);
		if ($this->m_itemId == 0)
			redirect("?p=cat");
		$this->m_catId = $g_db->DLookUp("SELECT catId FROM b_items WHERE itemId=" . to_sql($this->m_itemId, "Number"));
	}
	public function action()
	{
		global $g_page;
		global $g_db;
		global $g_storage;

		parent::action();


		if ($this->m_sMessage != "")
			return;

		if ($this->m_cmd ==  "save")
		{
			$this->onSave();
			$n = $this->m_fields["n"]["value"];

			$g_db->execute("INSERT INTO b_orders (itemId, userId, n, dOrdered) VALUES (" . to_sql($this->m_itemId, "Number") . ", " . to_sql($this->m_userId, "Number") . ", " . to_sql($n, "Number") . ", now())");
			$this->m_orderId = $g_db->get_insert_id();


			$rowItem = $g_db->queryRow("SELECT title,notes,price FROM b_items WHERE itemId=" . to_sql($this->m_itemId, "Number"));
			$rowUser = $g_db->queryRow("SELECT c.name, c.address, c.phone, c.email FROM ad_users AS u JOIN ad_contacts AS c ON u.contact_id=c.id WHERE u.id=" . to_sql($_COOKIE['INF_USER_ID'], "Number"));
			if ($rowUser && $rowItem)
			{
				$message = "На сайте infit.ru клиент сделал новый заказ.<br /><br />\n" .
					"ФИО: " . $rowUser["name"] . "<br />\n" .
					"Адрес: " . $rowUser["address"] . "<br />\n" .
					"Телефон: " . $rowUser["phone"] . "<br />\n" .
					"Email: " . $rowUser["email"] . "<br />\n" .
					"<br />\n" .
					"Наименование: " . $rowItem["title"] . "<br />\n" .
					"Цена: " . $rowItem["price"] . "<br />\n" .
					"Кол-во: " . $n . "<br />\n";
				send_email(ADMIN_EMAIL, ADMIN_EMAIL, "Новый заказ на infit.ru", $message);
			}


			redirect("?" . $g_page->getParamsCorrectParam2("p", "cat", "itemsMes", "added"));

		}
	}
	protected function getParamsArray(&$a)
	{
		$a["catId"] = $this->m_catId;
		$a["itemId"] = $this->m_itemId;
		parent::getParamsArray($a);
	}

	protected function onParse()
	{
		global $g_db;

		if ($this->m_itemId > 0)
		{
			$row = $g_db->queryRow("SELECT title,notes,price FROM b_items WHERE itemId=" . to_sql($this->m_itemId, "Number"));
			if ($row)
			{
				$this->setVar("title", $row["title"]);
				$this->setVar("notes", $row["notes"]);
				$this->setVar("price", $row["price"]);
			}

			$row = $g_db->queryRow("SELECT c.name, c.address, c.phone, c.email FROM ad_users AS u JOIN ad_contacts AS c ON u.contact_id=c.id WHERE u.id=" . to_sql($_COOKIE['INF_USER_ID'], "Number"));
			if ($row)
			{
				$this->setVar("name", $row["name"]);
				$this->setVar("address", $row["address"]);
				$this->setVar("phone", $row["phone"]);
				$this->setVar("email", $row["email"]);
			}

			$rows = $g_db->queryAll("SELECT name FROM b_pics WHERE itemId=" . to_sql($this->m_itemId, "Number"));
			foreach ($rows as $row)
			{

				$this->setVar("src", PICS_DIR . "/" . $row["name"]);
				$this->parseBlock("pic", true);
			}

			if (empty($_COOKIE['INF_USER_LOGIN']))
				$this->parseBlock("notlogged", true);
			else
				$this->parseBlock("logged", true);

		}


		parent::onParse();
	}
}




class CItemPage extends CPage
{
	public function __construct($pageName, $pageAttrs)
	{
		parent::__construct("html/public/page.html", $pageAttrs);

		$content = new CHtmlBlock("content", "html/public/item.html");
		$this->add($content);

		$content->add(new CPathBlock("path"));
		$content->add(new CCatsList("cats"));
		$content->add(new CItemForm("item"));


	}

}


?>