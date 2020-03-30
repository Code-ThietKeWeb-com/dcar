<?php
/*================================================================================*\
|| 							Name code : menu.php 		 			              ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                   ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 31/12/2007 by Thai Son
 **/

if (!defined('IN_vnT')) {
    die('Hacking attempt!');
}


$vnT->menu_display_default = "display:none";

class Menu
{
    var $output = "";

    //================================= box_left ===============================
    function box_left()
    {
        global $DB, $conf, $func, $vnT, $input;

        $vnT->menu_tpl = new XiTemplate(DIR_SKIN . DS . "menu.tpl");
        $vnT->menu_tpl->assign('LANG', $vnT->lang);
        $vnT->menu_tpl->assign("DIR_IMAGE", $vnT->dir_images);
        $vnT->menu_tpl->assign("DIR_STYLE", $vnT->dir_style);
        $vnT->menu_tpl->assign("DIR_JS", $vnT->dir_js);
        $vnT->menu_tpl->assign("CONF", $conf);

        $output = "";
        $output .= $this->box_menu();
        $output .= $this->box_news();

        return $output;
    }

    //=================================  get_menu  ===============================
    function get_menu()
    {
        global $DB, $conf, $func, $vnT, $input;
        $vnT->g_menu = "";
        $str_title = "title_" . $conf['langcp'];
        $str_desc = "description_" . $conf['langcp'];

        $navigation = array();
        $res_g = $DB->query("select * from admin_menu where parentid=0 and display=1 order by displayorder ASC , id ASC ");
        $stt = 0;
        while ($row_g = $DB->fetch_row($res_g)) {
            $navigation[$stt]['m_title'] = $row_g[$str_title];
            $navigation[$stt]['group'] = $row_g['g_name'];
            $navigation[$stt]['description'] = $row_g[$str_desc];

            $res_op = $DB->query("select * from admin_menu where parentid=" . $row_g['id'] . " and g_name='" . $row_g['g_name'] . "' and display=1 order by displayorder ASC , id ASC ");
            $k = 0;
            $arr_option = array();
            while ($row_option = $DB->fetch_row($res_op)) {
                $arr_option[$k]['name'] = $row_option[$str_title];
                $arr_option[$k]['mod'] = $row_option['module'];
                $arr_option[$k]['block'] = $row_option['block'];
                $arr_option[$k]['act'] = $row_option['act'];
                $arr_option[$k]['sub'] = $row_option['sub'];
                $arr_option[$k]['description'] = $row_option[$str_desc];
                $k++;
            }
            $DB->free_result($res_op);

            $navigation[$stt]['option'] = $arr_option;

            $vnT->permission[$row_g['g_name']] = $row_g[$str_title];
            $vnT->g_menu .= "'" . $row_g['g_name'] . "',";

            $stt++;
        }
        $DB->free_result($res_g);

        $vnT->g_menu = substr($vnT->g_menu, 0, -1);

        return $navigation;
    }

    //======================== box_menu ===========================
    function box_menu()
    {
        global $DB, $conf, $func, $vnT, $input;
        $output = "";
        $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
        $vnT->permission_act = array();
        $Menu = $this->get_menu();

        $myPre = array();
        $vnT->myPre_act = array();
        $vnT->myPre_sub = array();
        $arr_permission = unserialize($vnT->admininfo['permission']);
        if (is_array($arr_permission)) {
            $vnT->g_menu = "";
            foreach ($arr_permission as $key => $value) {
                $vnT->g_menu .= "'" . $key . "',";
                $myPre[] = $key;
                $listAct = explode("|", $value);
                foreach ($listAct as $k => $v) {
                    $arr_act = explode("=>", $v);
                    $vnT->myPre_act[] = $arr_act[0];
                    $vnT->myPre_sub[$arr_act[0]] = $arr_act[1];
                }
            }
            $vnT->g_menu = substr($vnT->g_menu, 0, -1);
        }


        $box_menu = "";
        for ($i = 0; $i < count($Menu); $i++) {
            //check quyen
            $data["class"] = '';

            //$myPre = explode(",",$vnT->admininfo['permission']);
            if (in_array($Menu[$i]['group'], $myPre) || $vnT->admininfo['level'] == 0) {

                $style = $vnT->menu_display_default;
                $optionMenu = $Menu[$i]['option'];
                if (is_array($optionMenu)) {

                    $list_menu = "";
                    for ($j = 0; $j < count($optionMenu); $j++) {
                        $smenu = $optionMenu[$j];

                        //echo  "smenu = ".$smenu['act']." <br>";
                        // check act
                        if (in_array($smenu['act'], $vnT->myPre_act) || $vnT->admininfo['level'] == 0) {
                            $option = ($_GET['block']) ? $_GET['block'] : $_GET['mod'];

                            $vnT->permission_act[] = $smenu['act'];

                            // lay current
                            if ((strtolower(trim($option)) == $smenu["mod"])) {
                                $style = "display:";
                                $current = 'class="current"';
                                $data["class"] = 'active';

                                if (!empty($_GET['act']) || $smenu['act']) {
                                    if ($_GET['act'] && $_GET['act'] == $smenu['act']) {
                                        $current = 'class="current"';
                                        if (!empty($_GET['sub']) || $smenu['sub']) {
                                            if ($_GET['sub'] && $_GET['sub'] == $smenu['sub']) {
                                                $current = 'class="current"';
                                            } else {
                                                $current = '';
                                            }
                                        }
                                    } else {
                                        $current = '';
                                    }
                                }

                            } else {
                                $current = '';
                            }

                            // tao link
                            if ($smenu['newlink']) {
                                $link = $smenu['newlink'];
                            } else {
                                if ($smenu['block']) {
                                    $link = "?block=" . trim($smenu['block']) . "&act=" . trim($smenu['act']) . "&lang=" . $lang;
                                } else {
                                    $link = "?mod=" . trim($smenu['mod']) . "&act=" . trim($smenu['act']) . "&lang=" . $lang;
                                }

                            }

                            if ($smenu['newwin']) {
                                $win = "target=\"_blank\"";
                            } else
                                $win = "";

                            // check sub
                            if (!empty($smenu['sub'])) {
                                //echo "sub = ".$smenu['sub']."<br>";
                                $link .= "&sub=" . $smenu['sub'];
                                if (strstr($vnT->myPre_sub[$smenu['act']], $smenu['sub']) || $vnT->admininfo['level'] == 0) {
                                    $list_menu .= "<li id='m-".$smenu['act']."-".$smenu['sub']."' {$current}><a href=\"{$link}\" {$win}  >{$smenu['name']}</a></li>";
                                }
                            } else {
                                if (!strstr($vnT->myPre_sub[$smenu['act']], "manage") && $vnT->admininfo['level'] != 0) {
                                    $arr_sub = explode(",", $vnT->myPre_sub[$smenu['act']]);
                                    $link .= "&sub=" . $arr_sub[0];
                                }
                                $list_menu .= "<li id='m-".$smenu['act']."' {$current} ><a href=\"{$link}\" {$win}  >{$smenu['name']}</a></li>";
                            }

                        }
                    }

                } // end if  optionMenu

                $data['f_title'] = $Menu[$i]['m_title'];
                $data['group'] = $Menu[$i]['group'];
                $data['img'] = ($style == "display:") ? $vnT->dir_images . "/but_tru.gif" : $vnT->dir_images . "/but_cong.gif";

                $data['style'] = $style;
                $data['list_menu'] = $list_menu;


                $vnT->menu_tpl->reset("html_menu_item");
                $vnT->menu_tpl->assign("data", $data);
                $vnT->menu_tpl->parse("html_menu_item");
                $box_menu .= $vnT->menu_tpl->text("html_menu_item");

            } // end if check per


        } // end for

        $data_menu['array_menu'] = $vnT->g_menu;
        $data_menu['box_menu'] = $box_menu;
        $vnT->menu_tpl->assign("data", $data_menu);
        $vnT->menu_tpl->parse("html_box_menu");
        $output = $vnT->menu_tpl->text("html_box_menu");
        return $output;
    }

    //======================== box_news ===========================
    function box_news()
    {
        global $DB, $conf, $func, $vnT, $input;
        $output = "";
        $data['list_news'] = "";

        $vnT->menu_tpl->assign("data", $data);
        $vnT->menu_tpl->parse("html_news");

        return $vnT->menu_tpl->text("html_news");
    }


// end class
}

?>