<!-- BEGIN: modules -->
<div class="wrapper">
    <div class="vnt-main-top">
        <div></div>
        <div id="vnt-navation" class="breadcrumb">
            <div class="navation">
                {data.navation}
            </div>
        </div>
    </div>
    <div class="wrapCont">{data.main}</div>
</div>
<!-- END: modules -->


<!-- BEGIN: html_contact -->
<script type="text/javascript" src="//www.google.com/jsapi"></script>
<script type="text/javascript" src="//maps.google.com/maps/api/js?key={CONF.GoogleMapsAPIKey}&language=vi"></script>
<script type="text/javascript" src="{DIR_JS}/google/gmaps.js"></script>
<script language=javascript>
  $(document).ready(function() {
    $.validator.addMethod("check_phone", function(value, element) {
      return this.optional(element) || /^[0-9\-.() ]{9,30}$/i.test(value);
    }, "{LANG.global.err_phone_invalid}" );

    var validator = $("#formContact").validate({
      rules: {
        phone: {
          required: true,
          check_phone: true
        },
        email: {
          required: true,
          email: true
        }

      },
      messages: {
        phone: {
          required: "{LANG.contact.mess_empty_phone}"
        },
        email: {
          required: "{LANG.contact.mess_empty_email}",
          email: "{LANG.contact.mess_empty_invalid}"
        }
      },
      errorElement: "em",
      errorPlacement: function ( error, element ) {
        // Add the `help-block` class to the error element
        error.addClass( "help-block" );

        // Add `has-feedback` class to the parent div.form-group
        // in order to add icons to inputs
        element.parents( ".div_input" ).addClass( "has-feedback" );

        // Add the span element, if doesn't exists, and apply the icon classes to it.
        if ( !element.next( "span" )[ 0 ] ) {
          $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
        }
      },
      success: function ( label, element ) {
        // Add the span element, if doesn't exists, and apply the icon classes to it.
        if ( !$( element ).next( "span" )[ 0 ] ) {
          $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
        }
      },
      highlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".div_input" ).addClass( "has-error" ).removeClass( "has-success" );
        $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
      },
      unhighlight: function ( element, errorClass, validClass ) {
        $( element ).parents( ".div_input" ).addClass( "has-success" ).removeClass( "has-error" );
        $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
      },
      submitHandler: function(form) {
        if($(".div-recaptcha").length){
          if (grecaptcha.getResponse() == ''){
            jAlert('{LANG.global.err_recaptcha_empty}',js_lang['error'] );
          }else{
            form.submit();
          }
        }else{
          form.submit();
        }
      }
    });

  });


</script>

<!-- BEGIN: html_item -->
<div class="info-contact">
    <div class="qr">{row.qrcode}</div>
    <div class="over">
        <div class="name">{row.company}</div>
        <ul>
            <li class="fa-home"><span>{LANG.contact.address}:</span>{row.address}</li>
            <!-- BEGIN: phone -->
            <li class="fa-phone"><span>{LANG.contact.phone}:</span><a  href="tel:{row.phone}">{row.phone}</a></li>
            <!-- END: phone -->
            <!-- BEGIN: fax -->
            <li class="fa-fax"><span>Fax:</span>{row.fax}</li>
            <!-- END: fax -->
            <!-- BEGIN: email -->
            <li class="fa-envelope"><span>Email: </span><a  href="mailto:{row.email}">{row.email}</a></li>
            <!-- END: email -->
            <!-- BEGIN: website -->
            <li class="fa-globe"><span>Website: </span> <a  href="{row.website}">{row.website}</a></li>
            <!-- END: website -->
        </ul>
        <div class="view-map-contact"><a href="#map{row.id}"><span>{LANG.contact.view_map}</span></a></div>
    </div>
    <div class="clear"></div>
</div>
<!-- END: html_item -->


<div class="form-contact v2">

    <form id="formContact" name="formContact"  method="post" action="{data.link_action}"  class="form validate">
        {data.err}

        <div class="form-group">
            <div class="textContact">{LANG.contact.note_contact}</div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="form-group div_input">
                    <div class="faForm fa-user">
                        <input type="text" name="name" id="name" class="required"  placeholder="{LANG.contact.full_name} (*)" value="{data.name}"  title="{LANG.contact.mess_empty_name}" />
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="form-group div_input">
                    <div class="faForm fa-home">
                        <input type="text" name="address" id="address" placeholder="{LANG.contact.address}"  value="{data.address}" />
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="form-group div_input">
                    <div class="faForm fa-phone">
                        <input type="text" name="phone" id="phone" class="required" placeholder="{LANG.contact.phone}" value="{data.phone}"  >
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="form-group div_input">
                    <div class="faForm fa-envelope">
                        <input type="text" name="email" id="email" class="required" placeholder="Email" value="{data.email}" >
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group div_input">
                    <div class="faForm fa-home">
                        <input type="text" name="subject" id="subject" class="required" placeholder="{LANG.contact.contact_subject}" value="{data.subject}" title="{LANG.contact.mess_empty_subject}" />
                    </div>
                </div>
                <div class="form-group div_input">
                    <div class="faForm fa-edit">
                        <textarea class="form-control required" id="content" name="content" rows="3"   placeholder="{LANG.contact.contact_content}" title="{LANG.contact.mess_empty_content}">{data.content}</textarea>
                    </div>
                </div>


                <div class="form-group flexDesign">
                    <div class="flexLeft">
                        <!-- BEGIN: html_recaptcha -->
                        <div class="div-recaptcha">
                            <script src="https://www.google.com/recaptcha/api.js?hl=vi"></script>
                            <div class="g-recaptcha" data-sitekey="{CONF.reCAPTCHA_site_key}"></div>
                        </div>
                        <!-- END: html_recaptcha -->

                        <!-- BEGIN: html_captcha -->
                        <label for="security_code">{LANG.contact.security_code} <span>(*)</span></label>
                        <div class="colRight ">
                            <div class="input-group">
                                <div class="faForm fa-shield div_input">
                                    <input type="text" name="security_code" id="security_code" class="form-control required"  title="{LANG.contact.mess_empty_security_code}" maxlength="6"/>
                                </div>
                                <span class="input-group-img"><img class="security_ver" src="{captcha.ver_img}" alt="Code" /></span>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <!-- END: html_captcha -->


                    </div>
                    <div class="flexRight">
                        <div class="gridButton">

                            <div class="col">
                                <input type="hidden" name="do_submit" id="do_submit" value="1" />
                                <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
                                <button name="do_submit" type="submit" value="{LANG.contact.btn_send}"><span>{LANG.contact.btn_send}</span></button>
                            </div>
                            <div class="col">
                                <button name="btnReset" type="reset"><span>{LANG.contact.btn_reset}</span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </form>

</div>




<!-- BEGIN: html_map -->
<div class="map-contact" id="viewmap">
    <div class="mc-tab hidden-lg hidden-md">{row.cur_map_title}</div>
    <ul class="list-tab">
        {row.list_tab}
    </ul>
    <div class="clear"></div>
    <div class="content">
        <div id="ext_maps">{row.maps}</div>
    </div>
</div>
<!-- END: html_map -->

<!-- END: html_contact -->

