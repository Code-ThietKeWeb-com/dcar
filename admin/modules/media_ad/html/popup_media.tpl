<!-- BEGIN: html_popup -->
<!DOCTYPE html>
<html>
<head>
	<title>[:: Admin ::]</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width" />
	<link rel="SHORTCUT ICON" href="vntrust.ico" type="image/x-icon"/>
	<link rel="icon" href="vntrust.ico" type="image/gif">
	<link href="{DIR_JS}/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="{DIR_STYLE}/fonts/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="{DIR_JS}/jquery_plugins/treeview/jquery.treeview.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="{DIR_JS}/jquery-ui/jquery-ui.min.css" type="text/css" />
	<link rel="stylesheet" type="text/css" href="{DIR_JS}/plupload_236/jquery.ui.plupload/css/jquery.ui.plupload.css"  />
	<link rel="stylesheet" type="text/css" href="modules/media_ad/css/popup_media.css?v=6.2" />
	<script type='text/javascript' src="{DIR_JS}/jquery.min.js"></script>
	<script type='text/javascript' src="{DIR_JS}/jquery-migrate.min.js"></script>
	<script type="text/javascript" src="{DIR_JS}/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="{DIR_JS}/jquery-ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="{DIR_JS}/jquery-ui/jquery.ui.selectable.min.js"></script>
	<script type="text/javascript" src="{DIR_JS}/plupload_236/plupload.full.min.js" charset="UTF-8"></script>
	<script type="text/javascript" src="{DIR_JS}/plupload_236/i18n/vi.js" charset="UTF-8"></script>
	<script type="text/javascript"  src="{DIR_JS}/contextmenu/jquery.contextmenu.js"></script>
	<script type="text/javascript"  src="{DIR_JS}/jquery_plugins/treeview/jquery.treeview.js"></script>
	<script type="text/javascript"  src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>

	<script type="text/javascript">
      var ROOT = "{CONF.rooturl}";
      var ROOT_URI = "{ROOT_URI}";
      var DIR_IMAGE = "{DIR_IMAGE}";
      var win = window.dialogArguments || opener || parent || top;
      var remote_url = "index.php?mod=media&act=remote&do=" ;
      var obj_return="{data.obj_return}";

      var LANG = [];
      LANG.select = "Chọn";
      LANG.download = "Tải về";
      LANG.preview = "Xem chi tiết";
      LANG.rename = "Đổi tên file";
      LANG.upload_size = "Kích thước";
      LANG.pubdate = "Ngày tạo";
      LANG.notupload = "Thư mục không được phép upload";
      LANG.upload_file = "Upload file";
      LANG.upload_mode = "Chọn kiểu upload";
      LANG.upload_mode_remote = "Upload từ URL";
      LANG.upload_mode_local = "Upload từ máy tính";
      LANG.upload_cancel = "Hủy";
      LANG.upload_add_files = "Thêm file";
      LANG.file_name = "Tên file";
      LANG.upload_status = "Trạng thái";
      LANG.upload_info = "Đã tải lên %s/%s tệp. Tốc độ %s/s";
      LANG.upload_stop = "Dừng";
      LANG.upload_continue = "Tiếp tục";
      LANG.upload_finish = "Hoàn tất";
      LANG.crop_error_small = "Ảnh này kích thước quá nhỏ, không nên cắt";
      LANG.save = "Lưu thay đổi";
      LANG.notlogo = "Lỗi: Hệ thống không tìm thấy file Logo, có thể bạn chưa cấu hình chèn ảnh logo hoặc file ảnh bị xóa, vui lòng cấu hình lại";
      LANG.addlogo_error_small = "Ảnh này kích thước quá nhỏ, không thể chèn logo vào";
      LANG.altimage = "Chú thích cho hình";
      LANG.upload_alt_note = "Hãy nhập chú thích cho file trước";
      LANG.createfolder = "Tạo folder";
      LANG.name_folder_error = "Bạn chưa đặt tên cho thư mục hoặc tên không đúng quy chuẩn";
	</script>


</head>
<body >

	{data.main}

</body>
</html>
<!-- END: html_popup -->

<!-- BEGIN: html_popup_media --> 
<input type="hidden" name="module" value="{data.module}"/>
<input type="hidden" name="currentFileUpload" value=""/>
<input type="hidden" name="currentFileUrl" value=""/>
<input type="hidden" name="selFile" value=""/>
<input type="hidden" name="CKEditorFuncNum" value="{data.CKEditorFuncNum}"/>
<input type="hidden" name="obj_gallery" value="{data.obj_gallery}"/>
<input type="hidden" name="area" value="{AREA}"/>
<input type="hidden" name="alt" value="{ALT}"/>
<input type="hidden" name="show_pic" value="{data.show_pic}"/>



<div class="media-content">
    <div class="row upload-wrap">
    <div id="imgfolder" class="col-sm-3 col-xs-4 imgfolder" >
        <p class="upload-loading">
            <em class="fa fa-spin fa-spinner fa-2x m-bottom upload-fa-loading">&nbsp;</em>
            <br />
            Đang tải dữ liệu, vui lòng đợi...
        </p>


    </div>
    <div id="upload-content" class="col-sm-9 col-xs-8 filebrowse">

        <div id="imglist" class="clearfix">
            <p class="upload-loading">
                <em class="fa fa-spin fa-spinner fa-2x m-bottom upload-fa-loading">&nbsp;</em>
                <br />
                Đang tải dữ liệu, vui lòng đợi...
            </p>
        </div>
        <div id="upload-queue"></div>

    </div>


	</div>
</div>

<div class="media-footer">
	<div class="row">
		<div class="col-sm-4 col-xs-12 hidden-xs">
			<div class="row">
				<div class="col-xs-1">
					<div class="refresh text-right">
						<em title="Cập nhật lại" class="fa fa-refresh fa-pointer">&nbsp;</em>
					</div>
				</div>

				<div class="col-xs-5">
					<select name="imgtype" title="Hiển thị loại file" class="form-control input-sm vchange">
						{data.option_filetype}
					</select>
				</div>

				<div class="col-xs-6">
					<select name="order" class="form-control input-sm vchange">
						<option value="0">{LANG.order0}</option>
						<option value="1">{LANG.order1}</option>
						<option value="2">{LANG.order2}</option>
					</select>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xs-12">
			<div id="upload-button-area" title="Dung lượng tối đa của file tải lên: 200.00 MB">
				&nbsp;
			</div>
		</div>

		<div class="col-sm-2 col-xs-12" {data.style_btn_insert} >
			<div class="text-right"><button class="btn btn-success" type="button"  onclick="{data.btn_insert}"><span>Chèn vào bài</span></button></div>
		</div>

	</div>




</div>

<div id="errorInfo" class="upload-hide" title="Thông báo"></div>
<div id="imgpreview" style="overflow:auto;display:none" title="{LANG.preview}">
	<div style="text-align:center;font-size:12px;font-weight:800;margin-top:10px" id="fileInfoAlt" class="dynamic"></div>
	<div style="text-align:center;margin-top:10px" id="fileView" class="dynamic"><span class="fileView"></span></div>
	<div style="text-align:center;font-size:12px;font-weight:800;margin-top:10px" id="fileInfoName" class="dynamic"></div>
	<div style="text-align:center;font-size:11px;margin-top:10px;margin-bottom:10px" id="fileInfoDetail" class="dynamic"></div>
</div>

<div style="display:none" id="contextMenu"></div>

<div class="modal fade" role="dialog" id="modal_createfolder" tabindex="-1"  aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{LANG.createfolder}</h4>
			</div>
			<div class="modal-body">
				<div class=form-group>
				<div class="row">
					<div class="col-xs-4"><label for="createfoldername">Nhập Tên thư mục</label></div>
					<div class="col-xs-8"><input type="text" name="createfoldername" class="form-control dynamic" /></div>
				</div>
				</div>
				<div class=form-group>
					<div class="text-center">
						<input type="button" class="btn btn-primary" name="createfolderOK" value="Tạo Folder"/>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>

			</div>

		</div>

	</div>
</div>

<div class="modal fade" role="dialog" id="modal_filerename" tabindex="-1"  aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{LANG.rename}</h4>
			</div>
			<div class="modal-body">

				<div class=form-group>
					<div class="row">
						<div class="col-xs-3"><label for="filerenameOrigName">Tên cũ</label></div>
						<div class="col-xs-9"><div id="filerenameOrigName"  class="dynamic" style="font-weight: bold;"></div></div>
					</div>
				</div>

				<div class=form-group>
					<div class="row">
						<div class="col-xs-3"><label for="filerenameNewName">{LANG.rename_newname}</label></div>
						<div class="col-xs-9"><input type="text" name="filerenameNewName" class="form-control dynamic" /></div>
					</div>
				</div>
				<div class=form-group>
					<div class="text-center">
						<input type="button" class="btn btn-primary" name="filerenameOK" value="Đổi tên"/>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>

			</div>

		</div>

	</div>
</div>

<div class="modal fade" id="uploadremote" tabindex="-1" role="dialog" aria-labelledby="uploadremoteLabel" aria-hidden="true">
	<div class="modal-dialog">

		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Upload file từ internet</h4>
			</div>
			<div class="modal-body">
				<div class="box-upload-remote" >
					<div class=form-group>
						<label for="uploadremoteFile">Nhập URL file</label>
						<input type="text" class="form-control dynamic" name="uploadremoteFile" id="uploadremoteFile" value="http://" />
					</div>
					<div class=form-group>
						<div class="text-center">
						<input type="button" class="btn btn-primary" name="uploadremoteFileOK" value="Upload file"/>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>

			</div>

		</div>

	</div>
</div>

<script language="javascript" src="modules/media_ad/js/popup_media.js?v=6.5"></script>

<script type="text/javascript">
	/* <![CDATA[*/
	var vnt_max_size_bytes = '{data.max_size_bytes}';
	var vnt_max_width = '1500';
	var vnt_max_height = '1500';
	var vnt_min_width = '10';
	var vnt_min_height = '10';
	var vnt_namecheck = /^([a-zA-Z0-9_-])+$/;
	var array_images = ["gif", "jpg", "jpeg", "pjpeg", "png" ,"svg","ico"];
	var vnt_loading_data = '<p class="upload-loading"><em class="fa fa-spin fa-spinner fa-2x m-bottom upload-fa-loading">&nbsp;</em><br />Đang tải dữ liệu, vui lòng đợi...</p>';
	var vnt_filters = {
	mime_types : [
		  { title : "Images files", extensions : "png,gif,jpg,bmp,tif,tiff,jpe,jpeg,jfif,ico,svg" },
		  { title : "Doc files", extensions : "doc,xls,pps,docx,xlsx,ppsx,vsd,pdf" },
      	  { title : "Video files", extensions : "mp4,3gp,wmv,wma,aiv,mp3,flv"},
		  { title : "Zip files", extensions : "zip,rar" }
		]
	};
	//Resize images on clientside if we can
	var vnt_resize = {
		width : 2000,
		height : 2000, 
		crop: false // crop to exact dimensions
	};
	var vnt_alt_require = true;
	var vnt_auto_alt = true;


$(document).ready(function(){
$("#imgfolder").load("?mod=media&act=remote&do=folderlist&path={data.path}&folder={data.folder}&random=" + randomNum(10));
$("#imglist").load("?mod=media&act=remote&do=imglist&path={data.path_img}&type={data.type}&random=" + randomNum(10));
vnTMedia.init();
});
</script>

<!-- END: html_popup_media --> 