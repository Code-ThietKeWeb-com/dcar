<?php

function writableCell( $folder ) {
	echo '<tr>';
	echo '<td class="item">' . $folder . '/</td>';
	echo '<td align="left">';
	echo is_writable( $GLOBALS['rootDir'] . '/' . $folder ) ? '<b><font color="green">Writeable</font></b>' : '<b><font color="red">Unwriteable</font></b>' . '</td>';
	echo '</tr>';
}

/**
* @package Joomla
*/
class HTML_installer {

	function showInstallForm( $title, $option, $element, $client = "", $p_startdir = "", $backLink="" ) {
		?>
		<form enctype="multipart/form-data" action="setup.php" method="post" name="filename">
		<table width="800" cellspacing="0" cellpadding="3" border="0" align="center" style="border: 1px solid rgb(102, 102, 102);">
		<tr>
				<td bgcolor="#ffffff" align="center"><?php echo $title;?><br>
<?php echo $backLink;?></td>
		</tr>

		</table>

<br>
		<table class="adminform" width="800" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF">
		<tr>
			<td bgcolor="#2b6082"  align="left" class="btitle">Upload Package File <font size="+1" color="#FF8400" ><?php echo $element; ?></font></td>
			
			</td>
		</tr>
		<tr  bgcolor="#FFFFFF">
			<td  height="50" align="center">
			Package File:
			<input class="text_area" name="userfile" type="file" size="70"/>
			<input class="button" type="submit" value="Upload File &amp; Install" />
			</td>
		</tr>
	
		</table>

		<input type="hidden" name="task" value="uploadfile"/>
		<input type="hidden" name="option" value="<?php echo $option;?>"/>
		<input type="hidden" name="element" value="<?php echo $element;?>"/>
		<input type="hidden" name="client" value="<?php echo $client;?>"/>
		</form>
		<br />
<?php
	}

	/**
	* @param string
	* @param string
	* @param string
	* @param string
	*/
	function showInstallMessage( $message, $title, $url ) {
		global $PHP_SELF;
		?>
<table class="adminheading">
		<tr>
			<th class="install">
			<?php echo $title; ?>
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<td align="left">
			<strong><?php echo $message; ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
			[&nbsp;<a href="<?php echo $url;?>" style="font-size: 16px; font-weight: bold">Continue ...</a>&nbsp;]
			</td>
		</tr>
		</table>
		<?php
	}
	
	function showform() {
		?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td><div align="center"><strong>Chọn loại install:</strong></div></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td><a href="?element=block">block</a></td>
		  </tr>
		  <tr>
			<td><a href="?element=module">module</a></td>
		  </tr>
		</table>
		<?php
	}
	
	function header() {
		?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>CMS vnTRUST Installer 2.0</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<style type="text/css">
<!--
html {
	overflow-x: auto;
	scrollbar-face-color: #CECECE;
	scrollbar-shadow-color: #6B6B6B;
	scrollbar-highlight-color: #F8F8F8;
	scrollbar-3dlight-color: #8A8A8A;
	scrollbar-darkshadow-color: #8A8A8A;
	scrollbar-track-color: #8A8A8A;
	scrollbar-arrow-color: #215A8C;
}
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #061D36;
	line-height:18px;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color:#DCDCDC;
}
a:link {
	color: #FF8400;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #FF8400;
}
a:hover {
	text-decoration: none;
	color: #FF6600;
}
a:active {
	text-decoration: none;
	color: #FF8400;
}
img { border : 0px; }
.bdr {
	border: 1px solid #344559;
}
.ctittle {
	font-size: 14px;
	font-weight: bold;
	color: #FFFFFF;
	text-decoration: none;
	background-color:#0063B0;
}
.mtittle {
	font-size: 16px;
	font-weight: bold;
	color: #FFFFFF;
	text-decoration: none;
	background-color:#2B6082;
	height:40px;
}
.ftittle {
	font-size: 12px;
	font-weight: bold;
	color: #FFFFFF;
	text-decoration: none;
	background-color:#3A74AB;
	height:25px;
}
.tittle {
	font-size: 12px;
	color: #FFFFFF;
	text-decoration: none;
	background-color:#3F668A;
}
.br_sp {
	border-top-width: 1px;
	border-top-style: dotted;
	border-top-color: #003366;
	color: #003333;
	text-decoration: none;
	font-size: 11px;
}
.btitle {
	color: #FFFFFF;
	font-weight: bold;
	padding:5px;
}
.rowdir {
	background-color:#2B6082;
	color:#FFFFFF;
	font-weight:bold;
}
.rowfile {
	background-color:#FFFFFF;
}
.rowbut {
	background-color:#FFFFCC;
}
.des {
	font-size:9px;
	text-align:left;
	color:#666666;	
}
-->
</style>
<script type="text/javascript" >

function isChecked(isitchecked){
	if (isitchecked == true){
		document.adminForm.boxchecked.value++;
	}
	else {
		document.adminForm.boxchecked.value--;
	}
}
function submitbutton(pressbutton) {
	var form = document.adminForm;
		form.task.value=pressbutton;
		form.submit();
}

function listItemTask( id, task ) {
    var f = document.adminForm;
    cb = eval( 'f.' + id );
    if (cb) {
        for (i = 0; true; i++) {
            cbx = eval('f.cb'+i);
            if (!cbx) break;
            cbx.checked = false;
        } // for
        cb.checked = true;
        f.boxchecked.value = 1;
        submitbutton(task);
    }
    return false;
}


function saveorder( n ) {
	checkAll_button( n );
}

//needed by saveorder function
function checkAll_button( n ) {
/*
	for ( var j = 0; j <= 30; j++ ) {
		box = eval( "document.adminForm.cb" + j );
		if ( box ) {
			if ( box.checked == false ) {
				box.checked = true;
			}
		} else {
			alert("You cannot change the order of items, as an item in the list is `Checked Out`");
			return;
		}
	}*/
	submitform('saveorder');
}

/**
* Submit the admin form
*/
function submitform(pressbutton){
	document.adminForm.task.value=pressbutton;
	try {
		document.adminForm.onsubmit();
		}
	catch(e){}
	document.adminForm.submit();
}
</script>
</head>
<body>
<table width="100%" bgcolor="#1E476A" align=center border=0 cellspacing=1 cellpadding=1>
	<tr>
	<td align=center class="mtittle" valign="middle"><b>CÀI ĐẶT MODULE - BLOCK Cho CMS VNTRUST v2.0</b></td>
	</tr>
</table>
<br>
<table width="800" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td align="center">&nbsp;

<div align="center"><strong><?=$mess?></strong></div>
		<?php
	}

	function footer() {
		?>
		</td>
  </tr>
</table>
		<table width="800" border="0" cellspacing="0" cellpadding="0" align="center" style="margin-top:50px;">
			<td bgcolor="#2b6082"  class="btitle" align="center"><a href="?element=plugin">QUẢN LÝ Plugins</a></td>
      
			<td bgcolor="#2b6082"  class="btitle" align="center"><a href="?element=block">QUẢN LÝ BLOCK</a></td>

			<td bgcolor="#2b6082"  class="btitle" align="center"><a href="?element=module">QUẢN LÝ MODULE</a></td>
		  </tr>
		</table>
		<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" bgcolor="#2B6082" class="btitle">Copyright © 2006 :: <a href="http://trust.vn" target="_blank">vnTRUST Co., LTD</a> :: All Rights Reserved</td>
	<td width="200" align="center" bgcolor="#3A74AB" class="btitle">Powered by <a href="http://trust.vn" target="_blank">vnTRUST CMS </a></td>
  </tr>
</table>
		<?php
	}
	

	/**
	* @param array
	* @param string The option
	*/
	function editPositions( &$positions) {
	global $rooturl;
		$rows = 9;
		$cols = 2;
		$n = $rows * $cols;
		?>
		<form action="setup.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="templates">
			Module Positions
			</th>
		</tr>
		</table>

		<table class="adminlist" width="80%" cellpadding="0" cellspacing="0" align="center">
		<tr>
		<?php
		for ( $c = 0; $c < $cols; $c++ ) {
			?>
			<th width="25">
			#
			</th>
			<th align="left">
			Position
			</th>
			<?php
		}
		?>
		</tr>
		<?php
		$i = 1;
		for ( $r = 0; $r < $rows; $r++ ) {
			?>
			<tr>
			<?php
			for ( $c = 0; $c < $cols; $c++ ) {
				?>
				<td>(<?php echo $i; ?>)</td>
				<td>
				<input type="text" name="position[<?php echo $i; ?>]" value="<?php echo @$positions[$i-1]->position; ?>" size="10" maxlength="10" />
				</td>
				
				<?php
				$i++;
			}
			?>
			</tr>

			<?php
		}
		?>
			<tr>
				<td colspan="10" align="center" style="padding-top:50px;">
            			<a class="toolbar" href="javascript:submitbutton('save_positions');">
				<img src="<?=$rooturl . '/install/'?>images/save_f2.png"  alt="Save" name="save_positions" title="Save" align="middle" border="0" />				<br />Save</a>
				</td>
			</tr>
		</table>
		<input type="hidden" name="element" value="position" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
	
	
}
?>
