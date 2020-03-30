
 
/** Top Nav
 **************************************************************** **/
vnTRUST.topNav = function () {
  
};


vnTRUST.vnTFooter = function (){
 
};




/** init Load
 **************************************************************** **/
vnTRUST.init = function () {
  var Xwidth = $(window).width();
  if (Xwidth < 1100) {
    $(".floating-left").hide();
    $(".floating-right").hide()
  }


  $('[data-toggle="tooltip"]').tooltip();

  $(".alert-autohide").delay(5000).slideUp(200, function () {
    $(this).alert('close');
  });

  $(".load_state").change(function() {
    var ext_display = $(this).attr("data-state");

    var mydata =  "do=option_state&city="+ $(this).val();
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

    var mydata =  "do=option_ward&state="+ $(this).val();
    $.ajax({
      type: "GET",
      url: ROOT+'load_ajax.php',
      data: mydata,
      success: function(html){
        $("#"+ext_display).html(html);
      }
    });
  });

 

  $(".menu-category .mc-title").click(function (e) {
    if(! $(this).parents(".menu-category").hasClass("active")){
      $(this).parents(".menu-category").addClass("active");
    }else{
      $(this).parents(".menu-category").removeClass("active");
    }
  });
  $(window).bind("click",function (e) {
    var $clicked = $(e.target);
    if(! $clicked.parents().hasClass("menu-category")){
      $(".menu-category").removeClass("active");
    }
  });


  vnTRUST.topNav();
  vnTRUST.vnTFooter();
  vnTRUST.load_Statistics();
  vnTRUST.goTopStart();

  $(window).resize(function(){
  });

};

/* Init */
jQuery(window).ready(function () {
  vnTRUST.init();
});


