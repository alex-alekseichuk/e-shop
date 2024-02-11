<?



class CItemForm extends CForm
{
	protected $m_catId = 0;
	protected $m_itemId = 0;

	function __construct($name)
	{
		parent::__construct($name, null, 
			Array(
				"title"			=> Array("title" => "Наименование",	"value" => "",	"max" => 250),
				"notes"			=> Array("title" => "Описание",		"value" => "",	"max" => 10240, "tohtml" => 1),
				"price"			=> Array("title" => "Цена",			"value" => "0",	"type" => "float"),
			)
		);
//				"fileName"		=> Array("title" => "Картинка",		"value" => "",	"type" => "file", "optional" => true, "ext" => "jpg|jpeg", "dir" => "items"),
	}

	public function init()
	{
		global $g_db;
		parent::init();

		$this->m_itemId = get_param("itemId", $this->m_itemId);
		if ($this->m_itemId == 0)
			$this->m_catId = get_param("catId", $this->m_catId);
		else {
			$this->m_catId = $g_db->DLookUp("SELECT catId FROM b_items WHERE itemId=" . to_sql($this->m_itemId, "Number"));
			$this->getFromDB();
		}

		if ($this->m_itemId == 0)
			$this->m_parent->disableBlock("pics");

		if ($this->m_catId == 0)
			redirect("?p=cat");

		if ($this->m_sMessage ==  "updated")
		{
			$this->m_sMessage = "Изменения сохранены.";
		}
		if ($this->m_sMessage ==  "added")
		{
			$this->m_sMessage = "Новый товар добавлен.";
		}
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

			$title = $this->m_fields["title"]["value"];
			$notes = $this->m_fields["notes"]["value"];
			$price = $this->m_fields["price"]["value"];
			//$fileName = $this->m_fields["fileName"]["value"];

			if ($this->m_itemId > 0)
			{
				$g_db->execute("UPDATE b_items SET catId=" . to_sql($this->m_catId, "Number") . ", title=" . to_sql($title, "") . ", notes=" . to_sql($notes, "") . ", price=" . to_sql($price, "Number") . " WHERE itemId=" . to_sql($this->m_itemId, "Number"));
				redirect("?" . $g_page->getParamsCorrectParam($this->m_name . "Mes", "updated"));
			} else {
				$g_db->execute("INSERT INTO b_items (catId, title, notes, price) VALUES (" . to_sql($this->m_catId, "Number") . ", " . to_sql($title, "") . ", " . to_sql($notes, "") . ", " . to_sql($price, "Number") . ")");
				$this->m_itemId = $g_db->get_insert_id();
				redirect("?" . $g_page->getParamsCorrectParam($this->m_name . "Mes", "added"));
			}
		}
		if ($this->m_cmd ==  "delete" && $this->m_itemId > 0)
		{
            $g_storage->deleteItem($this->m_itemId);
			redirect("?" . $g_page->getParamsCorrectParam2("p", "cat", "itemsMes", "deleted"));
		}
	}
	protected function getParamsArray(&$a)
	{
		$a["catId"] = $this->m_catId;
		$a["itemId"] = $this->m_itemId;
		parent::getParamsArray($a);
	}

	protected function getFromDB()
	{
		global $g_db;

		if ($this->m_itemId > 0)
		{
			$row = $g_db->queryRow("SELECT title,notes,price FROM b_items WHERE itemId=" . to_sql($this->m_itemId, "Number"));
			if ($row)
			{
				$this->m_fields["title"]["value"] = $row["title"];
				$this->m_fields["notes"]["value"] = $row["notes"];
				$this->m_fields["price"]["value"] = $row["price"];
				//$this->m_fields["fileName"]["value"] = $row["fileName"];

				return true;
			}
		}

		return false;
	}

	protected function onParse()
	{
		global $g_db;

		$this->setVar("catId", $this->m_catId);
		$this->setVar("itemId", $this->m_itemId);
		$this->setVar("itemIdN", $this->m_itemId == 0 ? "(новый)" : "#" . $this->m_itemId);

		if ($this->getFromDB())
		{


/*
			if ($this->m_fields["fileName"]["value"])
			{
				$sImg = "items/" . $this->m_fields["fileName"]["value"];
				if (file_exists($sImg))
				{
					$this->setVar("img", $sImg);
					$this->parseBlock("img");
				}
			}
*/


			if ($this->m_itemId > 0)
				$this->parseBlock("bDel");

		}

		parent::onParse();
	}
}


class CPicsForm extends CForm
{
	protected $m_picId = 0;
	protected $m_itemId = 0;

	function __construct($name)
	{
		parent::__construct($name, null, 
			Array(
				"name"		=> Array("title" => "Картинка",		"value" => "",	"type" => "file", "optional" => true, "ext" => "jpg|jpeg", "dir" => PICS_DIR),
			)
		);
	}

	public function init()
	{
		global $g_db;
		parent::init();

		$this->m_itemId = get_param("itemId", $this->m_itemId);
		$this->m_picId = get_param("picId", $this->m_itemId);

/*
		if ($this->m_itemId == 0)
			redirect("?p=cat");
		if ($this->m_itemId == 0)
			$this->m_catId = get_param("catId", $this->m_catId);
		else {
			$this->m_catId = $g_db->DLookUp("SELECT catId FROM b_items WHERE itemId=" . to_sql($this->m_itemId, "Number"));
			$this->getFromDB();
		}
*/

		if ($this->m_sMessage ==  "deleted")
		{
			$this->m_sMessage = "Фото удалено.";
		}
		if ($this->m_sMessage ==  "added")
		{
			$this->m_sMessage = "Новое фото добавлено.";
		}
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

			$name = $this->m_fields["name"]["value"];

			$g_db->execute("INSERT INTO b_pics (itemId, name) VALUES (" . to_sql($this->m_itemId, "Number") . ", " . to_sql($name, "") . ")");
			$this->m_picId = $g_db->get_insert_id();
			redirect("?" . $g_page->getParamsCorrectParam($this->m_name . "Mes", "added"));
		}
		if ($this->m_cmd ==  "delete" && $this->m_picId > 0)
		{
			$g_storage->deletePic($this->m_picId);
			redirect("?" . $g_page->getParamsCorrectParam2("p", "item", $this->m_name . "Mes", "deleted"));
		}
	}

/*
	protected function getFromDB()
	{
		global $g_db;

		if ($this->m_itemId > 0)
		{
			$row = $g_db->queryRow("SELECT name FROM b_pics WHERE picId=" . to_sql($this->m_itemId, "Number"));
			if ($row)
			{
				$this->m_fields["title"]["value"] = $row["title"];
				$this->m_fields["notes"]["value"] = $row["notes"];
				$this->m_fields["price"]["value"] = $row["price"];
				//$this->m_fields["fileName"]["value"] = $row["fileName"];

				return true;
			}
		}

		return false;
	}
*/

	protected function onParse()
	{
		global $g_db;

		$pics = $g_db->queryAll("SELECT name, picId FROM b_pics WHERE itemId=" . to_sql($this->m_itemId, "Number"));
		foreach ($pics as $row)
		{
			$this->setVar("picId", $row["picId"]);
			$this->setVar("name", $row["name"]);
			$this->parseBlock("pic", true);
		}

		parent::onParse();
	}
}


class CItemPage extends CLoggedPage
{
	public function __construct($pageName, $pageAttrs)
	{
		parent::__construct("html/admin/page.html", $pageAttrs);
/*
		$item = new CItemForm("content");
		$this->add($item);

		$item->add(new CPathBlock("path"));
		$item->add(new CPicsForm("pics"));
*/


		$content = new CHtmlBlock("content", "html/admin/item.html");
		$this->add($content);

		$content->add(new CItemForm("item"));
		$content->add(new CPathBlock("path"));
		$content->add(new CPicsForm("pics"));


	}

}


?>