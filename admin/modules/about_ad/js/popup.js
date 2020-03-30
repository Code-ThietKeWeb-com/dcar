
var vnTRUST = {

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


  init: function () {

  }

};



function edInsertContent(myField, myValue) {
  //IE support
  if (document.selection) {
    myField.focus();
    sel = document.selection.createRange();
    sel.text = myValue;
    myField.focus();
  }
  //MOZILLA/NETSCAPE support
  else if (myField.selectionStart || myField.selectionStart == '0') {
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    myField.value = myField.value.substring(0, startPos)
      + myValue
      + myField.value.substring(endPos, myField.value.length);
    myField.focus();
    myField.selectionStart = startPos + myValue.length;
    myField.selectionEnd = startPos + myValue.length;
  } else {
    myField.value += myValue;
    myField.focus();
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

