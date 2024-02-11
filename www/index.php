<?
global $appRoot;
$appRoot = $_SERVER['DOCUMENT_ROOT'] . '/';

require_once($appRoot.'build/baseconnect.php5');
require_once($appRoot.'build/top.php5');
require_once($appRoot . 'build/dbfunc.inc');
require_once($appRoot . 'build/block.inc');



showAllSections(getAllSections($CUR_HOST['id']));

require($appRoot.'build/bottom.php5');
require_once($appRoot.'build/basecloseconnect.php5');
?>