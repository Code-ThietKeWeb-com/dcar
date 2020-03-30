<!-- BEGIN: html_street_custom -->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Thêm </title>
    <link href="{DIR_JS}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="{DIR_STYLE}/fonts/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link rel='stylesheet' href='{DIR_JS}/thickbox/thickbox.css' type='text/css' media='all' />
    <link href="{DIR_MOD}/css/popup.css" rel="stylesheet" type="text/css"/>
    <script language="javascript"> var ROOT = "{CONF.rooturl}";  </script>
    <script type="text/javascript" src="{DIR_JS}/jquery.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/jquery-migrate.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/bootstrap/js/bootstrap.min.js"></script>
</head>
<body >

<div class="boxForm">
    {data.err}
    <form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm" class="validate">

        <div class="row">
            <div class="col-md-6 col-xs-6">
                <div class="form-group">
                    <label class="div-label" for="txt_label">Tỉnh thành <span class="font_err">*</span></label>
                    <div class="div-input">
                        <select id="city" name="city" class="form-control load_state" data-state="state">{data.option_city}</select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="div-label" for="txt_value">Quận huyện <span class="font_err">*</span></label>
                    <div class="div-input">
                        <select id="state" name="state" class="form-control"  data-street="street">{data.option_state}</select>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xs-6">
                <div class="form-group">
                    <label class="div-label" for="txt_label">Tiêu đề <span class="font_err">*</span></label>
                    <div class="div-input">
                        <input name="name" id="name" type="text" size="60" maxlength="250" class="form-control" value="{data.name}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="div-label" for="txt_value">Giá trị (VNĐ)<span class="font_err">*</span></label>
                    <div class="div-input">
                        <input name="price" id="price" type="text" size="60" maxlength="250" class="form-control number_format" value="{data.price}" />
                    </div>
                </div>
            </div>
        </div>


        <div class="div-button">
            <button id="do_submit" name="do_submit" type="submit" class="btn btn-primary" value="Submit"><span>Submit</span></button>
        </div>
<label

    </form>
</div>
<script type="text/javascript" src="{DIR_JS}/number/jquery.number.min.js"></script>
<script type='text/javascript'>

  $(document).ready(function () {

    $(".load_state").change(function() {
      var ext_display = $(this).attr("data-state");

      var mydata =  "do=option_state&city="+ $(this).val();
      $.ajax({
        type: "GET",
        url: ROOT+'load_ajax.php',
        data: mydata,
        success: function(html){
          $("#"+ext_display).html(html);
        }
      });
    });


    $('.number_format').number(  true ,0 );
  });



  <!-- BEGIN: html_sucess -->
  parent.vnTLandPrice.callBack_eModal({data.id},'{data.mess}');
  <!-- END: html_sucess -->
</script>



  </body>
</html>
<!-- END: html_street_custom -->



