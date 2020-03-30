<!-- BEGIN: html_folder_list -->
<ul id="foldertree" class="filetree">
	<li class="open collapsable">
		<span {data.style} class="menu view_dir {data.class}" title="{data.title}"> &nbsp; {data.titlepath}</span>
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


  $("#foldertree").treeview({
    collapsed : true,
    unique : true,
    persist : "location"
  });

  $("span.folder").click(function() {
    vnTMedia.folderClick(this)
  });

  $("span.menu").mouseup(function() {
    vnTMedia.menuMouseup(this)
  });


  $("span.menu").contextMenu("contextMenu", {
    menuStyle : {
      width : "120px"
    },
    bindings : {
      createfolder : function() {
        vnTMedia.createfolder()
      }
    }
  });


  $(document).ready(function(){
    vnTUpload.init();
  });
</script>
<!-- END: html_folder_list --> 
 



 
<!-- BEGIN: html_img_list -->

<!-- BEGIN: loopimg -->
<div class="imgcontent {IMG.sel}" title="{IMG.title}">
	<div  class="imgIcon">
		<img class="previewimg" alt="{IMG.alt}" title="{IMG.title}" name="{IMG.data}" src="{IMG.src}" width="{IMG.srcwidth}" height="{IMG.srcheight}" />
	</div>
	<div class="imgInfo">
		<span class="name-short">{IMG.file_name_short}</span>
		<span class="name-full">{IMG.file_name}</span>
		<span class="size">{IMG.size}</span>
	</div>
</div>
<!-- END: loopimg -->

<div class="clear"></div>

<div style="height:100px"></div>

<script type="text/javascript">

  $('.imgcontent').bind("mouseup", function(e) {
    e.preventDefault();
    vnTMedia.fileMouseup(this, e);
  });

  $(".imgcontent").dblclick(function() {
    vnTMedia.insertvaluetofield();
  });


  $(".imgcontent").contextMenu("contextMenu", {
    menuStyle : {
      width : "120px"
    },
    bindings : {
      select : function() {
        vnTMedia.insertvaluetofield()
      },
      download : function() {
        vnTMedia.download()
      },
      filepreview : function() {
        vnTMedia.preview()
      },
      rename : function() {
        vnTMedia.filerename()
      },
      filedelete : function() {
        vnTMedia.filedelete()
      }
    }
  });


  $( "#imglist" ).selectable({
    filter: '.imgcontent',
    delay: 90,
    start: function( e, ui ){
      KEYPR.isSelectable = true;
      KEYPR.isFileSelectable = true;
    },
    selecting: function( e, ui ){
      fileSelecting(e, ui);
    },
    stop: function( e, ui ){
      fileSelectStop(e, ui);

      setTimeout(function(){
        KEYPR.isSelectable = false;
        KEYPR.isFileSelectable = false;
      }, 50);
    },
    unselecting: function( e, ui ){
      fileUnselect(e, ui);
    }
  });


  //]]>
</script>


<!-- END: html_img_list -->