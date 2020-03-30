vnTAbout = {


  showAddInfo:function(item_id) {
    var title = 'Thêm bước thực hiện' ;
    return eModal
      .iframe('?mod=about&act=remote&do=add_info&item_id='+item_id, title)
      .then(function () {  setTimeout(function(){


      }, 100);  });
  },

  showEditInfo:function(id) {
    var title = 'Cập nhật bước thực hiện' ;
    return eModal
      .iframe('?mod=about&act=remote&do=edit_info&id='+id, title)
      .then(function () {  setTimeout(function(){


      }, 100);  });
  },

  callBackInfo:function(id,mess) {
    eModal.close();
    jAlert(mess,'Thông báo', function () {
      location.reload();
    });
  },

  del_Item:function(type,id){

    if (confirm('Bạn có chắc muốn xóa ?')) {
      $("#"+type+"_"+id).remove();
    }
    else {
      alert ('Phew~');
    }
  },

  init:function () {


    $(".list-choose").find("li").click(function(){
      var obj = $(this).closest('.list-choose') ;
      data = [];
      if($(this).hasClass("checked")) {
        $(this).removeAttr("class");
      } else {
        $(this).addClass("checked");
      }
      obj.find('li').each(function(i, e){
        if($(this).hasClass("checked")) {
          id = $(this).attr('rel');
          data.push(id);
        }
      });
      data.join();
      obj.find('.input-value').val(data);

    });

    $('[data-popup="eModal"]').click(function () {
      var type = $(this).data('type');
      var title = $(this).data('title');
      var url = $(this).data('url');

      return eModal
        .iframe(url,title)
        .then(function () {  setTimeout(function(){

        }, 100);  });
    });

  }
};

$(document).ready(function () {
  vnTAbout.init();
});


