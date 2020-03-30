<?php

	define('IN_vnT', 1);
	require_once("../../_config.php"); 
	require_once("../../includes/class_db.php"); 
	$DB = new DB;
	include ('../../includes/class_functions.php');
	include('../../includes/admin.class.php');
	$func  = new Func_Admin;
	$conf = $func->fetchDbConfig($conf);
	$lang= $conf['langcp'];
	
	$str_title = "title_".$lang;
	$str_desc = "description_".$lang;

?>

<table width="100%"  border="0" cellspacing="0" cellpadding="0" align=center>

<?php

if (isset($_GET['keyword'])) $keyword=$_GET['keyword']; else $keyword="";

$query = $DB->query("SELECT * FROM admin_menu WHERE 
	$str_title LIKE '%{$keyword}%' OR 
	$str_desc LIKE '%{$keyword}%'
	");

$have=0;

while ($page=$DB->fetch_row($query)) {

	$have=1;

	?><tr><td width="100%" bgcolor="#FFFFFF" align="left" style="padding:1px;" onmouseover="this.bgColor='#E6F4FF'" onmouseout="this.bgColor='#FFFFFF'">&nbsp;&rsaquo; <a onclick="load_content('<?=$page['id']?>');" href="javascript:;" style="color:#000000"><?=$page[$str_title]?></a></td></tr>
<?php

} 

if (!$have) {

	?><tr><td width="100%" align="center"><br><br><font color="#FF0000"><b>No Page Found !</b></font><br><br><br></td></tr>
<?php

}

?>

</table>