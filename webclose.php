<?php
	define('IN_vnT',1);
	require_once("_config.php"); 
	require_once("includes/class_db.php"); 
	$DB = new DB;
	require_once("includes/class_functions.php"); 	
	$func = new Func_Global;
	$conf=$func->fetchDbConfig($conf);

if(!empty($conf['web_redirect']))
	{
		echo "<meta http-equiv='refresh' content='0; url=".$conf['web_redirect']."' />";
?>

<?php		

	}
	else if (!empty($conf['web_iframe']))
	{

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $conf['indextitle']; ?></title>
</head>
<SCRIPT language=javascript>setInterval("x()",5);self.focus();function x(){window.status="<?php echo $conf['indextitle']; ?>"}</SCRIPT>

<frameset rows="0%,*">
  <frame name="top" scrolling="auto" src="test.html" FRAMEBORDER="NO" MARGINWIDTH=0 NORESIZE target="leftframe" >
  <frame name="bottom" target="leftframe" src="<?php echo $conf['web_iframe']; ?>" FRAMEBORDER="NO" MARGINWIDTH=0 NORESIZE>
<noframes>
<body>
</body>
</noframes>
</html>
<?php	
	}
	else
	{
?>
<html >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Protected</title>
<style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}
body {
	background-color: #CCCCCC;
	margin-left: 2px;
	margin-top: 2px;
	margin-right: 2px;
	margin-bottom: 2px;
}
a:link {
	color: #FFFF11;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #FFFF11;
}
a:hover {
	text-decoration: none;
	color: #0066FF;
}
a:active {
	text-decoration: none;
	color: #FFFF11;
}
.style1 {
	font-size: 14px;
	font-weight: bold;
	color: #FFFFFF;
}
.style2 {color: #FFFFFF}
-->
</style>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
  <tr>
    <td>
    <table width="80%" border="0" cellpadding="1" cellspacing="1" bgcolor="#333333" align="center">
  <tr>
    <td height="25" bgcolor="#113777" align="center"><span class="style1">This website was closed for maintenance.</span></td>
  </tr>
  <tr>
    <td align="center" bgcolor="#FFFFFF" style="padding:20px;"><?php echo $func->HTML($conf['web_close_desc']); ?></td>
  </tr>
  <tr>
    <td height="20" align="center" bgcolor="#113777"><span class="style2">Copyright &copy; 2007 <?php echo $_SERVER['HTTP_HOST']; ?> . Made by <a href="http://vntrust.com" target="_blank">TRUST.vn</a> </span></td>
  </tr>
</table>
    </td>
  </tr>
</table>
</body>
</html>
<?php		
	}
?>
