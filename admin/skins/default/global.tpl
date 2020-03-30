<!-- BEGIN: body --><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>[Admin] - TRUST.vn CMS</title>
<meta name="author" content="www.thietkeweb.com | TRUST.vn"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, maximum-scale=1, user-scalable=yes" />
<link rel="SHORTCUT ICON" href="vntrust.ico" type="image/x-icon" />
<link rel="icon" href="vntrust.ico" type="image/gif" >
<link href="{DIR_SKIN}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="{DIR_STYLE}/fonts/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="{DIR_STYLE}/global.css?v={data.ver}" rel="stylesheet" type="text/css">
<link rel='stylesheet' href='{DIR_JS}/thickbox/thickbox.css' type='text/css' media='all' />
<link rel='stylesheet' href='{DIR_JS}/fancybox3/jquery.fancybox.css' type='text/css'  />
<link rel='stylesheet' href='{DIR_JS}/jquery_alerts/jquery.alerts.css' type='text/css' media='all' />
{EXT_STYLE}
<script language="javascript" >var ROOT = "{CONF.rooturl}";	var DIR_IMAGE = "{DIR_IMAGE}"; var langcp = "{data.langcp}" ;  var lang = "{data.lang}" ;  var lang_js = new Array();   lang_js["please_chose_item"]   	= "{LANG.please_chose_item}";  lang_js["are_you_sure_del"]   	= "{LANG.are_you_sure_del}";</script>
<script type='text/javascript' src="{DIR_JS}/jquery.min.js"></script>
<script type='text/javascript' src="{DIR_JS}/jquery-migrate.min.js"></script>
<script type="text/javascript" src="{DIR_SKIN}/bootstrap/js/bootstrap.min.js"></script>
<script type='text/javascript' src='{DIR_JS}/thickbox/thickbox.js?v=3.1'></script>
<script type='text/javascript' src='{DIR_JS}/fancybox3/jquery.fancybox.js'></script>
<script type="text/javascript" src="{DIR_JS}/jquery_alerts/jquery.alerts.js"></script>
<script type="text/javascript" src="{DIR_JS}/jquery_validate/jquery.validate.min.js?v=1.11.1"></script>
<script type="text/javascript" src="{DIR_JS}/scrollbar/jquery.slimscroll.min.js"></script>
<script type='text/javascript' src="{DIR_JS}/admin/js_admin.js?v={data.ver}"></script>
{EXT_HEAD}
</head>
<body >
<div id="vnt-wrap">
<div id="vnt-header">

         <div class="headerLeft">
            <ul id="vnt-menuTop">
                <li class="divmenu"><a data-title="Menu" class="clickMenu" href="javascript:void(0)"><i class="fa fa-bars"></i><span class="spantitle">Menu</span></a></li>
                <li><a data-title="Hướng dẫn sử dụng" href="http://huongdan.thietkeweb.com" target="_blank"><i class="fa fa-book"></i></i></a></li>
                <li><a data-title="Hỗ trợ" href="http://hotro.thietkeweb.com" target="_blank"><i class="fa fa-life-ring"></i></a></li>
                <li class="logoMenu" data-title="TRUST.vn CMS"><img src="{DIR_IMAGE}/TRUSTvn_favicon_chon.png" alt="TRUST.vn CMS" /></li>
            </ul>
         </div>
         <div class="headerRight">
            <div class="top_admin">
                <div class="admin_title">
                    <div class="aImg"><i class="fa fa-user"></i></div>
                    <div class="aTitle">{admininfo.username}</div>
                    <div class="clear"></div>
                </div>
                <div class="admin_menu">
                    <div class="mTitle">Tài khoản</div>
                    <div class="mContent">
                        <a class="mBttom"  href="?mod=admin&act=admin&sub=edit&id={admininfo.adminid}" >
                            <i class="fa fa-list-ul"></i>
                            {LANG.account}
                        </a>
                        <a class="mBttom" href="?act=logout">
                            <i class="fa fa-sign-out"></i>
                            {LANG.logout}
                        </a>
                    </div>
                </div>
            </div>

             {data.box_lang}

            <div class="clear"></div>
         </div>
         <div class="clear"></div>
    </div>




	 
  <div class="clear"></div>
	<div id="vnt-content">
  	<div id="vnt-menu">
      <div id="wrapper-menu">
    	 {BOX_LEFT}
      </div>
    </div>
    <div id="vnt-main">
    	<div class="wrap-main">
    	{PAGE_CONTENT}
      </div>
    </div>
    <div class="clear">&nbsp;</div>
  </div>
  <div id="vnt-footer">
    <div class="copyright">      
    	Copyright since 2004 © <b>TRUST.vn CMS</b> <br/>[MADEBY]
    </div>
  </div>
</div>
<script type="text/javascript" src="{DIR_JS}/admin/jeip.js"></script>
<script type="text/javascript">
	function  quick_edit(obj,text_data){
		$( "#"+obj ).eip( "save.php", { select_text: true,data: text_data } );	
		$( "#"+obj ).trigger("click");
		$("#btn-"+ obj).hide();
	}
</script> 
</body>
</html>      
<!-- END: body -->

<!-- BEGIN: box_main -->
<div id="box_main">
	<div class="vnt-title">
  	<h2><span class="{data.icon}">{data.f_title}</span></h2>
    <div class="vnt-lang">{data.row_lang}</div>
    <div class="clear"></div>
  </div>
  <div class="vnt-main">
  	<div class="vnt-tool-menu">{data.menu}</div>
    <div class="box_content">{data.content}</div>
  </div>
  
</div>
     
<!-- END: box_main -->

<!-- BEGIN: box -->
<div class="postbox" id="{data.id}">
  <div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>{data.f_title}</span></h3>
  <div class="inside"> {data.content} </div>
</div>     
<!-- END: box -->

<!-- BEGIN: box_manage -->
<div class="box-manage">
<form action="{data.link_action}" method="post" name="manage" id="manage">
    <div class="nav-action nav-top">{data.button}</div>
    <div class="table-list table-responsive">
    <table  class="table table-sm table-bordered table-hover " id="table_list" >
        {data.list}
    </table>
    </div>
    <div class="nav-action nav-bottom">{data.button}</div>
    <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
    <input type="hidden" name="do_action" id="do_action" value="" >
</form>
</div>
<!-- END: box_manage -->


<!-- BEGIN: box_redirect -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv='refresh' content='{data.time_ref}; url={data.url}' />
    <title>.: LOGIN - ADMIN :.</title>
    <link href="{DIR_STYLE}/fonts/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="{DIR_JS}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="{DIR_JS}/jquery.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/jquery-migrate.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/bootstrap/js/bootstrap.min.js"></script>
    <link href="{DIR_STYLE}/addstyle.css" rel="stylesheet" type="text/css"/>
    <link href="{DIR_STYLE}/loginsuccess.css" rel="stylesheet" type="text/css"/>
</head>

<body>
<div class="stext1">{data.mess}</div>
<!--div class="stext2">với tài khoản admin</div-->
<div class="login_success">
    <ul class="bokeh">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>
<div class="clicklink"><a href="{data.url}">({LANG.mess_redirect})</a></div>
</body>
</html>
<!-- END: box_redirect -->