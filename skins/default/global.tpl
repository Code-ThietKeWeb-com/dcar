<!-- BEGIN: body --><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{data.meta_lang}" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset={CONF.charset}"/>
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
<title>{CONF.indextitle}</title>
<meta name="keywords" CONTENT="{CONF.meta_keyword}"/>
<meta name="description" CONTENT="{CONF.meta_description}"/>
<meta name="robots" content="index, follow"/>
{CONF.meta_extra}
<link rel="SHORTCUT ICON" href="{CONF.favicon}" type="image/x-icon"/>
<link rel="icon" href="{CONF.favicon}" type="image/gif"/>
<link href="{DIR_JS}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="{DIR_SKIN}/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="{DIR_JS}/fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
<link href="{DIR_JS}/jquery_alerts/jquery.alerts.css" rel="stylesheet" type="text/css" />
<link href="{DIR_JS}/slick/slick.css" rel="stylesheet" type="text/css"/>
<link href="{DIR_JS}/slick/slick-theme.css" rel="stylesheet" type="text/css"/>
<link href="{DIR_STYLE}/screen.css" rel="stylesheet" type="text/css"/>
<script language="javascript"> var ROOT = "{CONF.rooturl}";  var ROOT_MOD = "{data.link_mod}";  var DIR_IMAGE = "{DIR_IMAGE}"; var cmd= "{CONF.cmd}";  var lang = "{data.lang}";  var mem_id = {data.mem_id}; var js_lang = new Array(); js_lang['announce'] ="{LANG.global.announce}"; js_lang['error'] = "{LANG.global.error}"; </script>
<script type="text/javascript" src="{DIR_JS}/global.js?lang={data.lang}"></script>
<script type="text/javascript" src="{DIR_JS}/jquery.min.js"></script>
<script type="text/javascript" src="{DIR_JS}/jquery-migrate.min.js"></script>
<script type="text/javascript" src="{DIR_JS}/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{DIR_JS}/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="{DIR_JS}/jquery_alerts/jquery.alerts.js"></script>
<script type="text/javascript" src="{DIR_JS}/slick/slick.min.js"></script>
<script type="text/javascript" src="{DIR_JS}/core.js?v=6.0"></script>
<script type="text/javascript" src="{DIR_JS}/javascript.js?v=6.0"></script>
{EXT_HEAD}
</head>
<body>
<div id="vnt-wrapper">
    <div id="vnt-container">
        <div id="vnt-header">
            <div class="logo">{data.logo}</div>
            <div class="banner">{data.banner}</div>
            <div class="header-tool">
                <div class="box_lang">{data.box_lang}</div>
                <div class="header_like">{data.social_network.like}</div>
            </div>
            <div class="clear"></div>
        </div>
        <div id="vnt-menutop">
            <div class="menutop"><ul>{data.menu.menutop}</ul></div>
            <div class="header_search">{data.box_search}</div>
            <div class="clear"></div>
        </div>

        <div id="vnt-content">
            {PAGE_CONTENT}
            <div class="clear"></div>
        </div>

        <div id="vnt-footer">
            <div class="menu_footer">{data.menu.footer}</div>
            <div class="copyright">{LANG.global.copyright}<br>[MADEBY]</div>
        </div>

    </div>
    <div id="vnt-social-network">{data.social_network.icon}{data.social_network.share}
        <div class="clear"></div>
    </div>
</div>
{EXT_FOOTER}
</body>
</html>
<!-- END: body -->