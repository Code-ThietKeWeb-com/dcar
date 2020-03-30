jQuery(document).ready(function($) {

	var arr_notifi = {
    "contact": "?mod=contact&act=contact&lang="+lang,
    "order": "?mod=contact&act=contact&lang="+lang
  };

  $.each(arr_notifi, function( key, value ) {
    $("#g_"+key).parents('.menu-item').append('<div class="notifi" id="notifi_'+key+'"><a href = "javascript:;" onclick="window.location.href=\''+value+'\'"><span>0</span></a></div>');
  });


  var arr_menu = [] ;
  $('#admin-menu .sub-menu').each(function () {
    menu_name  = $(this).attr('id');
    menu_name  = menu_name.replace('g_', '');
    if(arr_notifi[menu_name]){
      arr_menu.push(menu_name);
		}
  });


  var mydata =  "menu_list="+arr_menu.toString();
  $.ajax({
    dataType: 'json',
    url: "ajax.php?do=notifi",
    type: 'POST',
    data: mydata ,
    success: function (data) {
      $.each(data, function( key, value ) {
        var i_bell = (value > 0) ? 'fa fa-bell' : 'fa fa-bell-slash';
        $("#notifi_"+key+" span").html('<i class="'+i_bell+'" aria-hidden="true"></i>');
        $("#m-"+key).append('<div class="notifi-child"><span>'+value+'</span></div>');
      });
    }
  });

	
});