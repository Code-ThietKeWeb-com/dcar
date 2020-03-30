
<!-- BEGIN: edit -->

<script type="text/javascript">
	/* <![CDATA[*/
	
function attachCallbacks(a) {
	a.bind("Error", function (a, e) { 
		$('#bugcallbacks').append("<div>Error: " + e.code +   ", Message: " + e.message +   (e.file ? ", File: " + e.file.name : "") + "</div>"   );	 
     a.refresh(); // Reposition Flash/Silverlight
		$('#bugcallbacks').show();
	});
	a.bind("FileUploaded", function (b, e, c) { 	 
		1 == a.total.queued && ("undefined" == typeof debug ? $("#myForm").submit() : ($("#bugcallbacks").show(), 0 < b.e.code && $("#bugcallbacks").append("<div>File: " + b.result + " <br/ > Error code: " + e.message + " <hr></div>")))
	})	
}

// Convert divs to queue widgets when the DOM is ready
$(function() {
	
	$("#uploader").pluploadQueue({
		// General settings
		runtimes : 'gears,flash,silverlight,browserplus,html5',
		url : 'modules/media_ad/ajax/upload.php',
 		rename : true,
		multipart: true,
		multipart_params: {
			'folder_id': '{data.folder_id}',  
			'folder_upload': '{data.folder_upload}',  
			'folder': '{data.folder}', 
			'w' : '{data.w_pic}',			
			'w_thumb': '{data.w_pic}'
		},		
 
		flash_swf_url : ROOT+'js/plupload/plupload.flash.swf',
		silverlight_xap_url : ROOT+'js/plupload/plupload.silverlight.xap' ,
		preinit: attachCallbacks
	});
 	
	
});

	/* ]]> */
</script>
<style>

#uploader { margin:0px auto;} 
.square.note {
    background: none repeat scroll 0 0 #ECECEC;
    border: 1px solid #CCCCCC;
    color: #333333;
    padding: 5px 10px 10px; 
}

</style>
<table width="90%" border="0" cellspacing="2" cellpadding="2" align="center">
  <tr>
    <td><h2><img src="{DIR_IMAGE}/mediamanager.png" align="absmiddle" /> Upload New Media</h2></td>
  </tr>
  <tr>
    <td>
    
          <form action="" method="post" name="myform">
<table width="100%"  border="0" align="center" cellspacing="2" cellpadding="2" class="tableborder">
  <tr>
    <td width="150" ><strong>Folder PATH : </strong></td>
    <td width="100" ><input name="folderpath" type="text" class="textfield" style="width:100%" readonly="readonly" value="{data.folderpath}" /></td>
    <td width="10" >/</td>
    <td ><input name="folder" id="folder" type="text" class="textfield" style="width:100%"  value="{data.folder}"  autocomplete="off" onmouseup="getLoad_Folder();" onkeyup="getLoad_Folder();" /><input type="hidden" id="folder_id" name="folder_id" value="{data.folder_id}"></td>
    <td  width="50" ><input name="btnChange" type="submit" class="button" value="Change Folder" /></td>
  </tr>

</table>
</form>
{data.err}
<br />
<br />

 

<form action="{data.link_action}" method="post" name="myForm" id="myForm"   enctype="multipart/form-data" >

  <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
  
 	<tr >
 
       <td class="row0" > 
       
       <div style="padding:10px 30px;">
      
	<input type="hidden" name="hlistfile" id="hlistfile" value="" />
	<div id="uploader">
		<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
	</div>
  
 
<br />

<div class="square note">
  <ul>
  <li>Mỗi lần upload tối đa <b>50 file</b>. Giữ phím <b>Ctrl</b> để chọn thêm ảnh, phím <b>Shift</b> để chọn cùng lúc nhiều file.</li>
      <li>Dung lượng mỗi file tối đa <b>{data.max_upload}</b></li> 
  </ul>
    <div class="clear"></div>
</div>			

<div id="bugcallbacks" style="display: none "></div>
       </div>
        </td>
    </tr>
     

	</table>
</form>



</td>
  </tr>
</table>

<script type="text/javascript">
  
function getLoad_Folder() {
	document.getElementById('folder_id').value = '';		
	var options = {
		script:"modules/media_ad/ajax/ajax_list.php?json=true&do=folder&",
		varname:"input",
		json:true,
		shownoresults:false,
		maxresults:6,
		callback: function (obj) { 
			document.getElementById('folder_id').value = obj.id;		
		}
	};
	var as_json = new bsn.AutoSuggest('folder', options);
}

</script> 

<!-- END: edit -->

<!-- BEGIN: manage -->

<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="{DIR_JS}/highslide/highslide-ie6.css" />
<![endif]-->
<LINK href="{DIR_JS}/jquery_ui/themes/base/ui.all.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{DIR_JS}/jquery_ui/ui.core.js?t=1"></script>
<script type="text/javascript" src="{DIR_JS}/jquery_ui/ui.draggable.js?t=1"></script>
<script type="text/javascript" src="{DIR_JS}/jquery_ui/ui.resizable.js?t=1"></script>
<script type="text/javascript" src="{DIR_JS}/jquery_ui/ui.button.js?t=1"></script>
<script type="text/javascript" src="{DIR_JS}/jquery_ui/ui.dialog.js?t=1"></script>
<script language="javascript1.2" src="{DIR_JS}/jquery_plugins/jquery.lazyload.js"></script>
<script type="text/javascript" src="{DIR_JS}/jquery_plugins/jquery.flash.js?t=1"></script>
<script>
var LANG = [];
LANG.upload_size = "Kích thước";
LANG.pubdate = "Cập nhật";
LANG.download = "Tải về";
LANG.preview = "Xem chi tiết";
LANG.addlogo = "Thêm Logo";
LANG.select = "Chọn";
LANG.upload_createimage = "Công cụ ảnh";
LANG.move = "Di chuyển";
LANG.rename = "Đổi tên file";
LANG.upload_delfile = "Xóa file";
LANG.createfolder = "Tạo folder";
LANG.renamefolder = "Đổi tên folder";
LANG.deletefolder = "Xóa folder";
LANG.delete_folder = "Bạn có chắc muốn xóa thư mục này không. Nếu xóa thư mục này đồng nghĩa với việc toàn bộ các file trong thư mục này cũng bị xóa ?";
LANG.rename_nonamefolder = "Bạn chưa đặt tên mới cho thư mục hoặc tên thư mục không đúng quy chuẩn";
LANG.folder_exists = "Lỗi! Đã có thư mục cùng tên tồn tại";
LANG.name_folder_error = "Bạn chưa đặt tên cho thư mục hoặc tên không đúng quy chuẩn";
LANG.rename_noname = "Bạn chưa đặt tên mới cho file";
LANG.upload_delimg_confirm = "Bạn có chắc muốn xóa file";
LANG.origSize = "Kích thước gốc";
</script>
{data.err}
<form action="" method="post" name="myform">
<table width="100%"  border="0" align="center" cellspacing="2" cellpadding="2" class="tableborder">
  <tr>
    <td width="200" ><strong>Folder PATH : </strong></td>
    <td ><input name="folderpath" type="text" class="textfield" style="width:100%"  readonly="readonly" value="{data.folderpath}" /></td>
    <td width="150" >/ <input name="foldername" type="text" class="textfield" style="width:120px"  value="" /></td>
    <td  width="50" ><input name="btnCreate" type="submit" class="button" value="Create Folder" /></td>
  </tr>

</table>
</form>
<br />
<form id="manage" name="manage" method="post" action="{data.link_action}">
	<form id="manage" name="manage" method="post" action="{data.link_action}">
		<div class="box-manage">
			<div class="nav-action nav-top">{data.button}</div>
			<div class="table-list table-responsive">

				<table  class="table table-sm table-bordered table-hover " id="table_list" >
<thead>
<tr>
		<th width="5%" align="center" ><input type="checkbox" value="all" class="checkbox" name="checkall" id="checkall"/></th>
		<th width="10%" align="center" >Preview</th>
		<th width="30%" align="left" >Image Name</th>
		<th width="20%" align="center" >Dimensions (W x H px)</th>
		<th width="15%" align="center" >Size</th>
		<th width="5%"  align="center" >Action</th>
		</tr>
</thead>
<tbody>

<tr>
			<tr class="row0" id="row_up" > 
			<td  align="left" >&nbsp;</td>
			<td  align="center"><a href="{data.link_up}" ><img src="{DIR_IMAGE_MEDIA}/folderup_32.png" /></a></td>
			<td  align="left" >...</td>
			<td  align="center" >&nbsp;</td>
			<td  align="center" >&nbsp;</td>
			<td   align="center" >&nbsp;</td>
			</tr>    
<!-- BEGIN: html_row -->
<tr class="row0" id="{row.row_id}"> 
	<td align="center" >{row.check_box}</td>
	<td align="center" >{row.preview}</td>
	<td align="left" >{row.name}</td>
	<td align="center" >{row.dimensions}</td>
	<td align="center" >{row.size}</td>
	<td align="center" >{row.action}</td>
</tr>
<!-- END: html_row -->
</tbody>
				</table>
			</div>
			<div class="nav-action nav-bottom">{data.button}</div>
		</div>
		<input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
		<input type="hidden" name="do_action" id="do_action" value="" >
	</form>

<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1">
  <tr>
    <td  height="30">{data.nav}</td>
  </tr>
</table>
<br />

<form enctype="multipart/form-data" method="post" id="uploadForm" name="uploadForm" action="">
<table width="100%"  border="0" align="center" cellspacing="2" cellpadding="2" class="tableborder">
  <tr>
    <td width="200" ><strong>Upload File [ Max = {data.max_upload} ] : </strong></td>
    <td ><input type="file" name="file_upload" id="file_upload" style="display: inline-block"/> <input name="btnUpload" type="submit" class="button" value="Start Upload" /></td>

  </tr>

</table>
</form>
<div id="imgpreview" style="overflow:auto;display:none" title="{LANG.preview}">
	<div style="text-align:center;font-size:12px;font-weight:800;margin-top:10px" id="fileInfoAlt" class="dynamic"></div>
	<div style="text-align:center;margin-top:10px" id="fileView" class="dynamic"><span class="fileView"></span></div>
	<div style="text-align:center;font-size:12px;font-weight:800;margin-top:10px" id="fileInfoName" class="dynamic"></div>
	<div style="text-align:center;font-size:11px;margin-top:10px;margin-bottom:10px" id="fileInfoDetail" class="dynamic"></div>
</div>
<script language="javascript1.2" src="modules/media_ad/js/media.js"></script>  
<br />
<!-- END: manage -->