<?php
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
@ini_set("display_errors", "1"); 
// tao file emaillist.txt
define('IN_vnT', 1);
require_once ("../../../_config.php");
include ("../../../includes/class_db.php");
$DB = new DB();
//Functions
include ($conf['rootpath'] . 'includes/class_functions.php');
include($conf['rootpath'] . 'includes/admin.class.php');
$func  = new Func_Admin;
  
$conf = $func->fetchDbConfig($conf);
$filename = $conf['rootpath']."vnt_upload/emaillist.csv";

$csv = "Name,E-mail Address\r\n";

$cat_id = (int) $_GET['cat_id'];
if ($cat_id) {
  $where = " WHERE cat_id= $cat_id";
}
 
$query = $DB->query("SELECT id,name,email FROM listmail $where ORDER BY id DESC ");
$stt = 0;
$arr_item = array();
while ($datarow = $DB->fetch_row($query)) {
	$stt ++;
	//print_r($datarow);
	$arr_item = array();
	$arr_item['name'] = ($datarow['name']) ? $datarow['name'] : "Khach hang ".$datarow['id'];
	$arr_item['email'] = $datarow['email'] ;
	if($arr_item['email']) {
		$csv .= join(",", $arr_item)."\r\n";
	}
}

header("Content-type: text/x-csv");
header("Content-Length: " . strlen($csv));
header("Content-Disposition: attachment; filename=emaillist_".date("d-m-Y").".csv"); 
echo $csv;
exit();
?>