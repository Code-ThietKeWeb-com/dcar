<html>
<title><? echo $_GET['title'];?></title>
 <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
</head>
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
<style type="text/css">
html {
	SCROLLBAR-BASE-COLOR: #CCCCCCC;
	SCROLLBAR-ARROW-COLOR: #000000;
}

body {
	background:#FFFFFF;
	margin-left: 5px;
	margin-top: 5px;
	margin-right: 5px;
	margin-bottom: 5px;
}
td,th {
	FONT-FAMILY: verdana, arial, helvetica, sans-serif;
	font-size: 11px;
	color: #000000;
}

a:active {  text-decoration: none; color:#FF0707}
a:link {  text-decoration: none ; color:#FF0707}
a:visited {  text-decoration: none ; color:#FF0707}
a:hover {  color:#1074EF; text-decoration: underline; }

img { border : 0px; }

input {
    color: #000000;
}

</style>


<body onLoad="resizeWinTo('pcontainer');">
<table  border="0" cellpadding="0" cellspacing="0" id="pcontainer">
  <tr>
    <td><img src="<?=$_GET['image']?>"></td>
  </tr>
  <tr>
    <td align="center"><a href = "javascript:window.close()"><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><br>
        Close Window</font></a></td>
  </tr>
</table>

</body>
<html>