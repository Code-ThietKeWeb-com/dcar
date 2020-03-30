<?php
	define('IN_vnT', 1);
	require_once("../../_config.php"); 
	require_once("../../includes/class_db.php"); 
	$vnT->DB= $DB = new DB;
	//Functions
	include ('../../includes/class_functions.php');
	include('../../includes/admin.class.php');
	$vnT->func = $func  = new Func_Admin;
	$conf = $func->fetchDbConfig($conf);
 
	$lang= $conf['langcp'];
	$id = 2;
	if($_GET['mod'] && $_GET['act']){
		$result = $DB->query("SELECT * FROM admin_menu WHERE module='".$_GET['mod']."' and act='".$_GET['act']."' and (sub='' or sub IS NULL) ");
		if($row = $DB->fetch_row($result))
		{
			$id = (int)$row['id'];
		}
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Huong Dan Su Dung AdminCP</title>
<script language="javascript" src="NDK.js"></script>
<LINK href="help_style.css" rel=stylesheet type=text/css>
</head>

<body onLoad="MM_preloadImages('images/but_cong.gif','images/but_tru.gif','images/undo.gif','images/undo1.gif','images/redo.gif','images/redo1.gif','images/wait.gif')">

<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#2670B7">

  <tr>

    <td colspan="2" bgcolor="#A1D1FE"><table width="100%" border="0" cellspacing="1" cellpadding="1">

      <tr>

        <td width="32" align="center"><a herf="javascript:;" onclick="show_home('<?=$id?>');" style="cursor:pointer"><img src="images/home.gif" width="32" height="32" alt="Home"/></a></td>

        <td width="40" align="center"><a herf="javascript:;" onclick="do_back();" style="cursor:pointer"><img src="images/undo.gif" width="32" height="32" alt="Back" name="img_back" id="img_back"/></a></td>

        <td width="40" align="center"><a herf="javascript:;" onclick="do_next();" style="cursor:pointer"><img src="images/redo.gif" width="32" height="32" alt="Next" name="img_next" id="img_next"/></a></td>

        <td width="40" align="center"><a herf="javascript:;" onclick="show_search();" style="cursor:pointer"><img src="images/search.gif" width="32" height="32" alt="Search"/></a></td>

        <td align="center" class="web_title">Administrator Help</td>

        <td width="40"><a herf="javascript:;" onclick="do_print();" style="cursor:pointer"><img src="images/print.gif" width="32" height="32" alt="Print"/></a></td>

      </tr>

    </table></td>

  </tr>

  <tr>

    <td  width="20%" align="left" valign="top" bgcolor="#E1F0FF"><table width="100%" border="0" cellspacing="1" cellpadding="1">

      <tr>

        <td class="main_menu"><a herf="javascript:;" onclick="show_search();" style="cursor:pointer"><b>&raquo;&nbsp;Search</b></a><br />

        <span id="f_search" style="display:none">

        <form id="form_search" name="form_search" method="post" action="" onsubmit="return do_search(this);">

          <input name="keyword" type="text" id="keyword" size="18" maxlength="250" />

          <input type="submit" name="Submit" value="Search" />

        </form><br />

        <table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#CCCCCC">

<tr><td width="100%" bgcolor="#0061B5" style="padding:2px;"><b style="line-height:20px;"><font color="#FFFFFF">Search Result</font></b><br>

        <span id="search_result" style="background-color:#FFFFFF"><table width="100%"  border="0" cellspacing="0" cellpadding="0" align=center><tr><td width="100%" bgcolor="#FFFFFF" align="left" style="padding:1px;"><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></span>

        </td></tr>

        </table>

        </span>

        </td>

      </tr>

      <tr>

        <td><a herf="javascript:;" onclick="show_menu_content();" style="cursor:pointer"><b>&raquo;&nbsp;Contents</b></a><br />
        <span id="menu_content"><?php include("menu_content.php"); ?></span>

        </td>

      </tr>

    </table></td>

    <td align="left" valign="top" bgcolor="#FFFFFF" style="padding:10px" width="80%"><span id="content"></span>    </td>

  </tr>

  <tr>

    <td colspan="2" bgcolor="#A1D1FE" align="center">&copy; 2006 <a href="http://www.vntrust.com" target="_blank">TRUST.vn . </a> All Rights Reserved.</td>

  </tr>

</table>

<script language="javascript">show_home('<?php echo $id; ?>');</script>

</body>

</html>