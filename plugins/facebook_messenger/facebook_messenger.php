<?php
/*
Plugin Name: facebook_messenger !
*/
$facebook_messenger='' ;
$res_p = $vnT->DB->query("SELECT * FROM plugins where name='facebook_messenger' ");
if($row_p = $vnT->DB->fetch_row($res_p))
{
	$folder = $row_p['folder'];
	$pluginPath = ROOT_URL.'plugins/'.$folder.'/';

	$params = @unserialize($row_p['params']);
	$fanpage_url = ($params['fanpage_url']) ? $params['fanpage_url']  : "https://www.facebook.com/congtyweb/";
	$fb_width = ($params['fb_width']) ? $params['fb_width']  : 310;
	$fb_height = ($params['fb_height']) ? $params['fb_height']  : 310;
	$messenger_text = ($params['messenger_text']) ? $params['messenger_text']  : "Phản hồi của bạn";


	$fb_lang =  ( $vnT->lang_name=="en" ) ? "en_US" : "vi_VN" ;

	$facebook_messenger = '<div id="fb-root"></div>
<script>(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/'.$fb_lang.'/sdk.js#xfbml=1&version=v2.5&appId='.$vnT->setting['facebook_appId'].'";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, \'script\', \'facebook-jssdk\'));</script>';


	if($params['type_show']==1){
		$vnT->html->addStyleSheet($pluginPath . "/css/popup.css");
		$vnT->html->addStyleSheet($pluginPath . "/css/messenger.css");

		$vnT->html->addScript($pluginPath."js/popup.js");
		$vnT->html->addScript($pluginPath."js/jquery.event.move.js");
		$vnT->html->addScript($pluginPath."js/rebound.min.js");

		$facebook_messenger .= '<div class="drag-wrapper drag-wrapper-right">
    		<div data-drag="data-drag" class="thing">
    			<div class="circle facebook-messenger-avatar facebook-messenger-avatar-type0">
    				<img class="facebook-messenger-avatar" src="'.$pluginPath.'images/facebook-messenger.svg" />
    			</div>
    			<div class="content">
    				<div class="inside">
    					<div class="fb-page" data-width="'.$fb_width.'" data-height="'.$fb_height.'" data-href="'.$fanpage_url.'" data-hide-cover="false" data-tabs="messages" data-small-header="true" data-show-facepile="true" data-adapt-container-width="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="'.$messenger_text.'"><a href="'.$messenger_text.'">Loading...</a></blockquote></div></div>
     				</div>
    			</div>
    		</div>
    		<div class="magnet-zone">
    			<div class="magnet"></div>
    		</div>
    	</div>';

		$facebook_messenger .= '<script type="text/javascript" src="'.$pluginPath.'js/facebook_messenger.js"></script>';


	}else{
		$vnT->html->addStyleSheet($pluginPath . "/css/cfacebook.css");
		$vnT->html->addScriptDeclaration('
			jQuery(document).ready(function () {
				jQuery(".chat_fb").click(function() {
					jQuery(".fchat").toggle("slow");
				});
			});
		');
		$vnT->html->addStyleDeclaration("#cfacebook{width:".$fb_width."px;}#cfacebook .fchat{height:".($fb_height+20).";} #cfacebook a.chat_fb{width:".$fb_width."px;}" );
		$facebook_messenger .= '<div id="cfacebook">
    <a href="javascript:void(0);" class="chat_fb" onclick="return:false;"><i class="fa fa-facebook-square"></i> '.$messenger_text.'</a>
    <div class="fchat">
        <div class="fb-page" data-tabs="messages" data-href="'.$fanpage_url.'" data-width="'.$fb_width.'" data-height="'.$fb_height.'" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false" data-show-posts="false"></div>
    </div>
</div>' ;

	}



}

$vnT->conf['extra_footer'] .= $facebook_messenger;
?>