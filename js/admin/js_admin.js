var inFormOrLink = true;
var mailfilter = /^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,6}$/;
var numcheck = /^([0-9])+$/;
var namecheck = /^([a-zA-Z0-9_-])+$/;
var md5check = /^[a-z0-9]{32}$/;
var imgexts = /^.+\.(jpg|gif|png|bmp)$/;
var iChars = "!@#$%^&*()+=-[]\\\';,./{}|\":<>?";
var specialchars = /\$|,|@|#|~|`|\%|\*|\^|\&|\(|\)|\+|\=|\[|\-|\_|\]|\[|\}|\{|\;|\:|\'|\"|\<|\>|\?|\||\\|\!|\$|\./g
var ie45, ns6, ns4, dom;
if (navigator.appName == "Microsoft Internet Explorer") ie45 = parseInt(navigator.appVersion) >= 4;
else if (navigator.appName == "Netscape") {
  ns6 = parseInt(navigator.appVersion) >= 5;
  ns4 = parseInt(navigator.appVersion) < 5;
}
dom = ie45 || ns6;



function is_array(mixed_var) {
  return ( mixed_var instanceof Array );
}

// strip_tags('<p>Kevin</p> <b>van</b> <i>Zonneveld</i>', '<i><b>');
function strip_tags(str, allowed_tags) {
  var key = '', allowed = false;
  var matches = [];
  var allowed_array = [];
  var allowed_tag = '';
  var i = 0;
  var k = '';
  var html = '';

  var replacer = function (search, replace, str) {
    return str.split(search).join(replace);
  }
  // Build allowes tags associative array
  if (allowed_tags) {
    allowed_array = allowed_tags.match(/([a-zA-Z0-9]+)/gi);
  }

  str += '';

  // Match tags
  matches = str.match(/(<\/?[\S][^>]*>)/gi);

  // Go through all HTML tags
  for (key in matches) {
    if (isNaN(key)) {
      // IE7 Hack
      continue;
    }

    // Save HTML tag
    html = matches[key].toString();

    // Is tag not in allowed list ? Remove from str !
    allowed = false;

    // Go through all allowed tags
    for (k in allowed_array) {
      // Init
      allowed_tag = allowed_array[k];
      i = -1;

      if (i != 0) {
        i = html.toLowerCase().indexOf('<' + allowed_tag + '>');
      }
      if (i != 0) {
        i = html.toLowerCase().indexOf('<' + allowed_tag + ' ');
      }
      if (i != 0) {
        i = html.toLowerCase().indexOf('</' + allowed_tag);
      }

      // Determine
      if (i == 0) {
        allowed = true;
        break;
      }
    }

    if (!allowed) {
      str = replacer(html, "", str);
      // Custom replace. No regexing
    }
  }

  return str;
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
  for (i = 0; i < l; i++) {
    if (whitespace.indexOf(str.charAt(i)) === -1) {
      str = str.substring(i);
      break;
    }
  }

  l = str.length;
  for (i = l - 1; i >= 0; i--) {
    if (whitespace.indexOf(str.charAt(i)) === -1) {
      str = str.substring(0, i + 1);
      break;
    }
  }

  return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}

// rawurlencode('Kevin van Zonneveld!'); = > 'Kevin%20van%20Zonneveld%21'
function rawurlencode(str) {

  str = (str + '').toString();
  return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%5B').replace(/\)/g, '%5D').replace(/\*/g, '%2A');
}

// rawurldecode('Kevin+van+Zonneveld%21'); = > 'Kevin+van+Zonneveld!'
function rawurldecode(str) {
  return decodeURIComponent(str);
}

function is_numeric(mixed_var) {
  return !isNaN(mixed_var);
}

function intval(mixed_var, base) {
  var type = typeof (mixed_var );

  if (type === 'boolean') {
    return (mixed_var) ? 1 : 0;
  } else if (type === 'string') {
    tmp = parseInt(mixed_var, base || 10);
    return (isNaN(tmp) || !isFinite(tmp)) ? 0 : tmp;
  } else if (type === 'number' && isFinite(mixed_var)) {
    return Math.floor(mixed_var);
  } else {
    return 0;
  }
}

function randomNum(a) {
  for (var b = "", d = 0; d < a; d++) {
    b += "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890".charAt(Math.floor(Math.random() * 62));
  }
  return b
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
function is_numeric(a) {
  return ( typeof a === "number" || typeof a === "string" ) && a !== "" && !isNaN(a);
}
function is_num(event, f) {
  if (event.srcElement) {
    kc = event.keyCode;
  } else {
    kc = event.which;
  }
  if ((kc < 47 || kc > 57) && kc != 8 && kc != 0) return false;
  return true;
}
function showhide(id) {
  el = document.all ? document.all[id] : dom ? document.getElementById(id) : document.layers[id];
  els = dom ? el.style : el;
  if (dom) {
    if (els.display == "none") {
      els.display = "";
    } else {
      els.display = "none";
    }
  }
  else if (ns4) {
    if (els.display == "show") {
      els.display = "hide";
    } else {
      els.display = "show";
    }
  }
}

function getobj(id) {
  el = document.all ? document.all[id] : dom ? document.getElementById(id) : document.layers[id];

  return el;
}


function selected_item_cus(fName) {
  var f = document.getElementById(fName);
  var name_count = f.length;
  for (i = 0; i < name_count; i++) {
    if (f.elements[i].checked) {
      return true;
    }
  }
  alert(lang_js['please_chose_item']);
  return false;
}

function do_submit_cus(action, fName) {

  var f = document.getElementById(fName);
  f.do_action.value = action;
  if (selected_item_cus(fName)) {
    f.submit();
  }
}

function do_movecat(action) {
  cat_chose = $('#cat_chose').val();
  if (cat_chose == 0) {
    alert('Vui lòng chọn danh mục cần chuyển đến');
  } else {
    document.manage.do_action.value = cat_chose;
    if (selected_item()) {
      document.manage.submit();
    }
  }
}


function select_row(row_id) {
  cur_class = document.getElementById(row_id).className;
  if (cur_class == "row_select") {
    document.getElementById(row_id).className = "row0";

  } else {
    document.getElementById(row_id).className = "row_select";
  }

}


// send html to the post textbox
function send_to_textbox(ojb, text) {
  $("#" + ojb).val(text);
  tb_remove();
}

// send html to the post textbox
function send_to_modules(ojb, text, thumb) {
  $("#ext_" + ojb).html('<img src="' + thumb + '" alt=""   /> <a href="javascript:del_picture(\'' + ojb + '\')" class="del">Xóa</a>');
  $("#" + ojb).val(text);
  $("#btnU_" + ojb).hide();
  tb_remove();
}

// send html to the send_to_object textbox
function send_to_object(ojb, text, pic) {
  if (pic) {
    $("#ext_" + ojb).html('<img src="' + pic + '" alt="" /> <a href="javascript:del_picture(\'' + ojb + '\')" class="del">Xóa</a>');
  }
  $("#" + ojb).val(text);
  tb_remove();
}

function del_picture(ojb) {
  $("#ext_" + ojb).html('');
  $("#" + ojb).val('');
  $("#btnU_" + ojb).show();
}


// do_check
function do_check(id) {

  $("#table_list tbody :input:checkbox").each(function () {
    var row_id = 'row_' + $(this).val();
    if (id == $(this).val()) {
      $('#' + row_id).addClass('row_select');
      $(this).attr('checked', 'checked');
      $(this).parent().addClass('checked');
    }
  });
}


// confirm_item
function confirm_item(theURL,mess) {
  if (confirm(mess)) {
    window.location.href=theURL;
  }
  else {
    alert ('Phew~');
  }
}

// del_item
function del_item(theURL) {
  if (confirm(lang_js['are_you_sure_del'])) {
    window.location.href = theURL;
  }
  else {
    alert('Phew~');
  }
}

// selected_item
function selected_item() {
  var ok = 0;
  $("#manage tbody :input:checkbox").each(function () {
    var c = $(this).attr('checked');
    if (c) {
      ok = 1;
    }
  });
  if (ok) {
    return true;
  } else {
    alert(lang_js['please_chose_item']);
    return false;
  }
}

function del_selected(action) {
  if (selected_item()) {
    question = confirm(lang_js['are_you_sure_del'])
    if (question != "0") {
      $("#manage").attr("action", action);
      $("#manage").submit();
    } else {
      alert('Phew~');
    }
  }

}

function action_selected(action, mess) {

  if (selected_item()) {

    if (mess != '') {
      question = confirm(mess);
      if (question != "0") {
        $("#manage").attr("action", action);
        $("#manage").submit();
      } else {
        alert('Phew~');
      }

    } else {
      $("#manage").attr("action", action);
      $("#manage").submit();
    }

  }

}

function update_selected(action) {
  if (selected_item()) {
    document.manage.action = action;
    document.manage.submit();
  }
}

function do_edit(action) {
  if (selected_item()) {
    for (i = 0; i < document.manage.elements.length; i++) {
      if (document.manage.elements[i].type == "checkbox" && document.manage.elements[i].name != "all" && document.manage.elements[i].checked == true) {
        id = document.manage.elements[i].value;
        break;
      } else {
        id = 1;
      }
    }
    action = action + '&id=' + id;
    document.manage.action = action;
    document.manage.submit();
  }
}

function do_submit(action) {
  inFormOrLink = true;
  document.manage.do_action.value = action;
  if (selected_item()) {
    document.manage.submit();
  }

}

//do_chkUpload
function do_ChoseUpload(objName, id) {
  $("." + objName + " :input:radio").each(function () {
    if (id == $(this).val()) {
      $(this).attr('checked', 'checked');
    }
  });
}





/*MXH*/
vnTMXH = {

  setTitle: function (text) {
    var link_seo = $("#link_seo").text();
    $("#friendly_title").val(text);
    $("#picturedes").val(text);
    $(".title_mxh").text(text);
    $.ajax({
      dataType: 'json',
      url: "ajax.php?do=friendly_url",
      type: 'POST',
      data: 'text=' + text,
      success: function (data) {
        $("#friendly_url").val(data.html);
        link_mxh = link_seo.replace("xxx", data.html);
        $(".link_mxh").text(link_mxh);
        updateURL();
        updateTitle();
      }
    });

  },

  setFriendlyUrl: function (text) {
    var link_seo = $("#link_seo").text();
    var link_mxh = link_seo.replace("xxx", text);
    $(".link_mxh").text(link_mxh);
  },
  setFriendlyTitle: function (text) {
    $(".title_mxh").text(text);
  },
  setMetaDesc: function (text) {
    $(".description_mxh").text(text);
  }


};


/*Core vnTRUST*/

var vnTRUST = {


  confirm_redirect: function (mess, url) {
    jConfirm(mess, 'Confirm', function (r) {
      if (r) {
        location.href = url;
      }
    });

  },


  // send html to the post textbox
    send_to_modules :function (ojb, text, thumb) {
    $("#ext_" + ojb).html('<img src="' + thumb + '" alt=""   /> <a href="javascript:del_picture(\'' + ojb + '\')" class="del">Xóa</a>');
    $("#" + ojb).val(text);
    $("#btnU_" + ojb).hide();
      tb_remove();
      $.fancybox.close();
  },

  // send html to the send_to_object textbox
   send_to_object :function (ojb, text, pic) {
    if (pic) {
      $("#ext_" + ojb).html('<img src="' + pic + '" alt="" /> <a href="javascript:del_picture(\'' + ojb + '\')" class="del">Xóa</a>');
    }
    $("#" + ojb).val(text);
     tb_remove();
     $.fancybox.close();
  },


  loadPopupMedia: function (obj, module, folder_browse) {
    /*var url = '?mod=media&act=popup_gallery&module=' + module + '&folder=' + folder_browse + '&type=image&obj=' + obj + '&TB_iframe=true&width=1050&height=520';
    tb_show('Chọn hình từ thư viện', url);*/


    var url = '?mod=media&act=popup_media&stype=gallery&module=' + module + '&folder=' + folder_browse + '&type=image&obj=' + obj  ;
    $.fancybox.open({
      src : url,
      type : 'iframe',
      title: 'Add an Image',
      smallBtn : true,
      opts : {
        iframe : {
          css : {
            width: '1100px',
            height: '520px'
          }
        },
        afterClose : function() {

        }
      }
    });

  },

  update_SortGallery : function(obj) {
    $( "#"+obj +" .div_sort").trigger('sortupdate');
  },


  initLoadGallery : function  (obj){

    $( "#"+obj).sortable({
      update: function(event, ui) {
        $( "#"+obj+" > div" ).each(function (e) {
          stt = (e+1) ;
          $(this).attr("data_count", stt);
          $(this).find('.pic_order').val(stt);
        });
      }
    });

    $( "#"+obj ).on('sortupdate',function() {
      $( "#"+obj+" > div" ).each(function (e) {
        stt = (e+1) ;
        $(this).attr("data_count", stt);
        $(this).find('.pic_order').val(stt);
      });
    });

    $("#"+obj+" .remove").click(function(){
      $(this).closest('.pic_item').remove();
      setTimeout(function () {
        vnTRUST.update_SortGallery(obj);
      }, 100);
    });

  },



  callbackGallery: function (obj, module,list_img) {
    //alert('obj ='+obj+' list_img = '+list_img);

    var arr_img = list_img.split("|");
    var pic_len = parseInt($(".pic_item").length) ;
    for (i in arr_img ) {
      stt = parseInt(i)+1;
      var picture = arr_img[i] ;
      var src = ROOT+'vnt_upload/'+picture ;

      if(module) {
        picture = picture.replace(module+"/", "");
      }

      var pic_order = parseInt(pic_len) + parseInt(stt) ;

      html_pic = '<div class="pic_item" data_count="'+pic_order+'" >';
      html_pic += '<a href="javascript:void(0);" class="remove" title="Delete">&nbsp;</a>';
      html_pic += '<div class="img"><img src="'+src+'"   alt="" /></img></div>';
      html_pic += '<input name="pictures[]" type="hidden" value="'+picture+'"/>';

      html_pic += '<input name="pic_name[]" type="text" value="" placeholder="Nhập tên hình" class="form-control"/>';
      html_pic += '<input class="pic_order" name="pic_order[]"   type="hidden" value="'+pic_order+'"/>';
      html_pic += '</div>';
      $('#'+obj).append(html_pic);
    }

    $("#"+obj+" .remove").click(function(){
      $(this).closest('.pic_item').remove();
      setTimeout(function () {
        vnTRUST.update_SortGallery(obj);
      }, 100);

    });


    tb_remove();
    $.fancybox.close();
  },


  vnTUpload: function (obj ,options) {
    var uploader_obj = "uploader_" + obj;
    uploader_obj = new plupload.Uploader(options);

    uploader_obj.bind('Init', function (up, params) {
    });

    uploader_obj.bind('FilesAdded', function (up, files) {
      for (var i in files) {
        $('#uploader_'+obj+' .upload-result' ).append('<div id="' + files[i].id + '">' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ') <b></b> <a href="javascript:void(0)" class="remove" title="Xóa">Xóa</a><input type="hidden" id="file_attach_' + files[i].id + '" name="file_attach' + obj + '[]" value=""  class="file_attach' + obj + '" /></div>');
        $('#' + files[i].id + ' a.remove').click(function () {
          uploader_obj.removeFile(files[i]);
          $('#' + files[i].id).hide();
          return false;
        });

      }
      setTimeout(function () { up.start(); }, 100);
    });

    uploader_obj.bind('UploadProgress', function (up, file) {
      document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
    });


    uploader_obj.bind('FileUploaded', function (up, file, info) {
      var result = $.parseJSON(info.response);
      $('#uploader_'+obj+' .upload-result' ).html('') ;
      $("#" + obj).val(result['file_src']);
      $('#uploader_'+obj+' .del-file' ).html('<i class="fa fa-trash"></i>').click(function () {
        $("#" + obj).val('');
        $(this).html('');
        return false;
      });

    });

    uploader_obj.init();
  },



  doUploadFile : function (obj) {

    var input_file = $("#input_"+obj);
    var countFiles = input_file[0].files.length;
    var imgPath = input_file[0].value;
    var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
    var obj_result = $("#upload_"+obj).find('.upload-result-view');
    var cur_files = obj_result.find(".file").length;
    var num_file = cur_files + countFiles ;


    if (typeof(FileReader) != "undefined") {
      //loop for each file selected for uploaded.
      for (var i = 0; i < countFiles; i++)
      {

        var html_file = '';
        var product_reader = new FileReader();
        product_reader.onload = function(e) {
          data_src = e.target.result ;
          /*$("<img />", {
            "src": e.target.result,
            "class": "thumb-image"
          }).appendTo(image_holder);*/

          html_file = '<div class="file" style="background-image:url('+data_src+')"><span class="close"><i class="fas fa-times-circle"></i></span></div>';
          obj_result.append(html_file);
        };
        obj_result.show();
        product_reader.readAsDataURL(input_file[0].files[i]);
        //image_holder.appendTo(html_file);
      }
    } else {
      obj_result.html("<p>This browser does not support FileReader.</p>");
    }



    if(num_file>5){
      alert('Chá»‰ Ä‘Æ°á»£c up tá»‘i Ä‘a 5 hÃ¬nh') ;
    }else{





      setTimeout(function(){
        $(".files-holder .file .close").click(function(){
          $(this).parent().remove();
          $("#btn-reviews-file").show();
        });
      }, 300);

      if(num_file==5) {
        $("#btn-reviews-file").hide();
      }
    }



  },



  delFileUpload : function  (obj){
    $("#upload_"+obj).removeClass('has-file');
    $("#"+obj).val('');
    $("#upload_"+obj+' .upload-result-view').html('');
  },


  init: function () {

    //upload hình
    $(".btnBrowseMedia").click(function () {
      var obj = $(this).attr("data-obj");
      var module = $(this).attr("data-mod");
      var folder = $(this).attr("data-folder");
      var type = $(this).attr("data-type");
      /*var url_popup = '?mod=media&act=popup_media&module=' + module + '&folder=' + folder + '&obj=' + obj + '&type=' + type + '&TB_iframe=true&width=900&height=474';
      tb_show('Add an Image', url_popup);*/

      var url_popup = '?mod=media&act=popup_media&module=' + module + '&folder=' + folder + '&obj=' + obj + '&type=' + type  ;
      $.fancybox.open({
        type : 'iframe',
        src : url_popup ,
        opts : {
          iframe : {
            css : {
              width: '1020px',
              height: '520px'
            }
          },
          afterClose : function() {

          }
        }
      },{
        toolsbar:false,
        smallBtn : true,
        baseClass : 'popupMedia'
      });

      return false;
    });


    $(".load_city").change(function() {
      var ext_display = $(this).attr("data-city");

      var mydata =  "do=option_city&country="+ $(this).val()+"&lang="+lang;
      $.ajax({
        type: "GET",
        url: ROOT+'load_ajax.php',
        data: mydata,
        success: function(html){
          $("#"+ext_display).html(html);
        }
      });
    });

    $(".load_state").change(function() {
      var ext_display = $(this).attr("data-state");

      var mydata =  "do=option_state&city="+ $(this).val()+"&lang="+lang;
      $.ajax({
        type: "GET",
        url: ROOT+'load_ajax.php',
        data: mydata,
        success: function(html){
          $("#"+ext_display).html(html);
        }
      });
    });

    $(".load_ward").change(function() {
      var ext_display = $(this).attr("data-ward") ;

      var mydata =  "do=option_ward&state="+ $(this).val()+"&lang="+lang;
      $.ajax({
        type: "GET",
        url: ROOT+'load_ajax.php',
        data: mydata,
        success: function(html){
          $("#"+ext_display).html(html);
        }
      });
    });



  }

};

/*======BEGIN THEM======*/
var isMobile = false; //initiate as false
// device detection
if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
  || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) isMobile = true;

$(document).ready(function () {

  // sidebar admin menu

  $('#menu-expand').click(function () {
    $('#admin-menu li.menu-item').each(function () {

      submenu = $(this).find('.sub-menu');
      submenu.hide(150);

      img_menu = $(this).find('.menu-toggle img');
      img_menu.attr({
        src: DIR_IMAGE + "/but_cong.gif",
        title: "Expand",
        alt: "but_cong"
      })

    });

  });

  $('#menu-collapse').click(function () {

    $('#admin-menu li.menu-item').each(function () {

      submenu = $(this).find('.sub-menu');
      submenu.show(150);

      img_menu = $(this).find('.menu-toggle img');
      img_menu.attr({
        src: DIR_IMAGE + "/but_tru.gif",
        title: "Collapse",
        alt: "but_tru"
      })
    });

  });


  /*==BEGIN: Chỉnh sửa==*/
  $('.menu-title h2').click(function () {
    menu = $(this).parent().parent().parent();
    if (!$(this).parent().hasClass("active")) {
      $(".menu-item .menu-title").each(function (e) {
        if ($(this).hasClass("active")) {
          $(this).removeClass("active");
          $(this).parents(".menu-item").find(".sub-menu").stop().slideToggle(500);
        }
      });
      $(this).parent().addClass("active");
    } else {
      $(this).parent().removeClass("active");
    }
    submenu = menu.find('.sub-menu');
    submenu.stop().slideToggle(500);


  });
  /*==END: Chỉnh sửa==*/
  $('.menu-toggle img').click(function () {

    menu = $(this).parent().parent().parent();
    submenu = menu.find('.sub-menu');
    submenu.slideToggle(150);
    if ($(this).attr("src") == DIR_IMAGE + "/but_cong.gif") {
      $(this).attr({
        src: DIR_IMAGE + "/but_tru.gif",
        title: "Collapse",
        alt: "but_tru"
      })
    } else {
      $(this).attr({
        src: DIR_IMAGE + "/but_cong.gif",
        title: "Collapse",
        alt: "but_tru"
      })
    }

  });

  // check all checkboxes
  $('#table_list tbody :checkbox').click(function (e) {
    var c = $(this).attr('checked');
    var row_id = 'row_' + $(this).val();
    if (c) {
      $('#' + row_id).addClass('row_select');
    } else {
      $('#' + row_id).removeClass('row_select');
    }

  });

  /*==BEGIN: Chỉnh sửa==*/
  $('#checkall').click(function (e) {
    var c = $(this).attr('checked');

    $(this).parents('form:first').find(':checkbox').attr('checked', function () {
      var row_id = 'row_' + $(this).val();
      if (c) {
        $('#' + row_id).addClass('row_select');
        $('#' + row_id).find(".spancheckbox").addClass("checked");
        return 'checked';
      } else {
        $('#' + row_id).removeClass('row_select');
        $('#' + row_id).find(".spancheckbox").removeClass("checked");
        return false;
      }
    });

  });
  /*==END: Chỉnh sửa==*/
  $('.desc_title').click(function () {
    desc = $(this).parent();
    desc_content = desc.find('.desc_content');
    desc_content.slideToggle(200);
    img = $(this).find('img');
    if (img.attr("src") == DIR_IMAGE + "/toggle_minus.png") {
      img.attr({
        src: DIR_IMAGE + "/toggle_plus.png",
        title: "Expand",
        alt: "bt_add"
      })
    } else {
      img.attr({
        src: DIR_IMAGE + "/toggle_minus.png",
        title: "Collapse",
        alt: "bt_except"
      })
    }

  });


  $('#wrapper-menu').slimScroll({
    height: '100%'
  });

  $('[data-toggle="tooltip"]').tooltip();
  $(".alert-autohide").delay(5000).slideUp(200, function () {
    $(this).alert('close');
  });

  $('#vnt-menuTop a.clickMenu').bind("click", function () {
    if (!$(this).hasClass("disable")) {
      $(this).addClass("disable");
      $("#vnt-menu").css({left: '-225px'});
      $("#vnt-header").css({'margin-left': '0'});
      $("#vnt-content").css({'margin-left': '5px'});
      $("#vnt-footer").css({'margin-left': '0'});
      $("body").css({"background-position": "0 0, 100% 100%"});
      if (isMobile == true) {
        $("html").css({"overflow": "auto"});
      }
    } else {
      $(this).removeClass("disable");
      $("#vnt-menu").css({left: '0'});
      $("#vnt-header").css({'margin-left': '225px'});
      $("#vnt-content").css({'margin-left': '230px'});
      $("#vnt-footer").css({'margin-left': '225px'});
      $("body").css({"background-position": "225px 0, 100% 100%"});
      if (isMobile == true) {
        $("html").css({"overflow": "hidden"});
      }
    }
    return false;
  });

  $(".top_langues .langues_title").click(function () {
    if (!$(this).parents(".top_langues").hasClass("show")) {
      $(this).parents(".top_langues").addClass("show");
    } else {
      $(this).parents(".top_langues").removeClass("show");
    }
  });
  $(".top_admin .admin_title").click(function () {
    if (!$(this).parents(".top_admin").hasClass("show")) {
      $(this).parents(".top_admin").addClass("show");
    } else {
      $(this).parents(".top_admin").removeClass("show");
    }
  });
  $(".div_icon").click(function () {
    if (!$(this).parents("li").hasClass("show")) {
      $(this).parents("li").addClass("show");
    } else {
      $(this).parents("li").removeClass("show");
    }
  });
  $(".selectAction .selectTitle").click(function () {
    if (!$(this).parents(".selectAction").hasClass("show")) {
      $(this).parents(".selectAction").addClass("show");
    } else {
      $(this).parents(".selectAction").removeClass("show");
    }
  });
  $(window).bind("click", function (e) {
    var $clicked = $(e.target);
    if (!$clicked.parents().hasClass("top_langues")) {
      $(".top_langues").removeClass("show");
    }
    if (!$clicked.parents().hasClass("top_admin")) {
      $(".top_admin").removeClass("show");
    }
    if (!$clicked.parents().hasClass("d_messages")) {
      $(".d_messages").removeClass("show");
    }
    if (!$clicked.parents().hasClass("d_bell")) {
      $(".d_bell").removeClass("show");
    }
    if (!$clicked.parents().hasClass("d_gadget")) {
      $(".d_gadget").removeClass("show");
    }
    if (!$clicked.parents().hasClass("selectAction")) {
      $(".selectAction").removeClass("show");
    }
  });

  if (isMobile == false) {
    $(window).resize(function () {
      if (window.innerWidth > 1050) {
        $("#vnt-menuTop a.clickMenu").removeClass("disable");
        $("#vnt-menu").css({left: '0'});
        $("#vnt-header").css({'margin-left': '225px'});
        $("#vnt-content").css({'margin-left': '230px'});
        $("#vnt-footer").css({'margin-left': '225px'});
        $("body").css({"background-position": "225px 0, 100% 100%"});
        if (isMobile == true) {
          $("html").css({"overflow": "auto"});
        }
      } else {
        $("#vnt-menuTop a.clickMenu").addClass("disable");
        $("#vnt-menu").css({left: '-225px'});
        $("#vnt-header").css({'margin-left': '0'});
        $("#vnt-content").css({'margin-left': '5px'});
        $("#vnt-footer").css({'margin-left': '0'});
        $("body").css({"background-position": "0 0, 100% 100%"});
        if (isMobile == true) {
          $("html").css({"overflow": "auto"});
        }
      }
    });
  }

  if (window.innerWidth > 1050) {
    $("#vnt-menuTop a.clickMenu").removeClass("disable");
    $("#vnt-menu").css({left: '0'});
    $("#vnt-header").css({'margin-left': '225px'});
    $("#vnt-content").css({'margin-left': '230px'});
    $("#vnt-footer").css({'margin-left': '225px'});
    $("body").css({"background-position": "225px 0, 100% 100%"});

  } else {
    $("#vnt-menuTop a.clickMenu").addClass("disable");
    $("#vnt-menu").css({left: '-225px'});
    $("#vnt-header").css({'margin-left': '0'});
    $("#vnt-content").css({'margin-left': '5px'});
    $("#vnt-footer").css({'margin-left': '0'});
    $("body").css({"background-position": "0 0, 100% 100%"});
  }
  $("input:checkbox").wrap("<span class='spancheckbox'></span>");
  $("input:checkbox").each(function () {
    if ($(this).is(':checked')) {
      $(this).parents(".spancheckbox").addClass("checked");
    }
  });
  $("input:checkbox").change(function () {
    if ($(this).is(':checked')) {
      $(this).parents(".spancheckbox").addClass("checked");
      return false;
    } else {
      $(this).parents(".spancheckbox").removeClass("checked");
      return false;
    }
  });

  //$('a').bind('click', function() { inFormOrLink = true; });
  $('form').bind('submit', function () {
    inFormOrLink = true;
  });
  $('form').bind('button', function () {
    inFormOrLink = true;
  });
  $('button').bind('click', function () {
    inFormOrLink = true;
  });
  $("input[type=submit] , input[type=button]").bind("click", function () {
    inFormOrLink = true;
  });


  $(window).keydown(function (event) {
    if (event.which == 116) {
      inFormOrLink = true;
    }
  });

  window.onbeforeunload = function () {

    if (inFormOrLink == true) {
      return null;
    } else {
      return "Do you really want to close?";
    }
  };

  vnTRUST.init();
});
/*======END THEM======*/