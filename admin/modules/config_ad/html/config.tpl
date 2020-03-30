<!-- BEGIN: manage -->
<br/>
<div id="tabs">
    <ul>
        <li><a href="#tabConfig"><span>{LANG.config_website}</span></a></li>
        <li><a href="#tabSystem"><span>{LANG.config_system}</span></a></li>
        <li><a href="#tabMenuAamin"><span>{LANG.config_menu_admin}</span></a></li>
        <li><a href="#tabRobots"><span>File robots.txt</span></a></li>
        <li><a href="#tabPhpinfo"><span>{LANG.manage_phpinfo}</span></a></li>
        <li><a href="#tabWebClose"><span>{LANG.manage_web_close}</span></a></li>
    </ul>
    <div id="tabConfig">
        <form action="{data.link_action}" method="post" name="f_config" id="f_config">
            {data.err}

            <table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable">

                <tr height=20 class="row_title">
                    <td colspan="2"><strong>{LANG.website_setting}</strong></td>
                </tr>

                <tr>
                    <td align="right" class="row1"><strong>{LANG.charset} : </strong></td>
                    <td align="left" class="row0"><input name="cot[charset]" type="text" size="50" maxlength="250"
                                                         value="{data.charset}"></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>Favicon : </strong></td>
                    <td align="left" class="row0">
                        <div class="input-group" style="max-width: 500px;">
                            <input type="text" name="cot[favicon]"  id="favicon"    class="form-control"  value="{data.favicon}" />
                            <div class="input-group-btn"><button type="button" class="button btnBrowseMedia" value="Browse server" data-obj="favicon" data-mod="" data-folder="File/Image" data-type="file" ><span class="img">Image</span></button></div>
                        </div>

                    </td>
                </tr>
                <tr>
                    <td align="right" class="row1" width="30%"><strong>{LANG.skin} : </strong></td>
                    <td align="left" class="row0">{data.list_skin}</td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>{LANG.module_default} : </strong></td>
                    <td align="left" class="row0">{data.list_module}</td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>{LANG.default_wysiwyg} : </strong></td>
                    <td align="left" class="row0">{data.list_editor}</td>
                </tr>



                <tr>
                    <td align="right" class="row1"><strong>{LANG.record} : </strong></td>
                    <td align="left" class="row0"><input name="cot[record]" type="text" size="20" maxlength="250" value="{data.record}"></td>
                </tr>


                <tr>
                    <td align="right" class="row1"><strong>{LANG.email} :</strong></td>
                    <td align="left" class="row0"><input name="cot[email]" type="text" size="50" maxlength="250"
                                                         value="{data.email}"/></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>Hotline :</strong></td>
                    <td align="left" class="row0"><input name="cot[hotline]" type="text" size="20" maxlength="250"
                                                         value="{data.hotline}"/></td>
                </tr>

                <tr>
                    <td align="right" class="row1"><strong>Google Maps API Key :</strong></td>
                    <td align="left" class="row0"><input name="cot[GoogleMapsAPIKey]" type="text" size="70"  maxlength="250" value="{data.GoogleMapsAPIKey}" style="width:95%"/></td>
                </tr>


                <tr  class="row_title">
                    <td colspan="2"><strong>Google reCAPTCHA</strong></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>reCAPTCHA Api_url :</strong></td>
                    <td align="left" class="row0"><input name="cot[reCAPTCHA_api_url]" type="text" size="70" maxlength="250" value="{data.reCAPTCHA_api_url}" style="width:95%"/></td>
                </tr>

                <tr>
                    <td align="right" class="row1"><strong>reCAPTCHA Site key :</strong></td>
                    <td align="left" class="row0"><input name="cot[reCAPTCHA_site_key]" type="text" size="70" maxlength="250" value="{data.reCAPTCHA_site_key}"  style="width:95%"/></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>reCAPTCHA Secret key :</strong></td>
                    <td align="left" class="row0"><input name="cot[reCAPTCHA_secret_key]" type="text" size="70"  maxlength="250" value="{data.reCAPTCHA_secret_key}"  style="width:95%"/></td>
                </tr>


                <tr height=20 class="row_title">
                    <td colspan="2"><strong>{LANG.counter_setting}</strong></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>{LANG.counter} :</strong></td>
                    <td align="left" class="row0">{data.list_counter}</td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>{LANG.default_counter} :</strong></td>
                    <td align="left" class="row0"><input name="cot[counter_default]" type="text" size="20"
                                                         maxlength="250" value="{data.counter_default}"/></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>{LANG.random_online} :</strong></td>
                    <td align="left" class="row0"><input name="cot[random_online]" type="text" size="20" maxlength="250"
                                                         value="{data.random_online}"/></td>
                </tr>
                <tr  >
                    <td  align="right" class="row1" > <strong>Script mở rộng ở đầu trang <span class="font_err">( head )</span>:</strong><br><span class="font_small" style="font-weight:normal;">VD : Google AdWords , Global site tag</span></td>
                    <td align="left" class="row0" ><textarea name="cot[extra_header]" rows="5" cols="50" style="width:95%">{data.extra_header}</textarea> </td>
                </tr>

                <tr  >
                    <td  align="right" class="row1" > <strong>Script mở rộng ở đầu nội dung <span class="font_err"> ( body )</span>:</strong><br><span class="font_small" style="font-weight:normal;">VD : Google Tag Manager (noscript) </span></td>
                    <td align="left" class="row0" ><textarea name="cot[extra_body]" rows="5" cols="50" style="width:95%">{data.extra_body}</textarea> </td>
                </tr>

                <tr>
                    <td align="right" class="row1"><strong>{LANG.extra_footer} :</strong><br><span class="font_small"
                                                                                                   style="font-weight:normal;">{LANG.note_extra_footer}</span>
                    </td>
                    <td align="left" class="row0"><textarea name="cot[extra_footer]" rows="5" cols="50"
                                                            style="width:95%">{data.extra_footer}</textarea></td>
                </tr>


                <tr>
                    <td class="row1">&nbsp;</td>
                    <td class="row0">
                        <input type="hidden" name="num" value="{data.num}">
                        <input type="hidden" name="csrf_token" value="{data.csrf_token}" />
                        <input type="hidden" name="do_submit" value="1">
                        <input type="submit" name="btnEdit" value="Update >>" class="button"></td>
                </tr>
            </table>
        </form>
    </div>
    <div id="tabSystem">
        <form action="{data.link_action}" method="post" name="f_config" id="f_config">
            <table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable">


                <tr  class="row_title">
                    <td colspan="2"><strong>Thiết lập an ninh</strong></td>
                </tr>
                <tr>
                    <td align="right" class="row1" width="30%">Kích hoạt chức năng chặn đăng nhập sai nhiều lần: </strong></td>
                    <td align="left" class="row0">{data.list_login_attempt}</td>
                </tr>

                <tr>
                    <td align="right" class="row1"><strong>Sai tối đa : </strong></td>
                    <td align="left" class="row0"><input name="cot[login_attempt_num]" type="text" size="50"   value="{data.login_attempt_num}"></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>Thời gian theo dõi : </strong></td>
                    <td align="left" class="row0"><input name="cot[login_attempt_time]" type="text" size="50"  value="{data.login_attempt_time}"> phút</td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>Thời gian bị cấm đăng nhập : </strong></td>
                    <td align="left" class="row0"><input name="cot[login_attempt_time_ban]" type="text" size="50"  value="{data.login_attempt_time_ban}"> phút</td>
                </tr>


                <tr>
                    <td align="right" class="row1">Hiển thị captcha login Admin: </strong></td>
                    <td align="left" class="row0">{data.list_captcha_admin}</td>
                </tr>

                <tr>
                    <td align="right" class="row1"><strong>Loại captcha: </strong></td>
                    <td align="left" class="row0">{data.list_captcha_type}</td>
                </tr>




                <tr   class="row_title">
                    <td colspan="2"><strong>Cache Engine</strong></td>
                </tr>
                <tr>
                    <td align="right" class="row1" width="30%"><strong>{LANG.cache} : </strong></td>
                    <td align="left" class="row0">{data.list_cache}</td>
                </tr>

                <tr>
                    <td align="right" class="row1"><strong>{LANG.day_del_adminlog} : </strong></td>
                    <td align="left" class="row0"><input name="cot[day_del_adminlog]" type="text" size="50"
                                                         maxlength="250" value="{data.day_del_adminlog}"></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>{LANG.day_del_counter} : </strong></td>
                    <td align="left" class="row0"><input name="cot[day_del_counter]" type="text" size="50"
                                                         maxlength="250" value="{data.day_del_counter}"></td>
                </tr>


                <tr height=20 class="row_title">
                    <td colspan="2"><strong>SMTP Information</strong></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>{LANG.method_email}</strong> :</td>
                    <td align="left" class="row0">{data.list_method_email}</td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>{LANG.smtp_host} :</strong></td>
                    <td align="left" class="row0"><input name="cot[smtp_host]" type="text" size="50" maxlength="250"
                                                         value="{data.smtp_host}" id="smtp_host"/></td>
                </tr>

                <tr>
                    <td align="right" class="row1"><strong>{LANG.smtp_type_encryption} :</strong></td>
                    <td align="left" class="row0">{data.list_smtp_type_encryption}</td>
                </tr>


                <tr>
                    <td align="right" class="row1"><strong>{LANG.smtp_port} :</strong></td>
                    <td align="left" class="row0"><input name="cot[smtp_port]" type="text" size="50" maxlength="250"
                                                         value="{data.smtp_port}" id="smtp_port"/></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>{LANG.smtp_autentication} :</strong></td>
                    <td align="left" class="row0">{data.list_smtp_autentication}</td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong> {LANG.smtp_username} :</strong></td>
                    <td align="left" class="row0">
                        <input name="cot[smtp_username]" type="text" size="50" maxlength="250" value="{data.smtp_username}" />
                         <span id="ext_isFrom" style="display: inline-block; margin-left: 5px;">| <b>{LANG.smtp_from}</b> {data.list_smtp_from}</span>
                    </td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>{LANG.smtp_password}:</strong></td>
                    <td align="left" class="row0"><input name="cot[smtp_password]" type="password" size="50"
                                                         maxlength="250" value="{data.smtp_password}"/></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong>From Name</strong> :</td>
                    <td align="left" class="row0"><input name="cot[from_name]" type="text" size="50" maxlength="250"  value="{data.from_name}"/></td>
                </tr>

                <tr align="center">

                    <td align="right" class="row1">&nbsp;</td>
                    <td class="row0">
                        <input type="hidden" name="num" value="{data.num}">
                        <input type="hidden" name="csrf_token" value="{data.csrf_token}" />
                        <input type="hidden" name="do_submit" value="1">
                        <input type="submit" name="btnEdit" value="Update >>" class="button"></td>
                </tr>
            </table>

            <script>
              jQuery(document).ready(function ($) {
                $("#method_email").change(function () {
                  switch ($(this).val()) {
                    case "mail"     :
                      $("#smtp_port").val("25");
                      $("#smtp_type_encryption").val("none");
                      break;
                    case "smtp"     :
                      $("#smtp_port").val("25");
                      $("#smtp_type_encryption").val("none");
                      break;
                    case "gmail" :
                      $("#smtp_host").val("smtp.gmail.com");
                      $("#smtp_port").val("465");
                      $("#smtp_type_encryption").val("ssl");
                      break;

                  }
                });
                $("#smtp_type_encryption").change(function () {
                  switch ($(this).val()) {
                    case "none"     :
                      $("#smtp_port").val("25");
                      break;
                    case "ssl"     :
                      $("#smtp_port").val("465");
                      break;
                    case "tls" :
                      $("#smtp_port").val("587");
                      break;

                  }
                });


              });
            </script>
        </form>
    </div>

    <div id="tabMenuAamin">
        {data.menu_admin}
    </div>


    <div id="tabRobots">
        {data.err_robots}
        <form action="{data.link_robots}" method="post" name="myform" id="myform" onsubmit="return checkform(this);">
            <table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable">
                <tr>
                    <td class="row0"><b>Cập nhật file robots.txt </b></td>
                </tr>
                <tr>
                    <td class="row0" align="center"><textarea name="robots" id="robots" cols="60" rows="20"
                                                              style="width: 99%">{data.robots}</textarea></td>
                </tr>

                <tr>
                    <td class="row0" align="center">
                        <input type="submit" name="do_submit" value="Submit" class="button"/>
                        <input type="hidden" name="csrf_token" value="{data.csrf_token}" />
                        <input type="reset" name="Submit22" value="Reset" class="button"/>
                    </td>

                </tr>
            </table>
        </form>


    </div>


    <div id="tabPhpinfo">

        <style>
            .phpInfo {
                height: 500px;
                overflow-x: hidden;
                overflow-y: auto;
            }


        </style>
        <div class="phpInfo">
        {data.phpinfo}
        </div>
    </div>

    <div id="tabWebClose">
        {data.err}
        <form action="{data.link_action}" method="post" name="myform" id="myform" onsubmit="return checkform(this);">
            <table width="100%" border="0" cellspacing="1" cellpadding="1" class="admintable">
                <tr>
                    <td align="right" class="row1"><strong> {LANG.status} : </strong></td>
                    <td align="left" class="row0">{data.lis_web_close}</td>
                </tr>

                <tr>
                    <td align="right" class="row1"><strong>{LANG.mess_close_website} :</strong></td>
                    <td align="left" class="row0"><textarea name="cot[web_close_desc]" cols="60"
                                                            rows="10">{data.web_close_desc}</textarea></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong> {LANG.iframe_to_website} : </strong></td>
                    <td align="left" class="row0"><input name="cot[web_iframe]" type="text" value="{data.web_iframe}"
                                                         size="60"/></td>
                </tr>
                <tr>
                    <td align="right" class="row1"><strong> {LANG.redirect_to_website} : </strong></td>
                    <td align="left" class="row0"><input name="cot[web_redirect]" type="text"
                                                         value="{data.web_redirect}" size="60"/></td>
                </tr>

                <tr align="center">
                    <td align="right" class="row1">&nbsp;</td>
                    <td class="row0">
                        <input type="submit" name="do_submit" value="Submit" class="button"/>
                        <input type="hidden" name="csrf_token" value="{data.csrf_token}" />
                        <input type="reset" name="Submit22" value="Reset" class="button"/>
                    </td>

                </tr>
            </table>
        </form>
    </div>

</div>

<br/>
<!-- END: manage -->


<!-- BEGIN: html_menu_admin -->
{data.err}
<form id="manage" name="manage" method="post" action="{data.link_action}">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="bg_tbl">
        <tr>
            <td>
                <table border="0" cellspacing="2" cellpadding="2">
                    <tr>
                        <td width="40" align="center"><img src="{DIR_IMAGE}/arr_top.gif" width="17" height="17"></td>
                        <td>{data.button}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>

                <table cellspacing="1" class="adminlist">
                    <thead>
                    <tr height="25">
                        <th width="5%" align="center"><input type="checkbox" value="all" class="checkbox"
                                                             name="checkall" id="checkall"/></th>
                        <th width="10%" align="center">Thứ tự</th>
                        <th align="center">Tiêu đề</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- BEGIN: html_row -->
                    <tr class="{row.class}" id="{row.row_id}">
                        <td align="center">{row.check_box}</td>
                        <td align="center">{row.order}</td>
                        <td align="left" class="row">{row.title}</td>
                    </tr>
                    <!-- END: html_row -->

                    <!-- BEGIN: html_row_no -->
                    <tr class="row0">
                        <td colspan="7" align="center" class="font_err">{mess}</td>
                    </tr>
                    <!-- END: html_row_no -->
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" cellspacing="2" cellpadding="2">
                    <tr>
                        <td width="40" align="center"><img src="{DIR_IMAGE}/arr_bottom.gif"></td>
                        <td>{data.button}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <input type="hidden" name="csrf_token" value="{data.csrf_token}" />
    <input type="hidden" name="do_action" id="do_action" value="">
</form>

<!-- END: html_menu_admin -->
