<!-- BEGIN: login --><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, maximum-scale=1, user-scalable=yes"/>
    <title>Admin login - TRUST.vn CMS</title>
    <meta name="description" content="Hệ thống quản lý nội dung TRUST.vn CMS do công ty TRUST.vn nghiên cứu, thiết kế, lập trình, cập nhật. Đã đăng ký sở hữu trí tuệ ®"/>
    <link rel="SHORTCUT ICON" href="vntrust.ico" type="image/x-icon"/>
    <link href="{DIR_STYLE}/fonts/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="{DIR_JS}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="{DIR_STYLE}/animate.css" rel="stylesheet" type="text/css"/>
    <link href="{DIR_STYLE}/login.css" rel="stylesheet" type="text/css"/>
    <link href="{DIR_JS}/selectMaster/selectordie.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="{DIR_JS}/jquery.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/jquery-migrate.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/bootstrap/dialog/js/bootstrap-dialog.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/jquery_plugins/jquery.validate.js"></script>
    <script type="text/javascript" src="{DIR_JS}/selectMaster/selectordie.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/jquery.jrumble.1.3.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {

        $(".text-input").focus(function () {
          $(this).parent().addClass("visible");
        });
        $(".text-input").blur(function () {
          $(this).parent().removeClass("visible");
        });
        $("form input:checkbox").each(function () {
          if ($(this).is(':checked')) {
            $(this).parents(".checkbox").addClass("checked");
          }
        });
        $("form input:checkbox").change(function () {
          if ($(this).is(':checked')) {
            $(this).parents(".checkbox").addClass("checked");
            return false;
          } else {
            $(this).parents(".checkbox").removeClass("checked");
            return false;
          }
        });

        $(window).load(function () {
          setTimeout(function () {
            $(".formLogin").removeClass("zoomIn");
          }, 1000);
        });

        var validator = $("#formLogin").validate({

          showErrors: function (errorMap, errorList) {
            var $size = this.errorList.length;
            for (var i = 0; i < $size; i++) {
              var c_element = $(this.errorList[i].element).parent();
              c_element.jrumble({
                x: 1,
                y: 0,
                rotation: 0,
                speed: 50
              });
              c_element.trigger('startRumble');
            }

            setTimeout(function () {
              $(".rowInput div").trigger('stopRumble');
            }, 500);

            this.defaultShowErrors();
          },
          invalidHandler: function () {

          },
          rules: {
            txtUsername: {
              required: true
            },
            txtPassword: {
              required: true
            },
            "txtPassSec": {
              required: true
            }
          }
        });

        $("#langcp").selectOrDie({
          placeholder: "Ngôn ngữ / Language",
          customClass: "selectLang"
        }).change(function () {
          changeLang($(this).val());
        });

        function changeLang(lang) {
          var arrLang = {
            'vn': {
              'PassSec': 'Mật khẩu bảo vệ',
              'Password': 'Mật khẩu',
              'Username': 'Tên đăng nhập',
              'remember': 'Lưu nhớ ?',
              'fogotPass': 'Quên mật khẩu ?',
              'login': 'Đăng nhập',
              'webDesign': 'Thiết kế web :',
              'txtClose': 'Đóng',
              'txtAlert': 'Thông báo'
            },
            'en': {
              'PassSec': 'Secret code',
              'Password': 'Password',
              'Username': 'Username',
              'remember': 'Remember ?',
              'fogotPass': 'Fogot password ?',
              'login': 'Login',
              'webDesign': 'Web design :',
              'txtClose': 'Close',
              'txtAlert': 'Alert'
            }
          };

          switch (lang) {
            case "en": {
              getValue(arrLang.en);
              cur_lang = arrLang.en;
            }
              break;
            default: {
              getValue(arrLang.vn);
              cur_lang = arrLang.vn;
            }
          }
        }

        function getValue(e) {
          $("#txtPassSec").attr("placeholder", e.PassSec);
          $("#txtPassword").attr("placeholder", e.Password);
          $("#txtUsername").attr("placeholder", e.Username);
          $("#do_submit").val(e.login);
          $(".rememberPass").html(e.remember);
          $(".forget").html(e.fogotPass);
          $("#do_submit span").html(e.login);
          $(".thietkeweb").html(e.webDesign);
        }

      });

    </script>
</head>

<body>
<div class="blurFixed"></div>
<div class="formLogin animated {data.zoom}">
    <div class="lLogo"><img src="http://www.thietkeweb.com/backlink/trustvn-logo.svg"  onerror="this.onerror=null; this.src='http://www.thietkeweb.com/backlink/trustvn-logo.png'"  alt="TRUST.vn" title="TRUST.vn" width="100%"/></div>
    <form id="formLogin" action="?act=login&ref={data.ref}" method="POST">
        <div class="login-err">{data.err}</div>
        <div class="rowInput">
            <div class="search-select dropdown">
                {data.list_lang}
            </div>
        </div>
        <div class="rowInput">
            <div class="divUsername">
                <input type="text" name="txtUsername" autocomplete="off" id="txtUsername" class="text-input username" value="" placeholder="{LANG.username}"/>
                <div class="divIcon"></div>
            </div>
        </div>
        <div class="rowInput">
            <div class="divPassword">
                <input type="password" name="txtPassword" autocomplete="off" id="txtPassword"  class="text-input password" value="" placeholder="{LANG.password}" readonly="readonly" onfocus="this.removeAttribute('readonly')"/>
                <div class="divIcon"></div>
            </div>
        </div>

        <!-- BEGIN: html_pass_sec -->
        <div class="rowInput">
            <div class="divPassword">
                <input type="password" name="txtPassSec" autocomplete="off" id="txtPassSec" class="text-input password"  value="" placeholder="{LANG.sec_password}"/>
                <div class="divIcon"></div>
            </div>
        </div>
        <!-- END: html_pass_sec -->

        <!-- BEGIN: html_recaptcha -->
        <div class="rowInput">
            <div class="divCaptcha">
                <div class="div-recaptcha">
                    <script src="https://www.google.com/recaptcha/api.js?hl=vi"></script>
                    <div class="g-recaptcha" data-sitekey="{captcha.reCAPTCHA_site_key}"></div>
                </div>
            </div>
        </div>
        <!-- END: html_recaptcha -->

        <!-- BEGIN: html_captcha -->
        <div class="rowInput">
            <div class="divCaptcha">
                <div class="captcha-input">
                    <div class="divUsername">
                        <input type="text" name="security_code" id="security_code" class="text-input security_code" value="" placeholder="Mã xác nhận" autocomplete="off"/>
                        <div class="divIcon"></div>
                    </div>
                </div>
                <div class="captcha-img">
                    <img src="{captcha.ver_img}" alt="Mã xác nhận" />
                </div>
            </div>
        </div>
        <!-- END: html_captcha -->

        <div class="rowInput">
            <div class="fl"><label><span class="checkbox"><input type="checkbox" name="ck_remember"/></span><span  class="rememberPass">{LANG.remember_pass}</span></label></div>
            <div class="fr"><a class="forget" href="?act=lostpass">{LANG.lostpass}</a></div>
            <div class="clear"></div>
        </div>
        <div class="rowInput"> <button type="submit" name="btnLogin" id="do_submit" class="btn button" value="{LANG.login}"><span>{LANG.login}</span></button>
        </div>
        <div class="rowInput">
            <div style="text-align: right;">
                <a href="http://www.thietkeweb.com" target="_blank" title="thiet ke web" rel="dofollow" class="thietkeweb">Thiết kế web :</a>
                <a href="http://www.trust.vn" target="_blank" rel="dofollow"><img src="http://www.thietkeweb.com/backlink/trustvn-logo.svg" onerror="this.onerror=null; this.src='http://www.thietkeweb.com/backlink/trustvn-logo.png'" title="TRUST.vn" alt="TRUST.vn" width="65"/></a>
            </div>
        </div>
    </form>
</div>

</body>
</html>
<!-- END: login -->