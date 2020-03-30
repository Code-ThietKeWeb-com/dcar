vnTMailList = {


  do_send_maillist : function () {
    if (selected_item()){

      var arr_ids= new Array();
      var numItem =0;
      $("#manage tbody :input:checkbox").each(function () {
        var c = $(this).attr('checked');
        if (c) {
          arr_ids.push($(this).val());
          numItem=numItem+1;
        }
      });

      var ids = arr_ids.join(',');
      location.href="?mod=mail_list&act=mail_list&sub=send_mail&ids="+ids;
    }
  },

  init:function () {


  }
};

$(document).ready(function () {
  vnTMailList.init();
});


