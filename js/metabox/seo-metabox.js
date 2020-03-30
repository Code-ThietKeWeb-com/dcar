function highlight(content,kw) {
        //pattern = new RegExp('(>[^<.]*)(' + kw + ')([^<.]*)','gim');
        //pattern = new RegExp( "(^\|[ \n\r\t.,'\"\+!?:-]+)("+kw+")($\|[ \n\r\t.,'\"\+!?:-]+)", 'gim' );
        pattern = new RegExp(kw,'gim');
        replaceWith = '<strong>$&</strong>';
        highlighted = content.replace(pattern,replaceWith);
    return highlighted;
}
function locdau(str) {
   str= str.toLowerCase();  
   str= str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");  
   str= str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");  
   str= str.replace(/ì|í|ị|ỉ|ĩ/g,"i");  
   str= str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");  
   str= str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");  
   str= str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");  
   str= str.replace(/đ/g,"d");  
   str= str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_/g,"-"); 
 /* tìm và thay thế các kí tự đặc biệt trong chuỗi sang kí tự - */ 
   str= str.replace(/-+-/g,"-"); //thay thế 2- thành 1- 
   str= str.replace(/^\-+|\-+$/g,"");  
 //cắt bỏ ký tự - ở đầu và cuối chuỗi  
   return str;  
 } 
 
function yst_clean( str, cleanalphanumeric ) { 
	if ( str == '' || str == undefined )
		return '';
	
	try {
		if ( cleanalphanumeric == true )
			str = str.replace(/[^a-zA-Z0-9\s]/, '');
		str = str.replace(/<\/?[^>]+>/gi, ''); 
		str = str.replace(/\[(.+?)\](.+?\[\/\\1\])?/, '');
	} catch(e) {}
	return str;
}

function ptest(str, p) {
	//str = yst_clean( str, true ); 
	str = str.toLowerCase();
	//alert(p);
	var r = str.match(p);
	if (r != null)
		return '<span class="good">YES ('+r.length+')</span>';
	else
		return '<span class="wrong">NO</span>';
}

function testFocusKw() {
	// Retrieve focus keyword and trim
	var focuskw = jQuery.trim( jQuery('#metakey').val() );
	focuskw = focuskw.toLowerCase();
	
	var postname = jQuery('#friendly_url').val();
	var url	= wpseo_permalink_template.replace('%postname%', postname).replace('http://','');
	url = postname;
	//var p = new RegExp("(^\|[ \n\r\t.,'\"\+!?:-]+)"+focuskw+"($\|[ \n\r\t.,'\"\+!?:-]+)",'gim');
	var p = new RegExp(focuskw,'gim');
	ld = locdau(focuskw);
//	var p2 = new RegExp(ld.replace(/\s+/g,"[-_\\\//]"),'gim');
	var p2 = new RegExp(ld,'gim');
//	alert(p2);
	//alert(jQuery('#metadesc').val());
	if (focuskw != '') {
		html = '';
		html += '<div class="article_heading"><div class="label">Article Heading</div>' + ptest( jQuery('#'+wpseo_title).val(), p ) + '</div>';
		html += '<div class="page_url"><div class="label">Page URL</div>' + ptest( url, p2 ) + '</div>';
		html += '<div class="page_title"><div class="label">Page title</div>' + ptest( jQuery('#wpseosnippet .wpseo_title').text(), p ) + '</div>';
		html += '<div class="meta_desc"><div class="label">Meta description</div>' + ptest( jQuery('#metadesc').val(), p ) + '</div>';
		html += '<div class="content_result"><div class="label">Content</div>' + ptest( jQuery('#'+wpseo_content).val(), p ) + '</div>';
		jQuery('#focuskwresults').html(html);
	}
}

function updateTitle( force ) {
	
	if ( jQuery("#friendly_title").val() ) {
		var title = jQuery("#friendly_title").val();
	} else {
		var title =  jQuery('#'+wpseo_title).val();
	}
	
	
	if ( title == '' )
		return;

	title = jQuery('<div />').html(title).text();

	if ( force ) 
		jQuery('#friendly_title').val( title );

	title = yst_clean( title );
	title = jQuery.trim( title );

	if ( title.length > 70 ) {
		var space = title.lastIndexOf( " ", 67 );
		title = title.substring( 0, space ).concat( ' <strong>...</strong>' );
	}

	var len = 70 - title.length;
	if (len < 0)
		len = '<span class="wrong">'+len+'</span>';
	else
		len = '<span class="good">'+len+'</span>';

	title = boldKeywords( title, false );
	
	jQuery('#wpseosnippet .wpseo_title').html( title );		
	jQuery('#friendly_title-length').html( len );
	
	jQuery('#title_mxh').html( title );
	
	testFocusKw();
}

function updateDesc( desc ) {
	var autogen 	= false;
	var desc 		= jQuery.trim( yst_clean( jQuery("#metadesc").val() ) );
	var color 		= '#000';

	if ( desc == '' ) {
		if ( wpseo_metadesc_template != '' ) {
			var excerpt = yst_clean( jQuery("#excerpt").val() );
			desc = wpseo_metadesc_template.replace('%%excerpt_only%%', excerpt);
			desc = desc.replace('%%excerpt%%', excerpt);
		}

		desc = jQuery.trim ( desc );

		if ( desc == '' ) {
			desc = jQuery("#"+wpseo_content).val();
			desc = yst_clean( desc );
			var focuskw = jQuery.trim( jQuery('#metakey').val() );
			if ( focuskw != '' ) {
				var descsearch = new RegExp( focuskw, 'gim');
				if ( desc.search(descsearch) != -1 ) {
					desc = desc.substr( desc.search(descsearch), wpseo_meta_desc_length );
				} else {
					desc = desc.substr( 0, wpseo_meta_desc_length );
				}
			} else {
				desc = desc.substr( 0, wpseo_meta_desc_length );
			}
			var color = "#888";
			autogen = true;			
		}
	}

	if ( !autogen )
		var len = wpseo_meta_desc_length - desc.length;
	else
		var len = wpseo_meta_desc_length;
		
	if (len < 0)
		len = '<span class="wrong">'+len+'</span>';
	else
		len = '<span class="good">'+len+'</span>';

	if ( autogen || desc.length > wpseo_meta_desc_length ) {
		var space = desc.lastIndexOf( " ", ( wpseo_meta_desc_length - 3 ) );
		desc = desc.substring( 0, space ).concat( ' <strong>...</strong>' );
	}

	desc = boldKeywords( desc, false );

	jQuery('#metadesc-length').html(len);
	jQuery("#wpseosnippet .desc span.content").css( 'color', color );
	jQuery("#wpseosnippet .desc span.content").html( desc );
	jQuery("#description_mxh").html( desc );
	testFocusKw();
}

function updateURL() {
	var name = jQuery('#friendly_url').val();
	if(name){
		var url	= wpseo_permalink_template.replace('%postname%', name).replace('http://','');
		//url = name;
		url = boldKeywords( url, true );
		jQuery("#wpseosnippet .wpseo_url").html( url );
		jQuery("#link_mxh").html( url );
	}
	testFocusKw();
}

function boldKeywords( str, url ) {
    focuskw = jQuery.trim( jQuery('#metakey').val() );

    if ( focuskw == '' )
        return str;

    if ( focuskw.search(' ') != -1 ) {
        var keywords 	= focuskw.split(' ');
    } else {
        var keywords	= new Array( focuskw );
    }
    for (var i=0;i<keywords.length;i++) {
        var kw		= yst_clean( keywords[i] );
        if ( url ) {
            var kw 	= kw.replace(' ','-').toLowerCase();
            kwregex = new RegExp( "([-/])("+kw+")([-/])?" );
        } else {
            kwregex = new RegExp( "(^|[ \s\n\r\t\.,'\(\"\+;!?:\-]+)("+kw+")($|[ \s\n\r\t\.,'\)\"\+;!?:\-]+)", 'gim' );
        }
        str 	= str.replace( kwregex, "$1<strong>$2</strong>$3" );
    }
    return str;
}


function updateSnippet() {
	updateURL();
	updateTitle();
	updateDesc();
}

jQuery(document).ready(function(){	
	// Tabs, based on code by Pete Mall - https://github.com/PeteMall/Metabox-Tabs
	jQuery('.wpseo-metabox-tabs li a').each(function(i) {
		var thisTab = jQuery(this).parent().attr('class').replace(/active /, '');

		if ( 'active' != jQuery(this).attr('class') )
			jQuery('div.' + thisTab).hide();

		jQuery('div.' + thisTab).addClass('wpseo-tab-content');

		jQuery(this).click(function(){
			// hide all child content
			jQuery(this).parent().parent().parent().children('div').hide();

			// remove all active tabs
			jQuery(this).parent().parent('ul').find('li.active').removeClass('active');

			// show selected content
			jQuery(this).parent().parent().parent().find('div.'+thisTab).show();
			jQuery(this).parent().parent().parent().find('li.'+thisTab).addClass('active');
		});
	});

	jQuery('.wpseo-heading').hide();
	jQuery('.wpseo-metabox-tabs').show();
	// End Tabs code
	
	jQuery('#related_keywords_heading').hide();
	
	var cache = {}, lastXhr;
 
	jQuery('#metakey').keyup( function() {
		updateTitle();	
		updateDesc();
		updateURL();	
	});
	jQuery('#friendly_title').keyup( function() {
		updateTitle();		
	});
	jQuery('#metadesc').keyup( function() {
		updateDesc();
	});
	jQuery('#excerpt').keyup( function() {
		updateDesc();
	});
	
	jQuery('#friendly_title').live('change', function() {
		updateTitle();
	});
	jQuery('#metadesc').live('change', function() {
		updateDesc();
	});
	jQuery('#metakey').live('change', function() {
		jQuery('#wpseo_relatedkeywords').show();
		jQuery('#wpseo_tag_suggestions').hide();
		jQuery('#related_keywords_heading').hide();
	});
	jQuery('#excerpt').live('change', function() {
		updateDesc();
	});
	jQuery('#'+wpseo_content).live('change', function() {
		updateDesc();
	});
	jQuery('#friendly_url').keyup( function() {
		updateURL();
	});
	jQuery('#tinymce').live('change', function() {
		updateDesc();
	});
	jQuery('#titlewrap #title').live('change', function() {
		updateTitle();
	});
	jQuery('#wpseo_regen_title').click(function() {
		updateTitle(1);
		return false;
	});
 
	
	updateSnippet();
});