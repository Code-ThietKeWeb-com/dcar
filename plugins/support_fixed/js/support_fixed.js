$(document).ready(function(){
    $(".support-hotline .div_title").click(function(){
      if(! $(this).parents(".support-hotline").hasClass("show")){
        $(this).parents(".support-hotline").addClass("show");
      }else{
        $(this).parents(".support-hotline").removeClass("show");
      }
    });     
});