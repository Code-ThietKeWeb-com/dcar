<?php
	session_start();
	define('IN_vnT',1);
	require_once("../../_config.php"); 
	require_once("../../includes/class_db.php"); 
	require_once("../../includes/class_functions.php"); 	
	$DB = new DB;
	$vnT->DB = $DB ;
	$func = new Func_Global;
	$conf=$func->fetchDbConfig($conf);

	$vnT->lang_name = (isset($_GET['lang'])) ? $_GET['lang']  : "vn" ;
	$func->load_language('support','blocks');
	
?>
<html>
<head>
<title>.: SUPPORT YAHOO :.</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/popup.css" rel="stylesheet" type="text/css" />
 
</head>

<body >
<div id="pcontainer" style="margin:5px" >

<table width="100%" border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="4"><img src="images/support_l.gif" width="4" height="29"></td>
        <td align="center" bgcolor="#CFCFCF" class="font_f_title"><?php echo $vnT->lang['support']['f_title']; ?></td>
        <td width="10"><img src="images/support_r.gif" width="10" height="29"></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EEEEEE">
      <tr>
        <td style="background:url(images/boxSuppor2.gif) #EEEEEE no-repeat right"><img src="images/boxSuppor1.gif" width="5" height="5"></td>
      </tr>
      <tr>
        <td style="padding:5px 10px;">
				
				<table width="100%" border="0" cellspacing="3" cellpadding="3">
				<?php
					$result = $DB->query ("select * from support WHERE type='yahoo' AND display=1 ORDER BY s_order ASC ");
					while ($row = $DB->fetch_row($result))
					{
						$nick = $row['nick'];
						
						 /*
						$imgsrc =  "images/".$row['type']."_off.gif";
						$yahoo_url = "http://opi.yahoo.com/online?u={$nick}&m=a&t=1";
						$tmp = @file_get_contents($yahoo_url);
						if($tmp != "00" && $tmp != "01") {
							$imgsrc =  "images/".$row['type']."_off.gif";
						}
						if($tmp == "01") {
							$imgsrc =  "images/".$row['type']."_on.gif";
						} elseif($tmp == "00") {
							$imgsrc =  "images/".$row['type']."_off.gif";
						}
						*/
						$text .="<tr>
					<td > <a href=\"ymsgr:sendIM?".$nick."\"><img  align='absmiddle' class=img  alt=\"Trợ giúp qua Yahoo Messenger!\" src=\"http://opi.yahoo.com/online?u=".$nick."&amp;m=g&amp;t=2\"/></a><br><span class='nick'><a href=\"ymsgr:sendIM?".$nick."\">".$nick."</a></span><br>";
						if ($row['name'])	$text .= $vnT->lang['support']['name']." : <b>".$func->fetch_array($row['name'])."</b><br>";
						if ($row['title']) $text .= $vnT->lang['support']['department']." : <b>".$func->fetch_array($row['title'])."</b><br>";
						if ($row['phone'])	$text .= $vnT->lang['support']['phone']." : <b>".$func->HTML($row['phone'])."</b><br>";
						$text .= '</td><tr>';
					}
					
					echo $text;
				?>
				
					
					
				</table>
				</td>
      </tr>
      <tr>
        <td style="background:url(images/boxSuppor3.gif) no-repeat right"><img src="images/boxSuppor4.gif" width="5" height="5"></td>
      </tr>
    </table></td>
  </tr>
</table>

</div>
</body>

</html>