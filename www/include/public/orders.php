<?

include_once("common.php");

class COrdersList extends CSimpleMysqlGrid
{
	protected $m_catId = 0;

	function __construct($name)
	{
		parent::__construct($name, null);

		$this->m_fields = Array(
			"orderId" => Array("orderId", null),
			"itemName" => Array("itemName", null),
			"itemId" => Array("itemId", null),
			"userId" => Array("userId", null),
			"price" => Array("price", null),
			"n" => Array("n", null),
			"amount" => Array("amount", null),
			"status" => Array("status", null),
			"dOrdered" => Array("dOrdered", null),
		);
		$this->m_nPerPage = 0;
		$this->m_sort = "dOrdered";
		$this->m_dir = "desc";

		$this->m_itemBlocks["bDone"] = 0;

		$sFrom = "b_orders AS o JOIN b_items AS i ON i.itemId=o.itemId";
		$this->m_sqlcount = "SELECT COUNT(*) FROM " . $sFrom;
		$this->m_sql = "SELECT o.orderId, i.title AS itemName, i.itemId, i.price, o.n, i.price*o.n AS amount, o.status, o.dOrdered" .
			" FROM " . $sFrom;
	}

	public function init()
	{
		parent::init();

		$this->m_userId = $_COOKIE['INF_USER_ID'];

		$sWhere = " WHERE o.status='O' AND o.userId=" . to_sql($this->m_userId, "Number");
		$this->m_sqlcount .= $sWhere;
		$this->m_sql .= $sWhere;


		if ($this->m_sMessage ==  "cancelled")
		{
			$this->m_sMessage = "Заказ отменен.";
		}
	}



	public function action()
	{
		global $g_page;
		global $g_db;

		parent::action();

		if ($this->m_sMessage != "")
			return;

		if (get_param("ordersCmd", "") ==  "cancel")
		{
			$orderId = get_param("orderId", 0);
			if ($orderId > 0)
			{
				if ($g_db->DLookUp("SELECT userId FROM b_orders WHERE orderId=" . to_sql($orderId, "Number"))
						== $this->m_userId)
	    	    {
					$g_db->execute("DELETE FROM b_orders WHERE orderId=" . to_sql($orderId, "Number"));
					redirect("?" . $g_page->getParamsCorrectParam($this->m_name . "Mes", "cancelled"));
				}
			}

		}
	}

}


class CDoneList extends CSimpleMysqlGrid
{
	protected $m_catId = 0;

	function __construct($name)
	{
		parent::__construct($name, null);
		$this->m_fields = Array(
			"orderId" => Array("orderId", null),
			"itemName" => Array("itemName", null),
			"itemId" => Array("itemId", null),
			"userId" => Array("userId", null),
			"price" => Array("price", null),
			"n" => Array("n", null),
			"amount" => Array("amount", null),
			"status" => Array("status", null),
			"dOrdered" => Array("dOrdered", null),
			"dDone" => Array("dDone", null),
		);
		$this->m_nPerPage = 0;
		$this->m_sort = "dDone";
		$this->m_dir = "desc";

		$this->m_itemBlocks["bDone"] = 0;

		$sFrom = "b_orders AS o JOIN b_items AS i ON i.itemId=o.itemId";
		$this->m_sqlcount = "SELECT COUNT(*) FROM " . $sFrom;
		$this->m_sql = "SELECT o.orderId, i.title AS itemName, i.itemId, i.price, o.n, i.price*o.n AS amount, o.status, o.dOrdered, o.dDone" .
			" FROM " . $sFrom;
	}

	public function init()
	{
		parent::init();

		$this->m_userId = $_COOKIE['INF_USER_ID'];

		$sWhere = " WHERE o.status='D' AND o.userId=" . to_sql($this->m_userId, "Number");
		$this->m_sqlcount .= $sWhere;
		$this->m_sql .= $sWhere;

	}



}


class COrdersPage extends CLoggedPage
{
	public function __construct($pageName, $pageAttrs)
	{
		parent::__construct("html/public/page.html", $pageAttrs);

		$content = new CHtmlBlock("content", "html/public/orders.html");
		$this->add($content);

		$content->add(new COrdersList("orders"));
		$content->add(new CCatsList("cats"));
		$content->add(new CDoneList("done"));

	}

}


?>