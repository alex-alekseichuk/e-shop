<?


class CPathBlock extends CHtmlBlock
{
	protected function onParse()
	{
		global $g_db;
		global $g_templ;

		$catId = get_param("catId", 0);
		$itemId = get_param("itemId", 0);
		if ($itemId > 0)
			$catId = $g_db->DLookUp("SELECT catId FROM b_items WHERE itemId=" . to_sql($itemId, "Number"));
		

		$cats = Array();
		$id = $catId;
		while ($id != 0)
		{
			$row = $g_db->queryRow("SELECT b.parentId, b.title, COUNT(i.itemId) AS n FROM b_cats AS b LEFT JOIN b_items AS i ON b.catId=i.catId WHERE b.catId=" . to_sql($id, "Number") . " GROUP BY b.catId");
			$cats[] = Array(
				"catId" => $id,
				"title" => $row["title"],
				"n" => $row["n"]
			);
			$id = $row["parentId"];
		}
		$cats[] = Array(
			"catId" => 0,
			"title" => "Каталог",
			"n" => 0
		);

		$bLink =& $this->getBlock("link");
		$bCurrent =& $this->getBlock("current");

		for ($i = sizeof($cats) - 1; $i > 0; $i--)
		{
			$this->setVar("catId", $cats[$i]["catId"]);
			$this->setVar("title", $cats[$i]["title"]);
			if ($cats[$i]["n"] > 0)
			{
				$this->setVar("n", $cats[$i]["n"]);
				$g_templ->parse($bLink, "bn");
				$g_templ->setBlockText($bLink, "bs", "");
			} else {
				$g_templ->parse($bLink, "bs");
				$g_templ->setBlockText($bLink, "bn", "");
			}
			$this->parseBlock("link", true);
		}

		$this->setVar("catId", $cats[0]["catId"]);
		$this->setVar("title", $cats[0]["title"]);
		if ($cats[0]["n"] > 0)
		{
			$this->setVar("n", $cats[0]["n"]);
			$g_templ->parse($bCurrent, "bn");
			$g_templ->setBlockText($bCurrent, "bs", "");
		} else {
			$g_templ->parse($bCurrent, "bs");
			$g_templ->setBlockText($bCurrent, "bn", "");
		}
		$this->parseBlock("current");

		parent::onParse();
	}

}

?>