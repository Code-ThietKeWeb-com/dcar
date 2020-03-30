var postboxes;
(function($) {
					
	postboxes = {
		
		add_postbox_toggles : function(page,args) {
			
			$('.postbox h3, .postbox .handlediv').click( function() {
				 
				var p = $(this).parent('.postbox'), id = p.attr('id');
				p.toggleClass('closed');
				 
				if ( id ) {
						
					if ( !p.hasClass('closed') && $.isFunction(postboxes.vnt_show) ){
						postboxes.vnt_show(id);
					}else if ( p.hasClass('closed') && $.isFunction(postboxes.vnt_hide) ){						
						postboxes.vnt_hide(id);
					}
				}
				
			} );
			 
 			 
		},	


		/* Callbacks */
		vnt_hide :  function(el) {
			$('#'+el).find('.inside').hide('slow');
		},
		
		vnt_show :  function(el) {
			$('#'+el).find('.inside').show('slow');
		}

		
	};

}(jQuery));
