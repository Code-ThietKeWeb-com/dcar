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
<title>.: SUPPORT GOOGLE :.</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/popup.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
<!--
function getRefToDivMod( divID, oDoc ) {
  if( !oDoc ) { oDoc = document; }
  if( document.layers ) {
    if( oDoc.layers[divID] ) { return oDoc.layers[divID]; } else {
      for( var x = 0, y; !y && x < oDoc.layers.length; x++ ) {
        y = getRefToDivMod(divID,oDoc.layers[x].document); }
      return y; } }
  if( document.getElementById ) { return oDoc.getElementById(divID); }
  if( document.all ) { return oDoc.all[divID]; }
  return document[divID];
}
function resizeWinTo( idOfDiv ) {
  var oH = getRefToDivMod( idOfDiv ); if( !oH ) { return false; }
  var oW = oH.clip ? oH.clip.width : oH.offsetWidth;
  var oH = oH.clip ? oH.clip.height : oH.offsetHeight; if( !oH ) { return false; }
  var x = window; x.resizeTo( oW + 200, oH + 200 );
  var myW = 0, myH = 0, d = x.document.documentElement, b = x.document.body;
  if( x.innerWidth ) { myW = x.innerWidth; myH = x.innerHeight; }
  else if( d && d.clientWidth ) { myW = d.clientWidth; myH = d.clientHeight; }
  else if( b && b.clientWidth ) { myW = b.clientWidth; myH = b.clientHeight; }
  if( window.opera && !document.childNodes ) { myW += 16; }
  	var myw = oW + ( ( oW + 200 ) - myW )+(5);
	var myh = oH + ( (oH + 200 ) - myH )+(5*2);
	if(myw > screen.availWidth){
		myw = screen.availWidth;
	}
	if(myh > screen.availHeight){
		myh = screen.availHeight;
	} 
  x.resizeTo( myw, myh );
  var scW = screen.availWidth ? screen.availWidth : screen.width;
  var scH = screen.availHeight ? screen.availHeight : screen.height;
  x.moveTo(Math.round((scW-myw)/2),Math.round((scH-myh)/2));
}
// -->
</script>
</head>
<script language="javascript">
 resizeWinTo('pcontainer');
</script>
<body>
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
				
				<table width="100%" border="0" cellspacing="2" cellpadding="2">
				<?php
					$result = $DB->query ("select * from support WHERE type='google'  ");
					while ($row = $DB->fetch_row($result)) {
						$nick = $row['nick'];							
						$text .="<tr>
											<td valign=\"top\"><img style=\"border: medium none ; padding: 0pt 2px 0pt 0pt;\" src=\"".$nick."&amp;w=9&amp;h=9\" alt=\"\" width=\"9\" height=\"9\"><a href=\"".$nick."\" target=\"_blank\" title=\"Click đây để nói chuyện với ".$func->HTML($row['name'])."\"><strong>".$func->HTML($row['name'])."</strong></a><br>";												
												if ($row['title'])	$text .="<span>".$func->HTML($row['title'])."</span><br>";
												if ($row['phone'])	$text .= "ĐT : <b>".$func->HTML($row['phone'])."</b><br>";
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