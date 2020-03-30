<!-- BEGIN: body_tpl -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, maximum-scale=1, user-scalable=yes" />
    <title>Admin Lost Pass - TRUST.vn CMS</title>
    <meta name="description" CONTENT="Hệ thống quản lý nội dung TRUST.vn CMS do công ty TRUST.vn nghiên cứu, thiết kế, lập trình, cập nhật. Đã đăng ký sở hữu trí tuệ ®"/>
    <link rel="SHORTCUT ICON" href="vntrust.ico" type="image/x-icon" />
    <link href="{DIR_STYLE}/fonts/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="{DIR_JS}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{DIR_STYLE}/login.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="{DIR_JS}/jquery.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/jquery-migrate.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/jquery_plugins/jquery.validate.js"></script>
    <script type="text/javascript">


      $(document).ready(function(){
        $(".text-input").focus(function(){
          $(this).parent().addClass("visible");
        });
        $(".text-input").blur(function(){
          $(this).parent().removeClass("visible");
        });


        var validator = $("#formLogin").validate({

        });

      });
    </script>

</head>

<body>
<div class="blurFixed"></div>
<div class="formLogin">
    <div class="lLogo"><img  src="http://www.thietkeweb.com/backlink/trustvn-logo.svg" onerror="this.onerror=null; this.src='http://www.thietkeweb.com/backlink/trustvn-logo.png'" alt="TRUST.vn" title="TRUST.vn" width="100%" /></div>
     {form_content}
    <div class="rowInput">
        <div style="text-align: right;">
            <a href="http://www.thietkeweb.com" target="_blank" title="thiet ke web" rel="dofollow" class="thietkeweb">Thiết kế web :</a>
            <a href="http://www.trust.vn" target="_blank" rel="dofollow"><img src="http://www.thietkeweb.com/backlink/trustvn-logo.svg"  onerror="this.onerror=null; this.src='http://www.thietkeweb.com/backlink/trustvn-logo.png'" title="TRUST.vn" alt="TRUST.vn" width="65" /></a>
        </div>
    </div>
</div>

</body>
</html>
<!-- END: body_tpl -->

<!-- BEGIN: reset_pass -->

<script>
  $(document).ready(function() {

    // validate signup form on keyup and submit
    $.validator.addMethod( "alphanumeric", function( value, element ) {
      return this.optional( element ) || /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=])[^\s]{6,}$/mg.test( value );
    }, "{LANG.err_password_invalid}" );


    var validator = $("#formLogin").validate({
      rules: {

        new_pass: {
          required: true,
          alphanumeric:true
        },
        re_new_pass: {
          required: true,
          equalTo: "#new_pass"
        }
      },
      messages: {

        new_pass: {
          required: "{LANG.err_empty_input}"
        },

        re_new_pass: {
          required: "{LANG.err_empty_input}",
          equalTo: "{LANG.err_re_password_incorrect}"
        }
      }

    });


  });


</script>

    <form id="formLogin" name="formLogin" action="{data.link_action}" method="POST" class="validate">
        <div class="form-title">RESET MẬT KHẨU</div>
        {data.err}
        <div class="rowInput">
            <div class="divPassword">
                <input type="password" name="new_pass" autocomplete="off" id="new_pass" class="text-input form-control password" value="" placeholder="{LANG.new_password}" readonly="readonly" onfocus="this.removeAttribute('readonly')"/>
                <div class="divIcon"></div>
            </div>
        </div>
        <div class="rowInput">
            <div class="divPassword">
                <input type="password" name="re_new_pass" autocomplete="off" id="re_new_pass" class="text-input form-control password" value="" placeholder="{LANG.re_new_password}" readonly="readonly" onfocus="this.removeAttribute('readonly')"/>
                <div class="divIcon"></div>
            </div>
        </div>
        <div class="rowInput">
            <div class="divPassword">
                <div class="div-recaptcha">
                    <script src="https://www.google.com/recaptcha/api.js?hl=vi"></script>
                    <div class="g-recaptcha" data-sitekey="{CONF.reCAPTCHA_site_key}"></div>
                </div>
            </div>
        </div>

        <div class="rowInput">
            <button type="submit" name="do_submit" id="do_submit" class="btn button" value="{LANG.btn_lostpass}"><span>{LANG.btn_lostpass}</span></button>
        </div>

    </form>

<!-- END: reset_pass -->



<!-- BEGIN: lostpass -->



    <form id="formLogin" action="?act=lostpass" method="POST">
        <div class="form-title">QUÊN MẬT KHẨU</div>
{data.err}
        <div class="rowInput">
            <div class="divUsername">
                <input type="text" name="txtUsername" autocomplete="off" id="txtUsername" class="text-input username" value="" placeholder="{LANG.username}"/>
                <div class="divIcon"></div>
            </div>
        </div>
        <div class="rowInput">
            <div class="divEmail">
                <input type="text" name="txtEmail" autocomplete="off"   id="txtEmail" class="text-input email" value="" placeholder="Email" />
                <div class="divIcon"></div>
            </div>
        </div>

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
                        <input type="text" name="security_code"  id="security_code" class="text-input security_code" value="" placeholder="Mã xác nhận"  autocomplete="off"/>
                        <div class="divIcon"></div>
                    </div>
                </div>
                <div class="captcha-img">
                    <img src="{captcha.ver_img}" alt="Mã xác nhận">
                </div>

            </div>


        </div>
        <!-- END: html_captcha -->



        <div class="rowInput">
            <button type="submit" name="do_submit" id="do_submit" class="btn button" value="{LANG.btn_lostpass}"><span>{LANG.btn_lostpass}</span></button>
        </div>

    </form>

<!-- END: lostpass -->
