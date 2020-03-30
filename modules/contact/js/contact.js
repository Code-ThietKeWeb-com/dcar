$(document).ready(function(){
  $(".map-contact .mc-tab").click(function(){
    if(!$(this).parents(".map-contact").hasClass("active")){
      $(this).parents(".map-contact").addClass("active");
    }
    else{
      $(this).parents(".map-contact").removeClass("active");
    }
  });
  $(".list-tab li a").click(function(){
    if($(window).innerWidth()<991){
      $(this).parents(".map-contact").removeClass("active");
      $(this).parents(".map-contact").find(".list-tab").stop().slideUp(500);
    }
  });
  $(".view-map-contact a").click(function(){
    $("html,body").animate({
      scrollTop: $(".map-contact").offset().top
    },1000);
    var target=$(this).attr("href");
    $(".map-contact .list-tab li").removeClass("active");
    $(".map-contact .list-tab li").each(function(){
      if($(this).find("a").attr("href")==target){
        $(this).addClass("active");
        get_hh();
      }
    });
    return false;
  });
  $(".map-contact .list-tab li").click(function(){
    $(this).siblings().removeClass("active");
    $(this).addClass("active");
    get_hh();
  });
  function get_hh(){
    var t = $(".list-tab li.active a").text();
    $(".map-contact .mc-tab").text(t);

    var map_id = $(".list-tab li.active a").data('id');
    if (map_id){
      load_maps(map_id,1170,480);
    }

  }
  get_hh();
});

function load_maps(id,w_map,h_map)
{
  $('.list_maps li').removeClass("active");
  $('#maps'+id).addClass("active");

  $("#ext_maps").html('<div align="center"><img src="'+DIR_IMAGE+'/ajax-loading.gif" alt="Loadding ..." height="100" /><br>Loadding ...</div>');
  $.ajax({
    type: "GET",
    url: ROOT+'modules/contact/ajax/load_map.php',
    data: "id="+id+"&w="+w_map+"&h="+h_map,
    success: function(html){
      $("#ext_maps").html(html);
    }
  });
  return false;
}