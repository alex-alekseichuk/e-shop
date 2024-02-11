<?

class CLoginBlock extends CHtmlBlock
{
	public function action()
	{
		global $g_page;
		global $g_db;

		$cmd = get_param($this->m_name . "Cmd", "");
		if ($cmd == "login")
		{
			$login = get_param("login", "");
			$passwd = get_param("passwd", "");

			$row = $g_db->queryRow("SELECT userId, passwd FROM b_users WHERE login=" . to_sql($login, ""));
			if ($row)
			{
				if ($passwd == $row["passwd"])
				{
					set_session("_userId", $row["userId"]);
					redirect("?p=orders");
				}
			}

			$this->addMessage("Некорректные логин/пароль.");
		}
		
	}

}

class CLoginPage extends CPage
{
	public function __construct($pageName, $pageAttrs)
	{
		parent::__construct("html/public/page.html", $pageAttrs);

		$content = new CLoginBlock("content", "html/public/login.html");
		$this->add($content);
	}

}

?>
