<?php if ($_GET['print']==1) { ?>
<style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}
body {
	background-color: #FFFFFF;
	margin-left: 3px;
	margin-top: 3px;
	margin-right: 3px;
	margin-bottom: 3px;
}
a {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #0066FF;
}
a:link {
	text-decoration: none;
	color: #003399;
}
a:visited {
	text-decoration: none;
	color: #003399;
}

a:hover {
	text-decoration: none;
	color: #0066FF;
}

a:active {
	text-decoration: none;
	color: #003399;
}

.pagetitle{
	color:#003399;
	font-size:18px;
	font-weight:bold;
}
-->

</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div align=left style="background-color:#E6E6E6; color:#000000; font-weight:bold; padding:3px;">Administrator Help</div>

<?php

} 

	define('IN_vnT', 1);
	require_once("../../_config.php"); 
	require_once("../../includes/class_db.php"); 
	$vnT->DB = $DB = new DB;
	include ('../../includes/class_functions.php');
	include('../../includes/admin.class.php');
	$vnT->func = $func  = new Func_Admin;
	$vnT->conf = $conf = $func->fetchDbConfig($conf);
	$lang= $conf['langcp'];
	
 	$id = (int)$_GET['id'];
	$str_title = "title_".$lang;
	$arr_module_default = array ("advertise","admin","backup","config","contact","country","database","lang","layout","mail_list","mail_temp","media","menu","page","plugins","poll","sitedoc","statistics","support");
	$notFound=0;
	
$query = $DB->query("SELECT * FROM admin_menu WHERE id=$id  ");
if ($page=$DB->fetch_row($query)) {
	?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="25"><div class="pagetitle"><?php echo $page[$str_title]; ?></div></td>
  </tr>
  <tr>
    <td height="2" bgcolor="#FF0000"></td>
  </tr>
  <tr>
    <td >
	<?php
		if(in_array($page['module'],$arr_module_default))
		{
			$file_name = "http://thietkeweb.com/admin/help/".$page['module']."/".$page['act'].".html";
			$content = file_get_contents($file_name);
			if($content) {
				echo $content ;
			}else{
				$notFound = 1 ;
			}
			
		}else{
			$file_name = "../modules/".$page['module']."_ad/help/".$page['act'].".html";
			if(file_exists($file_name)){
				include ($file_name);
			}else{
				$notFound = 1 ;
			}
		}
		
		if($notFound)
		{
			echo '<br><br><br><div align=center>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="#FF0000"><b>Watting Update ... !</b></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><br><br><br>';
		}
	?>
	</td>
  </tr>
</table>

	<?php
} else {

	?><br><br><br><div align=center>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="#FF0000"><b>Page Not Found !</b></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><br><br><br><?php

}



if ($_GET['print']==1) { ?>
<div align=center style="background-color:#E6E6E6; color:#000000; font-weight:bold; padding:3px;">Powered by vnTRUST CMS . All Rights Reserved.</div>
<script language="javascript">window.print();</script>

<? } ?>