<!-- BEGIN: box -->
<div class="box">
	 <div class="box-title"><div class="fTitle"> {data.f_title}</div></div> 
  <div class="box-content">
    {data.content}
  </div>
</div>      
<!-- END: box -->

<!-- BEGIN: box_left -->
<div class="box">
	 <div class="box-title"><div class="fTitle"> {data.f_title}</div></div> 
  <div class="box-content">
    {data.content}
  </div>
</div> 
<!-- END: box_left -->

<!-- BEGIN: box_middle -->
<div class="box_mid">
  <div class="mid-title">
    <div class="titleL">{data.f_title}</div>
    <div class="titleR">{data.more}</div>
    <div class="clear"></div>
  </div>
  <div class="mid-content">
    {data.content}
  </div>          
</div> 
<!-- END: box_middle -->

<!-- BEGIN: box_right -->
<div class="box">
	 <div class="box-title"><div class="fTitle"> {data.f_title}</div></div> 
  <div class="box-content">
    {data.content}
  </div>
</div> 
<!-- END: box_right -->

<!-- BEGIN: advertise -->{data.list_item}<!-- BEGIN: html_item --><div class="advertise"><a href="{row.link}" title="{row.title}"  onmousedown="return rwt(this,'advertise','{row.l_id}')" target='{row.target}'><img src="{row.src}" alt="{row.title}" /></a></div><!-- END: html_item --><!-- END: advertise -->

<!-- BEGIN: box_search -->
<script language="javascript" >
	function check_search(f)
	{
		var key_default = "{LANG.global.keyword_default}" ;
		var keyword = f.keyword.value;		
		var key_len = f.keyword.value.length;
		if( (keyword==key_default) || (keyword=='') )		{
			alert("{LANG.global.key_search_empty}");
			f.keyword.focus();
			return false;
		}
		if( key_len<2 ){
			alert("{LANG.global.key_search_invalid}");
			f.keyword.focus();
			return false;
		} 
		return true;
	}	

</script>
<form id="formSearch" name="formSearch" method="post" action="{data.link_search}" onSubmit="return check_search(this);" class="box_search">
<input name="do_search" value="1" type="hidden" />
	<table cellspacing="0" cellpadding="0" border="0"  >
		<tr>
 		<td  width="160" ><input name="keyword" id="keyword" type="text" class="text_search"  onfocus="if(this.value=='{LANG.global.keyword_default}') this.value='';" onBlur="if(this.value=='') this.value='{LANG.global.keyword_default}';"  value="{data.keyword}"  /></td>
		<td align="left" width="25"><button  id="btn-search" name="btn-search" type="submit" class="btn" value="{LANG.global.btn_search}" ><span >{LANG.global.btn_search}</span></button> </td>
		</tr>
	</table>
</form>
<!-- END: box_search --> 

<!-- BEGIN: box_redirect -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>{data.mess}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv='refresh' content='{data.time_ref}; url={data.url}' />
<link href="{DIR_STYLE}/screen.css" rel="stylesheet" type="text/css">
</head>	
<body>
<div style="overflow:hidden; margin:200px auto;" >
  <div id="box_redirect">
  	<div class="top"><img src="{DIR_IMAGE}/thongbao.gif" width="32" height="22" align="absmiddle" />&nbsp;{LANG.global.announce}</div>
    <div class="middle" >
    	<p class="fontMess" >{data.mess}</p>
        <p style="text-align:center"><img src="{DIR_IMAGE}/loading.gif" width="78" height="7" /></p>
        <p class="font_err" style="text-align:center">({data.mess_redirect})</p>
    </div>
    <div class="bottom">.::[ {LANG.global.copyright} ]::.</div>
  </div>
</div>
</body>
</html>       
<!-- END: box_redirect -->