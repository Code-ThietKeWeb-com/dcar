
<!-- BEGIN: manage -->
<form action="{data.link_fsearch}" method="post" name="fSearch">
<table width="100%" border="0" cellspacing="2" cellpadding="2" align="center" class="tableborder">
  
 <tr>
    <td class="row1" ><strong>Xóa từ ngày :</strong>  </td>
    <td align="left" class="row0"> 
    <input type="text" name="date_begin" id="date_begin" value="{data.date_begin}" size="15" maxlength="10"    /> &nbsp;&nbsp; <strong>đến :</strong> <input type="text" name="date_end" id="date_end" value="{data.date_end}" size="15" maxlength="10"   />  </td>
  </tr> 
  <tr>
  
  <td align="left"><strong>{LANG.search}  :</strong> &nbsp;&nbsp;&nbsp;  </td>
  <td align="left">{data.list_search} &nbsp;&nbsp;<strong>{LANG.keyword} :</strong> &nbsp;
    <input name="keyword"  value="{data.keyword}"type="text" size="20">
    <input name="btnSearch" type="submit" value=" Search " class="button"></td>
  </tr>
  
  <tr>
    <td width="15%"><strong>{LANG.totals}:</strong> &nbsp;</td>
    <td width="85%" ><b class="font_err">{data.totals}</b></td>
  </tr>

</table>
</form>
{data.err}
<br />
{data.table_list}
<br />
<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1" class="bg_tab">
  <tr>
    <td  height="25">{data.nav}</td>
  </tr>
</table>
<br />
<!-- END: manage -->

<!-- BEGIN: html_popup -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>[:: Admin ::]</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<LINK href="{DIR_STYLE}/global.css" rel="stylesheet" type="text/css">
<!--[if gte IE 6]>
<link rel='stylesheet' href="{DIR_STYLE}/ie.css" type='text/css' media='all' />
<![endif]-->
<link href="{DIR_STYLE}/style_tooltips.css" rel="stylesheet" type="text/css" />
<link rel='stylesheet' href='{DIR_JS}/thickbox/thickbox.css' type='text/css' media='all' />
{EXT_STYLE}
<script language="javascript" >
	var ROOT = "{CONF.rooturl}";
	var DIR_IMAGE = "{DIR_IMAGE}";
	var lang_js = new Array(); 
		lang_js["please_chose_item"]   	= "{LANG.please_chose_item}'";
		lang_js["are_you_sure_del"]   	= "{LANG.are_you_sure_del}";
</script>
<script language="javascript1.2" src="{DIR_JS}/jquery.js"></script> 
<script language="javascript1.2" src="{DIR_JS}/admin/js_admin.js"></script>

<script language="javascript1.2" src="{DIR_JS}/admin/common.js"></script>
<script language="javascript1.2" src="{DIR_JS}/admin/ajax-response.js"></script>
<script type="text/javascript" src="{DIR_JS}/tooltips.js"></script>
<script type='text/javascript' src='{DIR_JS}/thickbox/thickbox.js'></script> 
<script id='ext_javascript'></script>
{EXT_HEAD}

</head>
<body style="margin:10px;" > 
<div style="padding:5px; color:#F00; font-size:14px; " > {data.f_title} </div>
{data.err}
<br />
{data.table_list}
<br />
 
</body>
</html> 
<!-- END: html_popup -->
