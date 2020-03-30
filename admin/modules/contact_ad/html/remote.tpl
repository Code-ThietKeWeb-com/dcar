<!-- BEGIN: html_payment_form -->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Thêm hành trình tour</title>
    <link href="{DIR_JS}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link rel='stylesheet' href='{DIR_JS}/thickbox/thickbox.css' type='text/css' media='all' />
    <link href="{DIR_MOD}/css/popup.css?v=1.0" rel="stylesheet" type="text/css"/>
    <script language="javascript"> var ROOT = "{CONF.rooturl}";  </script>
    <script type="text/javascript" src="{DIR_JS}/jquery.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/jquery-migrate.min.js"></script>
    <script type='text/javascript' src='{DIR_JS}/thickbox/thickbox.js?v=3.1'></script>
    <script type="text/javascript" src="{DIR_JS}/bootstrap/js/bootstrap.min.js"></script>
    <script type='text/javascript' src='{DIR_MOD}/js/popup.js?v=1.0'></script>
</head>
<body   >

<div class="boxForm">
    {data.err}
    <form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm" class="validate">
        <div class="form-group">
            <label class="div-label" for="title">{LANG.title} <span class="font_err">*</span></label>
            <div class="div-input">
                <input name="title" id="v" type="text" class="form-control" required value="{data.title}" />
            </div>
        </div>
        <div class="form-group">
            <label class="div-label" for="title">Nội dung</label>
            <div class="div-input">
                <textarea name="content" id="content" class="ckeditor" cols="50" rows="20" style="width:100%; height:250px;" >{data.content}</textarea>
            </div>
        </div>


        <div class="div-button">
            <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
            <button id="do_submit" name="do_submit" type="submit" class="btn btn-primary" value="Submit"><span>Submit</span></button>
        </div>


    </form>
</div>


<script type="text/javascript" src="{CONF.rooturl}plugins/editors/ckeditor/ckeditor.js"></script>
<script type='text/javascript'>
  <!-- BEGIN: html_sucess -->
  parent.vnTAbout.callBackInfo({data.id},'{data.mess}');
  <!-- END: html_sucess -->
  $( 'textarea.ckeditor').each( function() {
    CKEDITOR.replace( $(this).attr('id') ,
      {
        language : 'vi',
        allowedContent: true,
        toolbar : 'Default',
        autoParagraph :false,
        filebrowserBrowseUrl : ROOT+'admin/?mod=media&act=popup_media&module={data.module}&folder={data.module}&type=file',
        filebrowserImageBrowseUrl : ROOT+'admin/?mod=media&act=popup_media&stype=editor&module={data.module}&folder={data.module}&type=image',
        filebrowserFlashBrowseUrl : ROOT+'admin/?mod=media&act=popup_media&module={data.module}&folder={data.module}&type=flash',
        height : '250px',
        width : '100%'
      });
  });

</script>
</body>
</html>
<!-- END: html_payment_form -->

<!-- BEGIN: html_info_form -->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>Thêm hành trình tour</title>
    <link href="{DIR_JS}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link rel='stylesheet' href='{DIR_JS}/thickbox/thickbox.css' type='text/css' media='all' />
    <link href="{DIR_MOD}/css/popup.css?v=1.0" rel="stylesheet" type="text/css"/>
    <script language="javascript"> var ROOT = "{CONF.rooturl}";  </script>
    <script type="text/javascript" src="{DIR_JS}/jquery.min.js"></script>
    <script type="text/javascript" src="{DIR_JS}/jquery-migrate.min.js"></script>
    <script type='text/javascript' src='{DIR_JS}/thickbox/thickbox.js?v=3.1'></script>
    <script type="text/javascript" src="{DIR_JS}/bootstrap/js/bootstrap.min.js"></script>
    <script type='text/javascript' src='{DIR_MOD}/js/popup.js?v=1.0'></script>
</head>
<body   >


<div class="boxForm">
    {data.err}
    <form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm" class="validate">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="div-label" for="title">{LANG.title} <span class="font_err">*</span></label>
                    <div class="div-input">
                        <input name="title" id="v" type="text" class="form-control" required value="{data.title}" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="div-label" for="title">Hình ảnh</label>
                    <div class="div-input">
                        <div id="ext_picture" class="picture" >{data.pic}</div>
                        <input type="hidden" name="picture" id="picture" value="{data.picture}" />
                        <div id="btnU_picture" class="div_upload" {data.style_upload} ><a  class="button thickbox" id="add_image" href="{data.link_upload}"><span class="img"><i class="fa fa-image"></i> Chọn hình</span></a></div>

                    </div>
                </div>



            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label class="div-label" for="title">Nội dung</label>
                    <div class="div-input">
                        <textarea name="description" id="description" class="ckeditor" cols="50" rows="20" style="width:100%; height:250px;" >{data.description}</textarea>
                    </div>
                </div>

            </div>
        </div>

        <div class="div-button">
            <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
            <button id="do_submit" name="do_submit" type="submit" class="btn btn-primary" value="Submit"><span>Submit</span></button>
        </div>


    </form>
</div>


<script type="text/javascript" src="{CONF.rooturl}plugins/editors/ckeditor/ckeditor.js"></script>
<script type='text/javascript'>
  <!-- BEGIN: html_sucess -->
  parent.vnTAbout.callBackInfo({data.id},'{data.mess}');
  <!-- END: html_sucess -->
$( 'textarea.ckeditor').each( function() {
CKEDITOR.replace( $(this).attr('id') ,
  {
    language : 'vi',
    allowedContent: true,
    toolbar : 'Default',
    autoParagraph :false,
    filebrowserBrowseUrl : ROOT+'admin/?mod=media&act=popup_media&module={data.module}&folder={data.module}&type=file',
    filebrowserImageBrowseUrl : ROOT+'admin/?mod=media&act=popup_media&stype=editor&module={data.module}&folder={data.module}&type=image',
    filebrowserFlashBrowseUrl : ROOT+'admin/?mod=media&act=popup_media&module={data.module}&folder={data.module}&type=flash',
    height : '250px',
    width : '100%'
  });
});

</script>
  </body>
</html>
<!-- END: html_info_form -->