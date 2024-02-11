<?

class CLoginBlock extends CHtmlBlock
{
	public function action()
	{
		global $g_page;

		$cmd = get_param($this->m_name . "Cmd", "");
		if ($cmd == "login")
		{
			$passwd = get_param("passwd", "");
			if ($passwd == ADMIN_PASSWD)
			{
				set_session("_admin", "admin");
				redirect("?p=orders");
			} else {
				$this->addMessage("Incorrect password.");
			}
		}
		
	}

}

class CLoginPage extends CPage
{
	public function __construct($pageName, $pageAttrs)
	{
		parent::__construct("html/admin/page.html", $pageAttrs);

		$content = new CLoginBlock("content", "html/admin/login.html");
		$this->add($content);
	}

}

?>
