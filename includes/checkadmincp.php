<?php 
$go_login = 1;
$act = (empty($_GET['act'])) ? "main" : $_GET['act'];
$nocheck = array(  "login" ,   "lostpass" ,   "logout" );
if (! in_array($_GET['act'], $nocheck)) 
{
	$arr_auth = explode("|",$_COOKIE[AUTH_COOKIE]);

	if($arr_auth[2] == $_SESSION['ses_admin']){
    $res_ck = $DB->query("SELECT * FROM admin WHERE username='".base64_decode($arr_auth[0])."' ");
    if($row_ck = $DB->fetch_row($res_ck))
    {
      $auth_hash = md5($row_ck['adminid'] . '|' . $row_ck['password']);
      if($arr_auth[1] ==$auth_hash )
      {
        $go_login=0;
        $vnT->admininfo = $func->get_admininfo($row_ck['adminid']);
      }
    }

  }

	
	if($go_login)
	{
		if ($_SERVER['QUERY_STRING']) {
      $ref = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      $ref = "&ref=" . $func->NDK_encode($ref);
    }
		
		flush();
    @header("Location: ?act=login" . $ref);
    echo "<meta http-equiv='refresh' content='0; url=?act=login" . $ref . "' />";
    exit();
	}   
}

require_once PATH_ADMIN . DS . 'adminlog.php';
?>