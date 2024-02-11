<?

$g_storage = new CStorage();

class CStorage
{
//	protected $m_db = null;
//	public function getDB() {return $this->m_db;}

	public function __construct()
	{
		global $g_db;
		$g_db = new CDBMysql();
		$g_db->connect();
	}

	public function deletePic($picId)
	{
		global $g_db;
		$fName = $g_db->DLookUp("SELECT name FROM b_pics WHERE picId=" . to_sql($picId, "Number"));
		@unlink(PICS_DIR . "/" . $fName);
		$g_db->execute("DELETE FROM b_pics WHERE picId=" . to_sql($picId, "Number"));
	}
	public function deleteItem($itemId)
	{
		global $g_db;
		$pics = $g_db->queryAll("SELECT name FROM b_pics WHERE itemId=" . to_sql($itemId, "Number"));
		foreach ($pics as $row)
			@unlink(PICS_DIR . "/" . $row["name"]);
		$g_db->execute("DELETE FROM b_pics WHERE itemId=" . to_sql($itemId, "Number"));
		$g_db->execute("DELETE FROM b_items WHERE itemId=" . to_sql($itemId, "Number"));
	}


	public function deleteCat($catId)
	{
		global $g_db;


		$cats = $g_db->queryAll("SELECT catId FROM b_cats WHERE parentId=" . to_sql($catId, "Number"));
		foreach ($cats as $row)
			$this->deleteCat($row["catId"]);

		$items = $g_db->queryAll("SELECT itemId FROM b_items WHERE catId=" . to_sql($catId, "Number"));
		foreach ($items as $row)
			$this->deleteItem($row["itemId"]);

		$g_db->execute("DELETE FROM b_cats WHERE catId=" . to_sql($catId, "Number"));
	}

}



?>