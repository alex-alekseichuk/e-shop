<?


class CCatForm extends CForm
{
	protected $m_catId = 0;
	protected $m_cmd2 = "";

	function __construct($name)
	{
		parent::__construct($name, "", 
			Array(
				"title"	=> Array("title" => "Название",	"value" => "", "max" => 250),
			)
		);
	}

	public function init()
	{
		parent::init();

		$this->m_catId = get_param("catId", $this->m_catId);

		$this->m_cmd2 = get_param($this->m_name . "Cmd2", "");

		if ($this->m_catId == 0 && $this->m_cmd2 != "new")
			$this->m_parent->disableBlock("cat");
		if ($this->m_catId == 0 || $this->m_cmd2 == "new")
			$this->m_parent->disableBlock("items");


		if ($this->m_sMessage ==  "updated")
		{
			$this->m_sMessage = "Изменения сохранены.";
		}
		if ($this->m_sMessage ==  "added")
		{
			$this->m_sMessage = "Новая категория добавлена.";
		}
	}
	public function action()
	{
		global $g_page;
		global $g_db;
		global $g_storage;

//echo "yes"; exit;

		parent::action();

		if ($this->m_sMessage != "")
			return;

		if ($this->m_cmd ==  "add")
		{
			$title = $this->m_fields["title"]["value"];
			$g_db->execute("INSERT INTO b_cats (title, parentId) VALUES (" . to_sql($title, "") . "," . to_sql($this->m_catId, "Number") . ")");
			$this->m_catId = $g_db->get_insert_id();
			redirect("?" . $g_page->getParamsCorrectParam2("catId", $this->m_catId, $this->m_name . "Mes", "added"));
		}

		if ($this->m_cmd ==  "update" && $this->m_catId > 0)
		{
			$title = $this->m_fields["title"]["value"];
			$g_db->execute("UPDATE b_cats SET title=" . to_sql($title, "") . " WHERE catId=" . to_sql($this->m_catId, "Number"));
			redirect("?" . $g_page->getParamsCorrectParam($this->m_name . "Mes", "updated"));
		}

		if ($this->m_cmd ==  "delete" && $this->m_catId > 0)
		{
			$parentId = $g_db->DLookUp("SELECT parentId FROM b_cats WHERE catId=" . to_sql($this->m_catId, "Number"));
			$g_storage->deleteCat($this->m_catId);
			redirect("?" . $g_page->getParamsCorrectParam2("catId", $parentId, "catsMes", "deleted"));
		}
	}

	protected function onParse()
	{
		global $g_db;

		$this->setVar("catIdN", $this->m_cmd2 == "new" ? "(новая)" : "#" . $this->m_catId);

		$this->setVar("catId", $this->m_catId);
		$this->setVar("cmd2", $this->m_cmd2);

		if ($this->m_catId > 0 && $this->m_cmd2 != "new")
		{
			$this->m_fields["title"]["value"] = $g_db->DLookUp("SELECT title FROM b_cats WHERE catId=" . to_sql($this->m_catId, "Number"));
		}

		if ($this->m_cmd2 == "new")
		{
			$this->setVar("cmd", "add");
			$this->setVar("catId", $this->m_catId);
		} else {
			$this->setVar("cmd", "update");
			$this->parseBlock("bDel");
		}

		parent::onParse();
	}

}



class CCatsList extends CSimpleMysqlGrid
{
	protected $m_catId = 0;

	function __construct($name)
	{
		parent::__construct($name, null);
		$this->m_fields = Array(
			"catId" => Array("catId", null),
			"title" => Array("title", null),
		);
		$this->m_nPerPage = 0;
		$this->m_sort = "title";

		$this->m_sqlcount = "SELECT COUNT(*) FROM b_cats";
		$this->m_sql = "SELECT catId, title FROM b_cats";
	}

	public function init()
	{
		parent::init();

		$this->m_catId = get_param("catId", $this->m_catId);
		$sWhere = " WHERE parentId=" . to_sql($this->m_catId, "Number");
		$this->m_sqlcount .= $sWhere;
		$this->m_sql .= $sWhere;


		if ($this->m_sMessage ==  "deleted")
		{
			$this->m_sMessage = "Категория удалена.";
		}
	}

//	protected function getParamsArray(&$a)
//	{
//		$a["catId"] = $this->m_catId;
//		parent::getParamsArray($a);
//	}


	protected function onParse()
	{
		$this->setVar("parentId", $this->m_catId);

		parent::onParse();
	}

}


class CItemsList extends CSimpleMysqlGrid
{
	protected $m_catId = 0;

	function __construct($name)
	{
		parent::__construct($name, null);
		$this->m_fields = Array(
			"itemId" => Array("itemId", null),
			"title" => Array("title", null),
			"price" => Array("price", null),
		);
		$this->m_nPerPage = 0;
		$this->m_sort = "title";

		$this->m_sqlcount = "SELECT COUNT(*) FROM b_items";
		$this->m_sql = "SELECT itemId, title, price FROM b_items";
	}

	public function init()
	{
		parent::init();

		$this->m_catId = get_param("catId", $this->m_catId);
		$sWhere = " WHERE catId=" . to_sql($this->m_catId, "Number");
		$this->m_sqlcount .= $sWhere;
		$this->m_sql .= $sWhere;

		if ($this->m_sMessage ==  "deleted")
		{
			$this->m_sMessage = "Товар удален.";
		}
	}

//	protected function getParamsArray(&$a)
//	{
//		$a["catId"] = $this->m_catId;
//		parent::getParamsArray($a);
//	}

}


class CCatPage extends CLoggedPage
{
	protected $m_catId = 0;

	public function __construct($pageName, $pageAttrs)
	{
		parent::__construct("html/admin/page.html", $pageAttrs);


		$content = new CHtmlBlock("content", "html/admin/cat.html");
		$this->add($content);

		$content->add(new CPathBlock("path"));
		$content->add(new CCatsList("cats"));
		$content->add(new CCatForm("cat"));
		$content->add(new CItemsList("items"));

	}

	public function init()
	{
		parent::init();

		$this->m_catId = get_param("catId", $this->m_catId);
	}

	protected function getParamsArray(&$a)
	{
		$a["catId"] = $this->m_catId;
		parent::getParamsArray($a);
	}

}


?>