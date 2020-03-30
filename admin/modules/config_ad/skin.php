<?php
/*================================================================================*\
|| 							Name code : cat_news.php 		 		            	  ||
||  				Copyright @2008 by Thai Son - CMS vnTRUST                     ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 09/01/2009 by Thai Son
 **/
if (!defined('IN_vnT')) {
    die('Hacking attempt!');
}

$vntModule = new vntModule();

class vntModule
{
    var $output = "";
    var $skin = "";
    var $linkUrl = "";
    var $module = "config";
    var $action = "skin";

    /**
     * function vntModule ()
     * Khoi tao
     **/
    function vntModule()
    {
        global $Template, $vnT, $func, $DB, $conf;
        require_once("function_" . $this->module . ".php");
        $this->skin = new XiTemplate(DIR_MODULE . DS . $this->module . "_ad" . DS . 'html' . DS . $this->action . ".tpl");
        $this->skin->assign('LANG', $vnT->lang);
        $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
        $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
        $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;

        switch ($vnT->input['sub']) {
            case 'edit':
                $nd['f_title'] = "Cập nhật giao diện Admin";
                $nd['content'] = $this->do_Edit($lang);
                break;
            default:
                $nd['f_title'] = "Cập nhật giao diện Admin";
                $nd['content'] = $this->do_Edit($lang);
                break;
        }
//        $nd['menu'] = $func->getToolbar_Small($this->module, $this->action, $lang);
        $nd['row_lang'] = $func->html_lang("?mod=" . $this->module . "&act=" . $this->action, $lang);

        $Template->assign("data", $nd);
        $Template->parse("box_main");
        $vnT->output .= $Template->text("box_main");

    }

    function do_Edit($lang)
    {
        global $vnT, $func, $DB, $conf;

        $arr_old = $func->fetchDbConfig();

        if ($vnT->input['do_submit']) {
            $cot = $_POST['cot'];
            $ok = $func->writeDbConfig("config", $cot, $arr_old);
            if ($ok) {
                //xoa cache
                $func->clear_cache();

                $mess = $vnT->lang["edit_success"];
            } else {
                $mess = $vnT->lang["edit_failt"];
            }

            $func->insertlog("Update", $_GET['act'], 1);

            $url = $this->linkUrl;
            $func->html_redirect($url, $mess);
        }


        $text = "";
        $did = $arr_old['skin_acp'];
        $path = $conf['rootpath'] . "admin/skins";
        if ($dir = opendir($path)) {
//            $text .= "<select name=\"cot[skin_acp]\" class='select' >";
            while (false !== ($file = readdir($dir))) {
                if ($file != "index.html" && $file != "." && $file != "..") {
                    $name = 'Version 2016 - 1';
                    if ($file == "default") {
                        $name = 'Giao diện cũ';
                    }
                    if ($did == $file)
                        $text .= "<div class='skin_row'><label><input name='cot[skin_acp]' type='radio' value=\"{$file}\" checked=\"checked\" /> <img src='" . $vnT->dir_images . "/".$file.".jpg'  alt='Duplicate' /> $name</label></div>";
                    else
                        $text .= "<div class='skin_row'><label><input name='cot[skin_acp]' type='radio' value=\"{$file}\" /> <img src='" . $vnT->dir_images . "/".$file.".jpg'  alt='Duplicate' /> $name</label></div>";
                }
            }
//            $text .= "</select>";
        }

        $data['list_skin'] = $text;

        /*assign the array to a template variable*/
        $this->skin->assign('data', $data);

        $this->skin->parse("edit");
        return $this->skin->text("edit");
    }
}
?>
