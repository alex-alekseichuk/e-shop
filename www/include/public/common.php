<?


class CCatsList extends CHtmlBlock
{

	function __construct($name)
	{
		parent::__construct($name, null);
	}

	protected function onParse()
	{
		global $g_db;
		global $g_templ;

		$rsCat1 = $g_db->queryAll("SELECT c.catId, c.title, count(i.itemId) AS n FROM b_cats AS c LEFT JOIN b_items AS i ON c.catId=i.catId WHERE c.parentId=0 GROUP BY c.catId ORDER BY c.title");
		$rsCat2 = $g_db->queryAll("SELECT c1.catId AS catId1, c2.catId AS catId2, c2.title AS title, count(i.itemId) AS n FROM (b_cats AS c1 JOIN b_cats AS c2 ON (c1.parentId=0 AND c2.parentId=c1.catId)) LEFT JOIN b_items AS i ON c2.catId=i.catId WHERE c1.parentId=0 GROUP BY c2.catId ORDER BY c1.title, c2.title");


		foreach ($rsCat1 as $row1)
		{
			$bCat1 =& $this->getBlock("cat1");
			$bCat2 =& $g_templ->getBlock($bCat1, "cat2");
			$g_templ->setBlockText($bCat1, "cat2", "");
			foreach ($rsCat2 as $row2)
			{
				if ($row2["catId1"] == $row1["catId"])
				{
					$this->setVar("catId", $row2["catId2"]);
					$this->setVar("title", $row2["title"]);
					if ($row2["n"] > 0)
					{
						$this->setVar("n", $row2["n"]);
						$g_templ->parse($bCat2, "bn");
						$g_templ->setBlockText($bCat2, "bs", "");
					} else {
						$g_templ->parse($bCat2, "bs");
						$g_templ->setBlockText($bCat2, "bn", "");
					}
					$g_templ->parse($bCat1, "cat2", true);
				}
			}

			$this->setVar("catId", $row1["catId"]);
			$this->setVar("title", $row1["title"]);
			if ($row1["n"] > 0)
			{
				$this->setVar("n", $row1["n"]);
				$g_templ->parse($bCat1, "bn");
				$g_templ->setBlockText($bCat1, "bs", "");
			} else {
				$g_templ->parse($bCat1, "bs");
				$g_templ->setBlockText($bCat1, "bn", "");
			}
			$this->parseBlock("cat1", true);
		}


		parent::onParse();
	}

}


?>