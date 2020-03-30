<?php
	define('IN_vnT', 1);
	define('PATH_ROOT', dirname(__FILE__));
	define('DS', DIRECTORY_SEPARATOR);
	require_once("_config.php"); 
	
	require_once("includes/class_db.php"); 
	$DB = new DB;
	//Functions
	include ( 'includes/class_functions.php');
	include ( 'includes/admin.class.php');
	$func  = new Func_Admin;
  
	$customer_code = ($_GET['code']) ? $_GET['code'] : $_SERVER['HTTP_HOST'];
	$lisence = ($_GET['lisence']) ? $_GET['lisence'] : "2eb0ca794f4cee2a52e739710e585dee";
	// update config
	$arr_old = $func->fetchDbConfig();
	$cot['customer_code'] = $customer_code;
	$cot['lisence'] = $lisence ;
	$ok = $func->writeDbConfig("config",$cot,$arr_old);
	if($ok){
		die("Da cap nhap lisence thanh cong")	;
	}
?>