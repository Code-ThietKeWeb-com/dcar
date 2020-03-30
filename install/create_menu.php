<?php
	define('IN_vnT',1);
	require_once(  '../_config.php' );
	require_once( '../includes/class_db.php' );
	$DB = new DB; 

	if  (isset($_POST['doOrder'])){
		$txtOrder = $_POST['txtOrder'];
		foreach($txtOrder as $key => $value){
			$dup['displayorder'] = $value;
			$DB->do_update("admin_menu",$dup,"id=$key");
		}
		
		$mess = "Cập nhật thứ tự thành công ";
	}
	
	if (isset($_POST['doCreate'])){
		require_once('includes/class_xml.php');
		//===============
		$xml = new XMLexporter();
		$xml->add_group('navgroups',array('master' => "true", 'version' => "1.0", 'product' => "CMS vnTRUST"));
		$xml->doc.=	"\r\n";
		$result = $DB->query("select * from admin_menu where parentid=0  order by displayorder");
		$i=0;
		while ($row = $DB->fetch_row($result)){
			$xml->doc.=	"<!-- NHOM ".$row['title']."-->\r\n";
			$xml->add_group('navgroup',array('gid' => $row['g_name'],
																			 'text' => $row['title'],
																			 'hr' => 'true',
																			 'displayorder' => $i
																			 ));	
				
				// lay menu
				$res_sub = $DB->query("select * from admin_menu where parentid=".$row['id']." order by displayorder " );
				$j=0;
				while ($row_sub = $DB->fetch_row($res_sub)){
					$xml->doc.=	"\r\n";	
					$xml->add_group('navoption',array('displayorder' => $j ));
					$xml->add_tag('name',$row_sub['title'],"",false);
					
					if ($row_sub['module'])
					$xml->add_tag('mod',$row_sub['module'],"",false);
					
					if ($row_sub['block'])
					$xml->add_tag('block',$row_sub['block'],"",false);
					
					if ($row_sub['act'])
					$xml->add_tag('act',$row_sub['act'],"",false);
					
					if ($row_sub['sub'])
						$xml->add_tag('sub',$row_sub['sub'],"",false);
					
					$j++;
					 
					$xml->close_group();
				}
			$xml->close_group();
			$xml->doc.=	"\r\n";
			$i++;
		}
		$xml->close_group();
		$xml->doc.=	"\r\n";
		
		$content_xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n\r\n";
		$content_xml .= $xml->output();
		$xml = null;
		
		$path = "../admin/xml/acp_menu.xml";
		if($handle = @fopen($path, "w")){
			fwrite($handle, $content_xml, strlen($content_xml));
			fclose($handle);
			$mess =  "Tạo menu admin thành công <br> <a  href='../admin'>.: Vào trang Admin :.</a>";
		}else{
			$mess = "Khong mo duoc file acp_menu.xml ";
		}	
	}
	
	
	
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
.adminlist{ margin-top:10px;}
.red{color:#FF8400}
-->
</style>
<table width="100%" bgcolor="#1E476A" align=center border=0 cellspacing=1 cellpadding=1>
	<tr>
	<td align=center class="mtittle" valign="middle"><b>CÀI ĐẶT MENU ADMIN Cho CMS VNTRUST v2.0</b></td>
	</tr>
</table>
<br>
<table width="800" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td align="center">
			<b style="color:#FF0000"> <?php echo $mess; ?>&nbsp;</b>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="adminheading">
			<tr>
			<td bgcolor="#2b6082" align="left" class="btitle">ADMIN MENU</td>
		  </tr>
			</table>
			<form name="adminForm" method="post" action="create_menu.php">
			<?php
				$result = $DB->query("select * from admin_menu where parentid=0  order by displayorder ");
				$text='';
				while ($row = $DB->fetch_row($result)){
					$text.='<table cellspacing="1" cellpadding="1" border="0" bgcolor="#999999" class="adminlist">'	;
					$text.='<tr bgcolor="#cccccc">
									<td width="10%" class="title" align=center><input name="txtOrder['.$row['id'].']" id="txtOrder" type="text" value="'.$row['displayorder'].'" size=4 style="text-align:center;font-weight:bold;"></td>
									<td width="10%" class="title" colspan=3>
									<strong class="red">['.$row['g_name'].']</strong>&nbsp;<strong>'.$row['title'].'</strong> </td></tr>';
					
					$res_sub = $DB->query("select * from admin_menu where parentid=".$row['id']." order by displayorder " );
					while ($row_sub = $DB->fetch_row($res_sub)){
						if ($row_sub['block']) {
							$option = "?block=".$row_sub['block'];
						}else{
							$option = "?mod=".$row_sub['module'];
						}
						
						if ($row_sub['act']) {
							$action = "&amp;act=".$row_sub['act'];
						}
						if ($row_sub['sub']) {
							$action .= "&amp;sub =".$row_sub['sub'];
						}
						
						$text.='<tr bgcolor="#fff">
						<td width=10% align=center><input name="txtOrder['.$row_sub['id'].']" id="txtOrder" type="text" value="'.$row_sub['displayorder'].'" size=5 style="text-align:center"></td>
						<td  width=40% align=left>'.$row_sub['title'].'</td>
						<td width=20% align=left>'.$option.'</td>
						<td width=25% align=left>'.$action.'</td>
						</tr>';
					}
					$text.='</table>'	;
				}
				echo $text;
			?>
			
			


		<p><input name="doOrder" type="submit" value="Cập nhật thứ tự">&nbsp;&nbsp;<input name="doCreate" type="submit" value="Tạo xml menu"></p> 
		</form>
		</td>
  </tr>
</table>
		<table width="800" border="0" cellspacing="0" cellpadding="0" align="center" style="margin-top:10px;">

			<td bgcolor="#2b6082"  class="btitle" align="center"><a href="setup.php?element=block">QUẢN LÝ BLOCK</a></td>
			
			<td bgcolor="#2b6082"  class="btitle" align="center"><a href="create_menu.php">TẠO FILE XML MENU ADMIN</a></td>
			<td bgcolor="#2b6082"  class="btitle" align="center"><a href="setup.php?element=module">QUẢN LÝ MODULE</a></td>
		  </tr>
		</table>
		<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" bgcolor="#2B6082" class="btitle">Copyright © 2006 :: <a href="http://viettechltd.com" target="_blank">vnTRUST Co., LTD</a> :: All Rights Reserved</td>
	<td width="200" align="center" bgcolor="#3A74AB" class="btitle">Powered by <a href="http://viettechltd.com" target="_blank">vnTRUST CMS </a></td>
  </tr>
</table>	