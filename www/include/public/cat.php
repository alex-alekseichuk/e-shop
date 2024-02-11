<?
include_once("common.php");




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
			"notes" => Array("notes", null),
		);
		$this->m_nPerPage = 0;
		$this->m_sort = "itemId";

		$this->m_sqlcount = "SELECT COUNT(*) FROM b_items";
		$this->m_sql = "SELECT itemId, title, price, notes FROM b_items";
	}

	public function init()
	{
		parent::init();

		$this->m_catId = get_param("catId", $this->m_catId);
		$sWhere = " WHERE catId=" . to_sql($this->m_catId, "Number");
		$this->m_sqlcount .= $sWhere;
		$this->m_sql .= $sWhere;

		if ($this->m_sMessage ==  "added")
		{
			$this->m_sMessage = "Заказ добавлен в вашу корзину.";
		}
	}


	protected function onItem()
	{
		global $g_templ;
		global $g_db;
		$g_templ->setBlockText($this->m_blItem, "pic", "");
		$pics = $g_db->queryAll("SELECT name FROM b_pics WHERE itemId=" . to_sql($this->m_fields["itemId"][2], "number"));
		foreach($pics as $row)
		{
			$this->setVar("src", PICS_DIR . "/" . $row["name"]);
			$g_templ->parse($this->m_blItem, "pic", true);
		}
	}	

}


class CCatPage extends CPage
{
	protected $m_catId = 0;

	public function __construct($pageName, $pageAttrs)
	{
		parent::__construct("html/public/page.html", $pageAttrs);


		$content = new CHtmlBlock("content", "html/public/cat.html");
		$this->add($content);

		$content->add(new CPathBlock("path"));
		$content->add(new CCatsList("cats"));
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