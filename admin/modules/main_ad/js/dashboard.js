var ajaxWidgets, ajaxPopulateWidgets;

jQuery(document).ready( function($) {
	// These widgets are sometimes populated via ajax
	ajaxWidgets = [
		'rss_news'
	];

	ajaxPopulateWidgets = function(el) {
		show = function(id, i) {
			var p, e = $('#' + id + ' div.inside:visible').find('.widget-loading');
			if ( e.length ) {
				p = e.parent();
				setTimeout( function(){
					p.load('modules/main_ad/widget-extra.php?do=' + id, '', function() {
						p.hide().slideDown('normal', function(){
							$(this).css('display', '');
							
						});
					});
				}, i * 500 );
			}
		}
		if ( el ) {
			el = el.toString();
			if ( $.inArray(el, ajaxWidgets) != -1 )
				show(el, 0);
		} else {
			$.each( ajaxWidgets, function(i) {
				show(this, i);
			});
		}
	};
	ajaxPopulateWidgets();

	postboxes.add_postbox_toggles('dashboard', { pbshow: ajaxPopulateWidgets } );


} );
