<?php
	define('IN_vnT',1);
	session_start();
	require_once("_config.php"); 
	require_once("includes/class_db.php"); 
	$DB = new DB;
	
	require_once("includes/class_functions.php"); 	
	$func = new Func_Global;
	$conf=$func->fetchDbConfig($conf);
	
	$email = $_GET['email'];
	
	$res_ck = $DB->query("select id from listmail where email='$email'");
	if($DB->num_rows($res_ck))
	{
		$DB->query("DELETE from listmail where email='$email' ");
		$err = "Delete email successfull !!!";
	}else{
		$err = "Email not found !!!";
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title><?php echo $err; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv='refresh' content='1; url=<?php echo $conf['rooturl']; ?>' />
<link href="skins/default/style/screen.css" rel="stylesheet" type="text/css">
</head>	
<body>
<div style="overflow:hidden; margin:200px auto;" >
  <div id="box_redirect">
  	<div class="top"><img src="skins/default/images/thongbao.gif" width="32" height="22" align="absmiddle" />&nbsp;Thông báo</div>
    <div class="middle" >
    	<p class="fontMess" ><?php echo $err; ?></p>
        <p style="text-align:center"><img src="skins/default/images/loading.gif" width="78" height="7" /></p>
        <p class="font_err" style="text-align:center"><a href="<?php echo $conf['rooturl']; ?>">Vui lòng đợi hoặc click vào đây</a></p>
    </div>
    <div class="bottom">.::[ Copyright &copy; 2008 <?php echo $_SERVER['HTTP_HOST']; ?> ]::.</div>
  </div>
</div>
</body>
</html>