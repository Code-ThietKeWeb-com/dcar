<?php
/*================================================================================*\
|| 							Name code : skin_poll.php 		 			             				  	    # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}

class skin_poll
{

  //=================Skin===================
  function html_add ($data)
  {
    return <<<EOF
<script language=javascript>
	function getObj(id,d)
	{
		var i,x;  if(!d) d=document; 
		if(!(x=d[id])&&d.all) x=d.all[id]; 
		for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][id];
		for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=ylib_getObj(id,d.layers[i].document);
		if(!x && document.getElementById) x=document.getElementById(id); 
		return x;
	};

	function AddFiled()
	{
		var nextHiddenIndex = (parseInt(getObj("num").value)+1);
		//alert ("dong = "+nextHiddenIndex)
		getObj("dong" + nextHiddenIndex).style.display = document.all ? "block" : "table-row";
		getObj("num").value = nextHiddenIndex;
		if(nextHiddenIndex >= 20) getObj("attachMoreLink").style.display = "none";
			
    }	
	
		
</script>
<br>

      <form action="{$data['link_action']}" method="post" name="add_news" enctype="multipart/form-data" >
        <table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center>
          <tr>
            <td colspan=2 align="center" class="font_err">{$data['err']}</td>
          </tr>
          <tr>
            <td width="24%" align="right">Ch&#7911; &#273;&#7873; : </td>
            <td width="76%" align="left"><input name="pollerTitle" type="text" size="50" maxlength="250" value="{$data['pollerTitle']}"></td>
          </tr>
		  <tr>
            <td align="right">H&#236;nh  : </td>
            <td align="left">{$data['pic']}<input name="image" type="file" id="image" size="40" maxlength="250"> (*.jpg,*.gif) Only !</td>
          </tr>
          <tr>
            <td colspan="2" align="center" height="10"></td>
          </tr>
          <tr>
            <td colspan="2"><hr noshade></td>
          </tr>
		   {$data['html_dong']}
		   <tr >
		   <td  height="20">
            <td height="20"><div id="attachMoreLink"><a href="javascript:AddFiled()">[Add more]</a></div></td>
          </tr>
          <tr align="center">
            <td colspan="2">
			<input type="hidden" name="num" id="num" size="5" value="{$data['num']}" >
			<input type="submit" name="btnAdd" value="Submit "  class="button">&nbsp;&nbsp;
            <input type="reset" name="Submit2" value="Reset"  class="button">            </td></tr>
        </table>
    </form>
<br>
EOF;
  }

  //================= Dong  =======================
  //=====
  function html_dong ($data)
  {
    global $func, $DB, $vnT;
    return <<<EOF
<tr id="dong{$data['stt']}" style="{$data['style']}">
   <td align="right">Name Option {$data['stt']} : </td>
   <td align="left"><input name="optionText[]" type="text" size="50" maxlength="250" value="{$data['optionText']}">
   </td>
</tr>
EOF;
  }

  //====================================
  function html_add_option ($data)
  {
    global $func, $DB, $vnT;
    return <<<EOF
<script language=javascript>
	function getObj(id,d)
	{
		var i,x;  if(!d) d=document; 
		if(!(x=d[id])&&d.all) x=d.all[id]; 
		for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][id];
		for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=ylib_getObj(id,d.layers[i].document);
		if(!x && document.getElementById) x=document.getElementById(id); 
		return x;
	};

	function AddFiled()
	{
		var nextHiddenIndex = (parseInt(getObj("num").value)+1);
		//alert ("dong = "+nextHiddenIndex)
		getObj("dong" + nextHiddenIndex).style.display = document.all ? "block" : "table-row";
		getObj("num").value = nextHiddenIndex;
		if(nextHiddenIndex >= 20) getObj("attachMoreLink").style.display = "none";
			
    }	
	
		
</script>
<br>

      <form action="{$data['link_action']}" method="post" name="add_news" >
        <table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center>
          <tr>
            <td colspan=2 align="center" class="font_err">{$data['err']}</td>
          </tr>
          <tr>
            <td width="24%" align="right">Ch&#7911; &#273;&#7873; : </td>
            <td width="76%" align="left"><input name="pollerTitle" type="text" size="50" maxlength="250" value="{$data['pollerTitle']}" readonly="ReadOnly"></td>
          </tr>
          <tr>
            <td colspan="2" align="center" height="10"></td>
          </tr>
          <tr>
            <td colspan="2"><hr noshade></td>
          </tr>
		   {$data['html_dong']}
		   <tr >
		   <td  height="20">
            <td height="20"><div id="attachMoreLink"><a href="javascript:AddFiled()">[Add more]</a></div></td>
          </tr>
          <tr align="center">
            <td colspan="2">
			<input type="hidden" name="num" id="num" size="5" value="{$data['num']}" >
			<input type="submit" name="btnAdd" value="Submit "  class="button">&nbsp;&nbsp;
            <input type="reset" name="Submit2" value="Reset"  class="button">            </td></tr>
        </table>
    </form>
<br>
EOF;
  }

  //=====
  function html_edit_poll ($data)
  {
    global $func, $DB, $vnT;
    return <<<EOF
<br>

      <form action="{$data['link_action']}" method="post" name="add_news" enctype="multipart/form-data" >
        <table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center>
          <tr>
            <td colspan=2 align="center">{$data['err']}</td>
          </tr>
          <tr>
            <td width="24%" align="right">Ch&#7911; &#273;&#7873; : </td>
            <td width="76%" align="left"><input name="pollerTitle" type="text" size="50" maxlength="250" value="{$data['pollerTitle']}"></td>
          </tr>
		  <tr>
            <td align="right">H&#236;nh  : </td>
            <td align="left">{$data['pic']}<input name="image" type="file" id="image" size="40" maxlength="250"> (*.jpg,*.gif) Only !</td>
          </tr>
          <tr>
            <td colspan="2" align="center"><input type="submit" name="btnEdit" value="Edit Poller"  class="button"></td>
          </tr>
        </table>
    </form>
<br>
EOF;
  }

  //=====
  function html_edit ($data)
  {
    global $func, $DB, $vnT;
    return <<<EOF
<br>

      <form action="{$data['link_action']}" method="post" name="add_news" >
        <table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center>
          <tr>
            <td colspan=2 align="center">{$data['err']}</td>
          </tr>
    
          <tr>
            <td width="25%" align="right"> Ch&#7911; &#273;&#7873; : </td>
            <td width="75%" align="left">
             {$data['list_cat']}
			 </td>
          </tr>
          <tr>
            <td align="right"> Ti&#234;u &#273;&#7873; l&#7921;a ch&#7885;n: </td>
            <td align="left"><input name="optionText" type="text" id="name" size="50" maxlength="250" value="{$data['optionText']}"></td>
          </tr>
          <tr align="center">
            <td colspan="2">
			<input type="submit" name="btnEditOption" value="Edit Option" class="button">&nbsp;&nbsp;
              <input type="reset" name="Submit2" value="Reset"  class="button">
            </td></tr>
        </table>
    </form>
<br>
EOF;
  }

  //=====
  function html_manage ($data)
  {
    global $func, $DB, $vnT;
    return <<<EOF
{$data['err']}
<br>
 <form action="{$data['link_search']}" method="post" name="myform">
<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
 
    <tr><td ><strong>Poll Name ({$data['lang']}):</strong> &nbsp; {$data['list_cat']}&nbsp;&nbsp;<a href="?mod=poll&act=poll&sub=add_option&id={$data['poll_id']}"><img src="{$vnT->dir_images}/add.gif"  alt="Add Option" align="absmiddle"/></a>&nbsp;
	  <a href="?mod=poll&act=poll&sub=edit_poll&id={$data['poll_id']}"><img src="{$vnT->dir_images}/edit.gif" width="16"  alt="Edit poller" align="absmiddle"></a>&nbsp;
	  <a href="javascript:del_item('?mod=poll&act=poll&sub=del&poll_id={$data['poll_id']}')" ><img src="{$vnT->dir_images}/delete.gif" width="16"  alt="Delete poller" align="absmiddle"></a>
	 </td>
	 </tr>
</table> 
</form>
{$data['table_list']}
<br>

EOF;
  }
  //end class
}
?>