function randomNum(a) {
  for (var b = "", d = 0; d < a; d++) {
    b += "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890".charAt(Math.floor(Math.random() * 62));
  }
  return b
}
// trim(' Kevin van Zonneveld ');
function trim(str, charlist) {
  var whitespace, l = 0, i = 0;
  str += '';

  if (!charlist) {
    whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
  } else {
    charlist += '';
    whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
  }

  l = str.length;
  for ( i = 0; i < l; i++) {
    if (whitespace.indexOf(str.charAt(i)) === -1) {
      str = str.substring(i);
      break;
    }
  }

  l = str.length;
  for ( i = l - 1; i >= 0; i--) {
    if (whitespace.indexOf(str.charAt(i)) === -1) {
      str = str.substring(0, i + 1);
      break;
    }
  }

  return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}

function htmlspecialchars_decode(string, quote_style) {
  var optTemp = 0,
    i = 0,
    noquotes = false;
  if (typeof quote_style === 'undefined') {
    quote_style = 2;
  }
  string = string.toString()
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>');
  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  };
  if (quote_style === 0) {
    noquotes = true;
  }
  if (typeof quote_style !== 'number') {
    // Allow for a single string or an array of string flags
    quote_style = [].concat(quote_style);
    for (i = 0; i < quote_style.length; i++) {
      // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
      if (OPTS[quote_style[i]] === 0) {
        noquotes = true;
      } else if (OPTS[quote_style[i]]) {
        optTemp = optTemp | OPTS[quote_style[i]];
      }
    }
    quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
    // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
  }
  if (!noquotes) {
    string = string.replace(/&quot;/g, '"');
  }
  // Put this in last place to avoid escape being double-decoded
  string = string.replace(/&amp;/g, '&');

  return string;
}

function vnt_filename_alt( fileAlt ){
  var lastChar = fileAlt.charAt(fileAlt.length - 1);

  if( lastChar === '/' || lastChar === '\\' ){
    fileAlt = fileAlt.slice(0, -1);
  }

  fileAlt = decodeURIComponent( htmlspecialchars_decode( fileAlt.replace(/^.*[\/\\]/g, '') ) );
  fileAlt = fileAlt.split('.');

  if( fileAlt.length > 1 ){
    fileAlt[fileAlt.length - 1] = '';
  }

  fileAlt = fileAlt.join(' ');
  fileAlt = fileAlt.split('_');
  fileAlt = fileAlt.join(' ');
  fileAlt = fileAlt.split('-');
  fileAlt = fileAlt.join(' ');
  return trim( fileAlt );
}
function resize_byWidth(a, b, d) {
  return Math.round(d / a * b);
}
function resize_byHeight(a, b, d) {
  return Math.round(d / b * a);
}
function calSize(a, b, d, e) {
  if (a > d) {
    b = resize_byWidth(a, b, d);
    a = d;
  }
  if (b > e) {
    a = resize_byHeight(a, b, e);
    b = e
  }
  return [a, b];
}
function calSizeMax(a, b, d, e) {
  var g = d;
  d = resize_byWidth(a, b, d);
  if (!(d <= e )) {
    d = e;
    g = resize_byHeight(a, b, e);
  }
  return [g, d];
}
function calSizeMin(a, b, d, e) {
  var g = d;
  d = resize_byWidth(a, b, d);
  if (!(d >= e )) {
    d = e;
    g = resize_byHeight(a, b, e);
  }
  return [g, d];
}
var namecheck = /^([a-zA-Z0-9_-])+$/;

var ICON = [];
ICON.select = ROOT + 'js/contextmenu/icons/select.png';
ICON.download = ROOT + 'js/contextmenu/icons/download.png';
ICON.preview = ROOT + 'js/contextmenu/icons/view.png';
ICON.create = ROOT + 'js/contextmenu/icons/copy.png';
ICON.move = ROOT + 'js/contextmenu/icons/move.png';
ICON.rename = ROOT + 'js/contextmenu/icons/rename.png';
ICON.filedelete = ROOT + 'js/contextmenu/icons/delete.png';

var NVLDATA = {
  support : false,
  init : function(){
    if( typeof( Storage ) !== "undefined" ){
      NVLDATA.support = true;
    }
  },
  getValue : function( key ){
    if( ! NVLDATA.support ){
      return '';
    }

    if( typeof( sessionStorage[key] ) !== "undefined" && sessionStorage[key] ){
      return sessionStorage[key];
    }

    return '';
  },
  setValue : function( key, val ){
    sessionStorage[key] = val;
  }
};

/* Keypress, Click Handle */
var KEYPR = {
  isCtrl : false,
  isShift : false,
  shiftOffset : 0,
  allowKey : [ 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123 ],
  isSelectable: false,
  isFileSelectable: false,
  init : function(){
    $('body').keyup(function(e){
      if( ! $(e.target).is('.dynamic') && $.inArray( e.keyCode, KEYPR.allowKey ) == -1 ){
        e.preventDefault();
      }else{
        return;
      }

      // Ctrl key unpress
      if( e.keyCode == 17 ){
        KEYPR.isCtrl = false;
      }else if( e.keyCode == 16 ){
        KEYPR.isShift = false;
      }
    });

    $('body').keydown(function(e){
      if( ! $(e.target).is('.dynamic') && $.inArray( e.keyCode, KEYPR.allowKey ) == -1 ){
        e.preventDefault();
      }else{
        return;
      }

      // Ctrl key press
      if( e.keyCode == 17 /* Ctrl */ ){
        KEYPR.isCtrl = true;
      }else if( e.keyCode == 27 /* ESC */ ){
        // Unselect all file
        $(".imgsel").removeClass("imgsel");
        LFILE.setSelFile();


        // Reset shift offset
        KEYPR.shiftOffset = 0;
      }else if( e.keyCode == 65 /* A */ && e.ctrlKey === true ){
        // Select all file
        $(".imgcontent").addClass("imgsel");
        LFILE.setSelFile();

      }else if( e.keyCode == 16 /* Shift */ ){
        KEYPR.isShift = true;
      }else if( e.keyCode == 46 /* Del */ ){
        // Delete file
        if( $('.imgsel').length && $("span#delete_file").attr("title") == '1' ){
          filedelete();
        }
      }else if( e.keyCode == 88 /* X */ ){
        // Move file
        if( $('.imgsel').length && $("span#move_file").attr("title") == '1' ){
          move();
        }
      }
    });

    // Unselect file when click on wrap area
    $('#imglist').click(function(e){
      if( KEYPR.isSelectable == false ){
        if( $(e.target).is('#imglist') ){
          $(".imgsel").removeClass("imgsel");
        }
      }

      KEYPR.isSelectable = false;
    });
  }
};

/* List File Handle */
var LFILE = {
  reload : function( path, file ){
    var type =  $("select[name=imgtype]").val();
    $("div#imglist").html(vnt_loading_data).load("index.php?mod=media&act=remote&do=imglist&path=" + path + "&imgfile=" + file + "&type=" +type + "&random=" + randomNum(10)) ;
  },
  setSelFile : function(){
    $("input[name=selFile]").val('');

    if( $('.imgsel').length ){
      fileName = new Array();
      $.each( $('.imgsel'), function(){
        fileName.push( $(this).attr("title") );
      });
      fileName = fileName.join('|');

      $("input[name=selFile]").val(fileName);
    }
  },
  setViewMode : function(){
    var numFiles = $('[data-img="false"]').length;
    var numImage = $('[data-img="true"]').length;
    var autoMode = $(".viewmode em").data('auto');

    if( autoMode ){
      if( numImage > numFiles ){
        $('#imglist').removeClass('view-detail');
      }else if( numFiles > 0 ){
        $('#imglist').addClass('view-detail');
      }
    }

    LFILE.setViewIcon();
  },
  setViewIcon : function(){
    if( $('#imglist').is('.view-detail') ){
      $('.viewmode em').removeClass('fa-hourglass-o fa-spin fa-list').addClass('fa-file-image-o').attr('title', $('.viewmode em').data('langthumb'));
    }else{
      $('.viewmode em').removeClass('fa-hourglass-o fa-spin fa-file-image-o').addClass('fa-list').attr('title', $('.viewmode em').data('langdetail'));
    }
  }

};


// Xu ly keo tha chuot chon file
function fileSelecting(e, ui){
  if( e.ctrlKey ){
    if( $(ui.selecting).is('.imgsel') ){
      $(ui.selecting).addClass('imgtempunsel');
    }else{
      $(ui.selecting).addClass('imgtempsel');
    }
  }else if( e.shiftKey ){
    $(ui.selecting).addClass('imgtempsel');
  }else{
    $(ui.selecting).removeClass('imgtempunsel').addClass('imgtempsel');
    $('#imglist .imgcontent:not(.imgtempsel)').addClass('imgtempunsel');
  }
}

// Xu ly khi thoi chon file
function fileUnselect(e, ui){
  $(ui.unselecting).removeClass('imgtempunsel imgtempsel');
}

// Xu ly khi ket thuc chon file
function fileSelectStop(e, ui){
  $('#imglist .ui-selected').removeClass('ui-selected');
  $('.imgtempsel').addClass('imgsel').removeClass('imgtempsel');
  $('.imgtempunsel').removeClass('imgsel imgtempunsel');
  LFILE.setSelFile();
}


// Upload tu internet (Submit)
$('[name="uploadremoteFileOK"]').click(function(){
  var fileUrl = $("input[name=uploadremoteFile]").val();
  var currUrl = $("input[name=currentFileUrl]").val();
  var folderPath = $("span#foldervalue").attr("title");
  var check = fileUrl + " " + folderPath;
  var fileAlt = $('#uploadremoteFileAlt').val();

  if( /^(https?|ftp):\/\//i.test( fileUrl ) === false) fileUrl = 'http://' + fileUrl;
  $("input[name=uploadremoteFile]").val(fileUrl);

  if( /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test( fileUrl ) && currUrl != check   ){
    $(this).attr('disabled', 'disabled');
    $('#upload-remote-info').html('<em class="fa fa-2x fa-spinner fa-spin"></em>');

    $.ajax({
      type : "POST",
      url : "index.php?mod=media&act=editor&do=upload&random=" + randomNum(10),
      data : "path=" + folderPath + "&fileurl=" + fileUrl + "&filealt=" + fileAlt,
      success : function(k){
        $("input[name=currentFileUrl]").val( check );
        $('[name="uploadremoteFileOK"]').removeAttr('disabled').val('');

        var l = k.split("_");
        if( l[0] == "ERROR" ){
          $("div#errorInfo").html(l[1]).dialog("open");
          $('#upload-remote-info').html('');
        }else{
          $("input[name=selFile]").val(k);
          $('#upload-remote-info').html('<em class="fa fa-2x fa-check text-success"></em>');
          LFILE.reload( folderPath, k );
          setTimeout( $('#uploadremote').modal('hide') , 500 );
        }
      }
    });
  }else{
    alert('fileUrl  '+fileUrl + ' không hợp lệ' );
  }
});

var vnTUpload = {
  uploader: null, // Pupload variable
  rendered: false, // Is rendered upload container
  started: false,
  buttons: '<div class="row">' +
    '<div class="col-sm-7 col-xs-8 buttons">' +
    '<div class="btn-group dropup browse-button">' +

    '<span><a class="btn btn-info" id="upload-remote"   href="javascript:void(0)" data-toggle="modal" data-target="#uploadremote" >' + LANG.upload_mode_remote + '</a></span>' +
    ' <span><a class="btn btn-primary" id="upload-local" href="javascript:void(0)">' + LANG.upload_mode_local + '</a></span>' +

    '</div> ' +
    '</div>' +
    '<div class="col-sm-5 col-xs-4 ">' +
    '<div class="row" id="upload-queue-total" >' +
    '<div class="col-xs-5 total-size"></div>' +
    '<div class="col-xs-7 total-status"></div>' +
    '</div>' +
    '</div>' +
    '</div>',


  reset: function(){
    // Destroy current uploader
    //vnTUpload.uploader.destroy();
    vnTUpload.started = false;

    // Clear uploader variable
    vnTUpload.uploader = null;
    // Reset upload button
    $('#upload-button-area').html( vnTUpload.buttons );

    // Clear upload container
    $('#upload-queue-files').html('');
  },

  renderUI: function(){
    // Hide files list and show upload container
    $('#imglist').css({'display' : 'none'});
    $('#upload-queue').css({'display' : 'block'});

    // Add some button
    $('#upload-button-area .buttons').append(
      '<input id="upload-start" type="button" class="btn btn-primary" value="' + LANG.upload_file + '"/> ' +
      '<input id="upload-cancel" type="button" class="btn btn-default" value="' + LANG.upload_cancel + '"/> '
    );

    // Change browse_button (Change style, Method: setOption is error)
    $('#upload-button-area .browse-button button').remove();
    $('#upload-remote').parent().remove();
    $('#upload-button-area .browse-button ul').removeAttr('role').removeClass('dropdown-menu').addClass('fixul');
    $('#upload-local').addClass('btn btn-primary').text(LANG.upload_add_files);
    $('#upload-button-area .browse-button ul li div:first').width( $('#upload-local').outerWidth() ).height( $('#upload-local').outerHeight() );

    // Build upload queue
    $('#upload-queue').html('<div class="queue-header">' +
      '<div class="container-fluid">' +
      '<div class="row">' +
      '<div class="col-sm-4 col-xs-9 ">' + LANG.file_name + '</div>' +
      '<div class="col-sm-3 col-xs-3 hidden-xs">' + LANG.altimage + '</div>' +
      '<div class="col-sm-2 col-xs-2 hidden-xs">' + LANG.upload_size + '</div>' +
      '<div class="col-sm-2 col-xs-2 hidden-xs">' + LANG.upload_status + '</div>' +
      '<div class="col-sm-1 col-xs-1"> </div>' +
      '</div>' +
      '</div>' +
      '</div>' +
      '<div id="upload-queue-files" class="container-fluid"></div>');

    // Rendered is true
    vnTUpload.rendered = true;
  },

  updateList: function(){
    var fileList = $('#upload-queue-files').html('');
    var fileAlt;

    $.each( vnTUpload.uploader.files, function(i, file){
      fileAlt = NVLDATA.getValue(file.id);

      if( vnt_auto_alt && fileAlt == '' ){

        fileAlt = vnt_filename_alt( file.name );

        NVLDATA.setValue( file.id, fileAlt );
      }

      fileList.append(
        '<div id="' + file.id + '" class="row file-item">' +
        '<div class="col-sm-4 col-xs-9 file-name"><span>' + file.name + '</span></div>' +
        '<div class="col-sm-3 col-xs-3 hidden-xs file-alt"><input type="text" value="' + fileAlt + '" onkeyup="NVLDATA.setValue( \'' + file.id + '\', this.value);" class="form-control upload-file-alt dynamic"/></div>' +
        '<div class="col-sm-2 col-xs-2 hidden-xs file-size">' + plupload.formatSize(file.size) + '</div>' +
        '<div class="col-sm-2 col-xs-2 hidden-xs file-status">' + file.percent + '%</div>' +
        '<div class="col-sm-1 col-xs-1 file-action text-right"></div>' +
        '</div>'
      );

      vnTUpload.handleStatus( file, false );

      $('#' + file.id + ' .file-delete').click(function(e){
        $('#' + file.id).remove();
        vnTUpload.uploader.removeFile( file );

        e.preventDefault();
      });
    });

    $('#upload-queue-total .total-size').html( plupload.formatSize( vnTUpload.uploader.total.size ) );

    // Scroll to end of file list
    fileList[0].scrollTop = fileList[0].scrollHeight;

    vnTUpload.updateTotalProgress();

    // Enable, disable start button
    if( vnTUpload.uploader.files.length ){
      $('#upload-start').removeAttr('disabled');
    }else{
      $('#upload-start').attr('disabled', 'disabled');
    }
  },

  destroyUpload: function(){
    // Reset uploader
    vnTUpload.reset();

    // Hide upload container and show file list
    $('#upload-queue').html('').css({'display' : 'none'});
    $('#imglist').css({'display' : 'block'});

    // Rendered is false
    vnTUpload.rendered = false;
  },

  handleStatus: function( file, response ){
    var actionClass;

    if( response != false ){
      check = response.split('_');

      if( check[0] == 'ERROR' ){
        file.status = plupload.FAILED;
        file.hint = check[1];
        vnTUpload.uploader.total.uploaded --;
        vnTUpload.uploader.total.failed ++;
      }else{
        file.name = response;
      }

      $.each( vnTUpload.uploader.files, function(i, f){
        if( f.id == file.id ){
          vnTUpload.uploader.files[i].status = file.status;
          vnTUpload.uploader.files[i].hint = file.hint;
          vnTUpload.uploader.files[i].name = file.name;
        }
      });
    }

    if( file.status == plupload.DONE ){
      actionClass = 'text-success fa fa-lg fa-check';
    }else if( file.status == plupload.FAILED ){
      actionClass = 'text-danger fa fa-lg fa-exclamation-triangle';
    }else if( file.status == plupload.QUEUED ){
      actionClass = 'fa fa-lg fa-trash-o file-delete fa-pointer';
    }else if( file.status == plupload.UPLOADING ){
      actionClass = 'text-info fa fa-lg fa-spin fa-circle-o-notch';
    }else{
      // Nothing to do
    }

    $('#' + file.id + ' .file-action').html('<i class="' + actionClass + '"></i>');

    if( file.hint ){
      $('#' + file.id).attr('title', file.hint);
    }
  },
  uploadCancel: function(){
    // Reset uploader
    vnTUpload.reset();

    // Hide upload container and show file list
    $('#upload-queue').html('').css({'display' : 'none'});
    $('#imglist').css({'display' : 'block'});

    // Rendered is false
    vnTUpload.rendered = false;

    // Init uploader
    vnTUpload.init();
  },

  updateTotalProgress: function(){
    $('#upload-queue-total .total-status').html(
      '<div class="progress">' +
      '<div class="progress-bar" role="progressbar" aria-valuenow="' + vnTUpload.uploader.total.percent + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + vnTUpload.uploader.total.percent + '%;">' + vnTUpload.uploader.total.percent + '%</div>' +
      '</div>'
    );

  },

  finish: function(){
    var folderPath = $("span#foldervalue").attr("title");

    if( vnTUpload.uploader.total.uploaded > 0 ){
      var selFile = new Array();

      $.each( vnTUpload.uploader.files, function( k, v ){
        if( v.status == plupload.DONE ){
          selFile.push( v.name );
        }
      });

      selFile = selFile.join('|');
    }else{
      var selFile = '';
    }

    $("input[name=selFile]").val( selFile );
    vnTUpload.uploadCancel();


    LFILE.reload( folderPath, selFile );
  },

  init : function(){
    // Reset upload if exists
    if( vnTUpload.uploader != null ){
      vnTUpload.reset();
    }

    $('#upload-button-area').html( vnTUpload.buttons );

    var folderPath = $("span#foldervalue").attr("title");

    vnTUpload.uploader = new plupload.Uploader({
      runtimes : 'html5,flash,silverlight,html4',
      browse_button : 'upload-local',
      url :  'modules/media_ad/ajax/upload_media.php?folder_path='+folderPath,
      max_file_size : vnt_max_size_bytes,
      flash_swf_url : ROOT+'js/plupload_236/Moxie.swf',
      silverlight_xap_url : ROOT+'js/plupload_236/Moxie.xap',
      drop_element : 'upload-content',
      filters : vnt_filters,
      resize: vnt_resize,
      multipart : true,
      init: {
        // Event on init uploader
        PostInit: function(){

        },

        // Event on add file (Add to queue or first add)
        FilesAdded: function(up, files){
          // Build upload container
          if( ! vnTUpload.rendered ){
            vnTUpload.renderUI();
          }

          vnTUpload.updateList();

          $('#upload-start').click(function(){
            // Check file before start upload
            var allow_start = true;
            if( vnt_alt_require ){
              $.each( $('#upload-queue-files .file-alt input'), function(){
                if( $(this).val() == '' ){
                  allow_start = false;
                  return false;
                }
              });

              if( allow_start == false ){
                $("div#errorInfo").html(LANG.upload_alt_note).dialog("open");
              }
            }

            if( allow_start ){
              vnTUpload.uploader.start();
            }
          });

          $('#upload-cancel').click(function(){
            vnTUpload.uploadCancel();
          });
        },

        // Event on trigger a file upload status
        UploadProgress: function( up, file ){
          $('#' + file.id + ' .file-status').html( file.percent + '%' );
          vnTUpload.handleStatus( file, false );
          vnTUpload.updateTotalProgress();
        },

        // Event on one file finish uploaded (Maybe success or error)
        FileUploaded: function( up, file, response ){
          response = response.response;
          vnTUpload.handleStatus( file, response );
        },

        // Event on start upload or finish upload
        StateChanged: function(){
          // Start upload
          if( vnTUpload.uploader.state === plupload.STARTED ){
            if( ! vnTUpload.started ){
              vnTUpload.started = true;
              // Hide control button
              $('#upload-start, #upload-cancel, #upload-button-area .browse-button').hide();

              // Add some button
              $('#upload-button-area .buttons').append(
                '<input id="upload-stop" type="button" class="btn btn-primary" value="' + LANG.upload_stop + '"/> ' +
                '<input style="display:none" id="upload-continue" type="button" class="btn btn-primary" value="' + LANG.upload_continue + '"/>' +
                '<div class="total-info pull-right"></div>'
              );


              // Init upload progress bar
              $('#upload-queue-total .total-status').html(
                '<div class="progress">' +
                '<div class="progress-bar" role="progressbar" aria-valuenow="' + vnTUpload.uploader.total.percent + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + vnTUpload.uploader.total.percent + '%;">' + vnTUpload.uploader.total.percent + '%</div>' +
                '</div>'
              );

              // Set button handle
              $('#upload-stop').click(function(){
                $(this).hide();
                $('#upload-continue').show();
                vnTUpload.uploader.stop();
              });

              $('#upload-continue').click(function(){
                $(this).hide();
                $('#upload-stop').show();
                vnTUpload.uploader.start();
              });
            }
          }else{
            vnTUpload.updateList();
          }
        },

        // Event on a file is uploading
        UploadFile: function( up, file ){
          // Not thing to do
        },

        // Event on remove a file
        FilesRemoved: function(){
          var scrollTop = $('#upload-queue-files').scrollTop();
          vnTUpload.updateList();
          $('#upload-queue-files').scrollTop( scrollTop );
        },

        // Event on all files are uploaded
        UploadComplete: function( up, files ){
          $('#upload-continue').hide();
          $('#upload-stop').hide();

          // Show finish button if has failed file
          if( vnTUpload.uploader.total.failed > 0 ){
            $('<input type="button" class="btn btn-primary" value="' + LANG.upload_finish + '" id="upload-finish"/>').insertBefore( $('#upload-stop') );

            $('#upload-finish').click(function(){
              vnTUpload.finish();
            });
          }else{
            $('<i class="fa fa-2x text-success fa-spin fa-spinner"></i>').insertBefore( $('#upload-stop') );
            setTimeout( "vnTUpload.finish()", 1000 );
          }
        },

        // Event on error
        Error: function(up, err){
          $("div#errorInfo").html( "Error #" + err.message + ": <br>" + err.file.name ).dialog("open");

          if( err.code === plupload.INIT_ERROR ){
            setTimeout( "vnTUpload.destroyUpload()", 1000 );
          }
        },

        // Get image alt before upload
        BeforeUpload: function(up, file) {
          var filealt = '';

          if( $('#' + file.id + ' .file-alt').length ){
            filealt = $('#' + file.id + ' .file-alt input').val();
          }

          vnTUpload.uploader.settings.multipart_params = {"filealt": filealt };
        }
      }
    });

    vnTUpload.uploader.init();

  }


};


vnTMedia = {
//  --------------------------------- main action -------------------------------- //


  insertvaluetofield : function ()
  {
    if ($("input[name=CKEditorFuncNum]").val() > 0 )
    {
      var a = $("input[name=CKEditorFuncNum]").val() ;
      var c = $("input[name=selFile]").val();
      var f =  $("span#foldervalue").attr("title") ;
      var d = ROOT_URI +"vnt_upload/"+ f + "/" + c;

      window.opener.CKEDITOR.tools.callFunction(a, d);
      window.close();

    } else if ($("input[name=obj_gallery]").val() !='') {
      vnTMedia.insertGallery();
    }else{
      var module = $("input[name=module]").val();
      var path_file =  $("span#path").attr("title");
      var src_file =  $("span#foldervalue").attr("title") + "/" +$("input[name=selFile]").val();
      if(module){
        src_file = src_file.replace(path_file+"/", "");
        src_thumb = ROOT + "vnt_upload/"+ $("span#foldervalue").attr("title") + "/thumbs/" +$("input[name=selFile]").val();
        self.parent.vnTRUST.send_to_modules(obj_return,src_file,src_thumb) ;
      }else{
        show_pic = $("input[name=show_pic]").val();
        var d = ROOT_URI +"vnt_upload/"+src_file ;
        pic ='';
        if(show_pic){
          pic =	 ROOT + "vnt_upload/"+ src_file;
        }
        self.parent.vnTRUST.send_to_object(obj_return,d,pic) ;
      }
    }
  },


  insertGallery:function(){
    vnTMedia.setSelFile();
    var obj =  $("input[name=obj_gallery]").val();
    var module =  $("input[name=module]").val();
    var folder  = $("#foldervalue").attr("title");
    var list_file =  $("input[name=selFile]").val();
    if(list_file)
    {
      arr_file = list_file.split("|");
      var list_img = '';
      for (a in arr_file ) {
        if(list_img!='') { list_img+='|'; }
        list_img +=  folder+'/'+arr_file[a] ;
      }

      self.parent.vnTRUST.callbackGallery(obj,module,list_img) ;
    }else{
      alert('Vui lòng chọn 1 hình') ;
    }
  },

  insertEditor:function(){
    vnTMedia.setSelFile();
    var folder  = $("#foldervalue").attr("title");
    var list_file =  $("input[name=selFile]").val();


    if(list_file)
    {
      arr_file = list_file.split("|");
      var list_img = '';
      for (a in arr_file ) {
        if(list_img!='') { list_img+='|'; }
        list_img +=  folder+'/'+arr_file[a] ;
      }
      self.parent.callbackEditorGallery(list_img) ;
    }else{
      alert('Vui lòng chọn 1 hình') ;
    }


  },

  setSelFile:function(){
    $("input[name=selFile]").val('');

    if( $('.imgsel').length ){
      fileName = new Array();
      $.each( $('.imgsel'), function(){
        fileName.push( $(this).attr("title") );
      });
      fileName = fileName.join('|');

      $("input[name=selFile]").val(fileName);
    }

  },

  folderClick:function (a) {

    $("#upload-queue").hide();
    $("#imglist").show();

    var b = $(a).attr("title");
    if (b != $("span#foldervalue").attr("title")) {

      $("span#foldervalue").attr("title", b);
      $("span#view_dir").attr("title", $(a).is(".view_dir") ? "1" : "0");

      $("span.folder").css("color", "");
      $(a).css("color", "red");

      if ($(a).is(".view_dir")) {
        $("div#imglist").html('<p style="padding:20px; text-align:center"><img src="' + DIR_IMAGE + '/load_bar.gif"/> please wait...</p>').load("index.php?mod=media&act=remote&do=imglist&path=" + b  + "&type=image"  + "&random=" + randomNum(10))
      } else {
        $("div#imglist").text("")
      }

    }


    vnTUpload.uploader = null;
    vnTUpload.rendered = false;
    vnTUpload.started = false;
    vnTUpload.init();
  },

  menuMouseup : function (a) {
    $(a).attr("title");
    $("span").attr("name", "");
    $(a).attr("name", "current");
    var b = "";
    //if ($(a).is(".create_dir")) {
    b += '<li id="createfolder"><img style="margin-right:5px" src="' + ICON.create + '"/>' + LANG.createfolder + '</li>'
    //}
    //if ($(a).is(".rename_dir")) {
    //  b += '<li id="renamefolder"><img style="margin-right:5px" src="' + ICON.rename + '"/>' + LANG.renamefolder + '</li>'
    //  }
    //if ($(a).is(".delete_dir")) {
    //  b += '<li id="deletefolder"><img style="margin-right:5px" src="' + ICON.filedelete + '"/>' + LANG.deletefolder + '</li>'
    //}
    if (b != "") {
      b = "<ul>" + b + "</ul>"
    }

    $("div#contextMenu").html(b)
  },

  fileMouseup : function ( file, e ){
    // Khong xu ly neu jquery UI selectable dang kich hoat
    if( KEYPR.isFileSelectable == false ){
      // Set shift offset
      if( e.which != 3 && ! KEYPR.isShift ){
        // Reset shift offset
        KEYPR.shiftOffset = 0;

        $.each( $('.imgcontent'), function(k, v){
          if( v == file ){
            KEYPR.shiftOffset = k;
            return false;
          }
        });
      }

      // e.which: 1: Left Mouse, 2: Center Mouse, 3: Right Mouse
      if( KEYPR.isCtrl ){
        if( $(file).is('.imgsel') && e.which != 3 ){
          $(file).removeClass('imgsel');
        }else{
          $(file).addClass('imgsel');
        }
      }else if( KEYPR.isShift && e.which != 3 ){
        var clickOffset = -1;
        $('.imgcontent').removeClass('imgsel');

        $.each( $('.imgcontent'), function(k, v){
          if( v == file ){
            clickOffset = k;
          }

          if( ( clickOffset == -1 && k >= KEYPR.shiftOffset ) || ( clickOffset != -1 && k <= KEYPR.shiftOffset ) || v == file ){
            if( ! $(v).is('.imgsel') ){
              $(v).addClass('imgsel');
            }
          }
        });
      }else{
        if( e.which != 3 || ( e.which == 3 && ! $(file).is('.imgsel') ) ){
          $('.imgsel').removeClass('imgsel');
          $(file).addClass('imgsel');
        }

        if( e.which == 3 ){

          var b = $("input[name=CKEditorFuncNum]").val(), d = $("input[name=area]").val(), html = "<ul>";

          html += '<li id="select"><em class="fa fa-lg fa-check-square-o">&nbsp;</em> ' + LANG.select + '</li>'
          html += '<li id="download"><em class="fa fa-lg fa-download">&nbsp;</em> ' + LANG.download + '</li>';
          html += '<li id="filepreview"><em class="fa fa-lg fa-eye">&nbsp;</em> ' + LANG.preview + '</li>';

          if ($("span#move_file").attr("title") == "1") {
            html += '<li id="move"><img style="margin-right:5px" src="' + ICON.move + '"/>' + LANG.move + '</li>'
          }
          if ($("span#rename_file").attr("title") == "1") {
            html += '<li id="rename"><em class="fa fa-lg fa-pencil-square-o">&nbsp;</em> ' + LANG.rename + '</li>'
          }
          if ($("span#delete_file").attr("title") == "1") {
            html += '<li id="filedelete"><em class="fa fa-lg fa-trash-o">&nbsp;</em> ' + LANG.upload_delfile + '</li>'
          }
          html += "</ul>";
          $("div#contextMenu").html(html)

        }
      }

      LFILE.setSelFile();


    }

    KEYPR.isFileSelectable = false;
  },

  createfolder :function () {
    $("input[name=createfoldername]").val("");
    $("#modal_createfolder").modal('show');
  },



  preview:function() {
    $("div.dynamic").text("");
    $("input.dynamic").val("");
    var a = $("input[name=selFile]").val(), e = LANG.upload_size + ": ";
    var d = $("img[title='" + a + "']").attr("name").split("|");
    b = $("span#foldervalue").attr("title") ;
    if (d[4] == "image" || d[3] == "swf") {
      var g = calSize(d[1], d[2], 360, 230);
      e += d[1] + " x " + d[2] + " pixels (" + d[5] + ")<br />";

      if(d[4] == "image")
      {
        $("div#fileView").html('<img style="border:2px solid #F0F0F0;" width="' + g[0] + '" height="' + g[1] + '" src="' + ROOT + 'vnt_upload/'+ b + "/" + a + '" />')
      }else{
        $("div#fileView").append("<div class='flash'></div>");
        $("div#fileView .flash").flash({	src : ROOT + "vnt_upload/" + b + "/" + a,		width : g[0],		height : g[1]	}, {	version : 8	});
      }
    } else {
      e += d[5] + "<br />";
      b = $("div[title='" + a + "'] div").html();
      $("div#fileView").html(b);
    }
    e += LANG.pubdate + ": " + d[7];
    $("#fileInfoAlt").html($("img[title='" + a + "']").attr("alt"));
    $("#fileInfoDetail").html(e);
    $("#fileInfoName").html(a);
    $("div#imgpreview").dialog({
      autoOpen : false,
      width : 388,
      modal : true,
      position : "center"
    }).dialog("open").dblclick(function() {
      $("div#imgpreview").dialog("close");
    });
  },
  download :function () {
    var c = $("input[name=selFile]").val();
    var e = $("img[title='" + c + "']").attr("name").split("|");
    p =  $("span#foldervalue").attr("title")  ;
    var link_file = ROOT+'vnt_upload/'+p+'/'+c;
    window.open(link_file );
  },

  filerename :function () {
    $("div.dynamic, span.dynamic").text("");
    $("input.dynamic").val("");
    var a = $("input[name=selFile]").val();
    $("div#filerenameOrigName").text(a);
    $("input[name=filerenameNewName]").val(a.replace(/^(.+)\.([a-zA-Z0-9]+)$/, "$1"));
    $("span[title=Ext]").text("." + a.replace(/^(.+)\.([a-zA-Z0-9]+)$/, "$2"));
    $("input[name=filerenameAlt]").val($("img[title='" + a + "']").attr("alt"));


    $("#modal_filerename").modal('show');

  },
  filedelete :function () {
    alert('Delete file');
  },

  init : function () {

    // Tao folder (Submit)
    $('[name="createfolderOK"]').click(function(){
      var a = $("input[name=createfoldername]").val(), b = $("span[name=current]").attr("title");
      if (a == "" || !namecheck.test(a)) {
        alert(LANG.name_folder_error);
        $("input[name=createfoldername]").focus();
        return false
      }
      $.ajax({
        type : "POST",
        url : remote_url + "createfolder&random=" + randomNum(10),
        data : "path=" + b + "&newname=" + a,
        success : function(d) {

          var e = d.split("_");
          if (e[0] == "ERROR") {
            alert(e[1])
          } else {

            $("#modal_createfolder").modal('hide');
            e = $("select[name=imgtype]").val();
            var h = $("span#path").attr("title");
            var path = h+"/"+d;

            $("#imgfolder").load(remote_url + "folderlist&path=" + h + "&folder=" + d + "&random=" + randomNum(10));
            LFILE.reload(path,'');
          }
        }
      });

    });


    // Doi ten file (Submit)
    $("input[name=filerenameOK]").click(function() {
      var b = $("input[name=selFile]").val();
      var d = $("input[name=filerenameNewName]").val();
      var e = b.match(/^(.+)\.([a-zA-Z0-9]+)$/);
      d = $.trim(d);
      $("input[name=filerenameNewName]").val(d);
      if (d == "") {
        alert(LANG.rename_noname);
        $("input[name=filerenameNewName]").focus();
      } else {
        if (e[1] == d ) {
          $("div#filerename").dialog("close");
        } else {
          p = $("span#foldervalue").attr("title") ;
          $(this).attr("disabled", "disabled");

          $.ajax({
            type : "POST",
            url : remote_url + "renameimg&num=" + randomNum(10),
            data : "path=" + p + "&file=" + b + "&newname=" + d  ,
            success : function(g) {
              var h = g.split("_");
              if (h[0] == "ERROR") {
                alert(h[1]);
                $("input[name=filerenameOK]").removeAttr("disabled");
              } else {

                $("#modal_filerename").modal('hide');

                h = $("select[name=imgtype]").val();
                $("input[name=filerenameOK]").removeAttr("disabled");
                $("div#filerename").dialog("close");
                $("#imglist").load(remote_url + "imglist&path=" + p + "&type=" + h + "&imgfile=" + g  + "&order=" + $("select[name=order]").val() + "&num=" + randomNum(10));
              }
            }
          });
        }
      }
    });

    $(".refresh em").click(function(){
      var a = $("span#foldervalue").attr("title"), b = $("input[name=selFile]").val();
      LFILE.reload(a,b);
      return false
    });

    $(".vchange").change(function() {
      var a = $("span#foldervalue").attr("title"), b = $("input[name=selFile]").val()  ;
      LFILE.reload(a,b);
    });

    KEYPR.init();
    NVLDATA.init();
  }
};

