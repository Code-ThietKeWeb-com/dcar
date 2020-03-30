vnTMenu = {
  changePos : function(pos) {
		$.ajax({
			url: "?mod=menu&act=remote&do=change_pos",
			cache: false,
			type: "POST",
			data: ({ 'pos': pos, 'lang': lang }),
			dataType: "html",
			success: function(html)
			{
				$("#ext_parent").html(html) ;
			}
		});

	},
	init:function () {

	}
};

$(document).ready(function () {
  vnTMenu.init();
});


