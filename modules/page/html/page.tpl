<!-- BEGIN: modules -->
<div id="vnt-navation" class="breadcrumb" itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><div class="navation">{data.navation}</div></div>
<section class="mod-content row">
	<div id="vnt-main" class="col-xs-9">{data.main}</div>
  <aside id="vnt-sidebar" class="col-xs-3">{data.box_sidebar}</aside>  
</section> 
<!-- END: modules -->

<!-- BEGIN: html_popup --> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{CONF.indextitle} {CONF.extra_title}</title>
<meta name="robots" content="index, follow"/>
<meta name="author" content="{CONF.indextitle}"/>
<meta name="description" CONTENT="{CONF.meta_description}" />
<meta name="keywords" CONTENT="{CONF.meta_keyword}" />
<link rel="SHORTCUT ICON" href="{CONF.rooturl}favicon.ico" type="image/x-icon" />
<link href="{DIR_MOD}/css/popup.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div style="padding:10px;">
{data.content}
</div>
</body>
</html>
<!-- END: html_popup --> 

<!-- BEGIN: html_sitemap --> 
{data.content}
<!-- END: html_sitemap --> 