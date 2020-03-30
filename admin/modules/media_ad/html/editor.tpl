<!-- BEGIN: html_folder_list -->
<ul id="foldertree" class="filetree">
	<li class="open collapsable">
		<span {data.style} class="menu {data.class}" title="{data.title}"> &nbsp; {data.titlepath}</span>
		<ul>
			{data.content}
		</ul>
	</li>
</ul>

<span style="display:none" id="path" title="{data.path}"></span>
<span style="display:none" id="foldervalue" title="{data.foldervalue}"></span>
<span style="display:none" id="view_dir" title="{data.view_dir}"></span>
<span style="display:none" id="create_dir" title="{data.create_dir}"></span>
<span style="display:none" id="rename_dir" title="{data.rename_dir}"></span>
<span style="display:none" id="delete_dir" title="{data.delete_dir}"></span>
<span style="display:none" id="upload_file" title="{data.upload_file}"></span>
<span style="display:none" id="create_file" title="{data.create_file}"></span>
<span style="display:none" id="rename_file" title="{data.rename_file}"></span>
<span style="display:none" id="delete_file" title="{data.delete_file}"></span>
<span style="display:none" id="move_file" title="{data.move_file}"></span>

<script type="text/javascript">
 
	/*is_allowed_upload();*/

	$("#foldertree").treeview({
		collapsed : true,
		unique : true,
		persist : "location"
	});

	$("span.folder").click(function() {
		vnTMedia.folderClick(this)
	});

	$("span.menu").mouseup(function() {
		menuMouseup(this)
	});

	$("span.menu").contextMenu("contextMenu", {
		menuStyle : {
			width : "120px"
		},
		bindings : {
			renamefolder : function() {
				renamefolder()
			},
			createfolder : function() {
				createfolder()
			},
			deletefolder : function() {
				deletefolder()
			}
		}
	});
 
</script>

<!-- END: html_folder_list --> 


<!-- BEGIN: html_img_list -->
<!-- BEGIN: loopimg -->
<div class="imgcontent{IMG.sel}" title="{IMG.title}">
	<div style="width:100px;height:96px;display:table-cell; vertical-align:middle;">
		<img class="previewimg" alt="{IMG.alt}" title="{IMG.title}" name="{IMG.data}" src="{IMG.src}" width="{IMG.srcwidth}" height="{IMG.srcheight}" />
	</div>
	<div class="imgInfo">
		{IMG.file_name}
		<br />
		{IMG.size}
	</div>
</div>
<!-- END: loopimg -->
<div class="clear"></div>
<!-- BEGIN: generate_page -->
<div class="generate_page">
	{GENERATE_PAGE}
</div>
<!-- END: generate_page -->
<div style="height:100px"></div>

<script type="text/javascript">
	//<![CDATA[
	$("img.previewimg").lazyload({
		placeholder : "{DIR_IMAGE}/grey.gif",
		container : $(".filebrowse")
	}); 
	
	$(".imgcontent").bind("mouseup", function() {
		vnTMedia.do_SelectPic(this)
		fileMouseup(this)
	});
 

	$(".imgcontent").contextMenu("contextMenu", {
		menuStyle : {
			width : "120px"
		},
		bindings : {
			select : function() {
				insertvaluetofield()
			},
			download : function() {
				download()
			},
			filepreview : function() {
				preview()
			},  
			rename : function() {
				filerename()
			},
			filedelete : function() {
				filedelete()
			}
		}
	});
	//]]>
</script>

<!-- BEGIN: imgsel -->
<script type="text/javascript">
	$(".imgcontent.imgsel").attr('id', 'imgsel_{data.currenttime}');
	window.location.href = "#imgsel_{data.currenttime}";
</script>
<!-- END: imgsel -->
<!-- END: html_img_list -->