<?


class COrdersList extends CSimpleMysqlGrid
{
	protected $m_catId = 0;

	function __construct($name)
	{
		parent::__construct($name, null);
		$this->m_fields = Array(
			"orderId" => Array("orderId", null),
			"itemName" => Array("itemName", null),
			"login" => Array("login", null),
			"userName" => Array("userName", null),
			"address" => Array("address", null),
			"phone" => Array("phone", null),
			"email" => Array("email", null),
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
		$this->m_sort = "dOrdered";
		$this->m_dir = "desc";

		$this->m_itemBlocks["bDone"] = 0;

		$sFrom = "((b_orders AS o JOIN ad_users AS u ON o.userId=u.id) JOIN ad_contacts AS c ON c.id=u.contact_id) JOIN b_items AS i ON i.itemId=o.itemId";
		$this->m_sqlcount = "SELECT COUNT(*) FROM " . $sFrom;
		$this->m_sql = "SELECT o.orderId, u.login, c.name AS userName, i.title AS itemName, i.itemId, u.id AS userId, i.price, o.n, i.price*o.n AS amount, o.status, o.dOrdered, o.dDone," .
			" c.address, c.phone, c.email" .
			" FROM " . $sFrom;
	}

	public function init()
	{
		parent::init();


		if ($this->m_sMessage ==  "done")
		{
			$this->m_sMessage = "Заказ выполнен.";
		}
	}



	protected function onItem()
	{
		if ($this->m_fields["status"][2] == "O")
		{
			$this->m_fields["dDone"][2] = "";
			$this->m_itemBlocks["bDone"] = 1;
		} else {
			$this->m_itemBlocks["bDone"] = 0;
		}
	}	

	public function action()
	{
		global $g_page;
		global $g_db;

		parent::action();

		if ($this->m_sMessage != "")
			return;

		if (get_param("ordersCmd", "") ==  "done")
		{
			$orderId = get_param("orderId", 0);
			if ($orderId > 0)
			{
				$g_db->execute("UPDATE b_orders SET dDone=now(), status='D' WHERE orderId=" . to_sql($orderId, "Number"));
				redirect("?" . $g_page->getParamsCorrectParam($this->m_name . "Mes", "done"));
			}

		}
	}

}


class COrdersPage extends CLoggedPage
{
	public function __construct($pageName, $pageAttrs)
	{
		parent::__construct("html/admin/page.html", $pageAttrs);

		//$this->add(new COrdersList("content"));


		$content = new CHtmlBlock("content", "html/admin/orders.html");
		$this->add($content);

		$content->add(new COrdersList("orders"));

	}

}

?>
