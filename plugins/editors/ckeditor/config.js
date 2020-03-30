/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
var imagesTemplatePath =  ROOT + "vnt_upload/ckeditor/" ;
var temp_content = [];
$.ajax({
  url: ROOT +"plugins/editors/ckeditor/vnt_template.php",
  cache: false,
  dataType: "json",
  type: "post",
  data: 'lang=vn',
  success: function (data) {
    for (var i = 0; i < data.length ; i++) {
      temp_content[i] = data[i];
    }
  }
});


CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:

    config.toolbar = 'Normal';
    config.entities = false;

    config.toolbarGroups = [
        { name: 'document', groups: [ 'document', 'doctools', 'mode'] },
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
        { name: 'forms', groups: [ 'forms' ] },
        '/',
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
        { name: 'links', groups: [ 'links' ] },
        { name: 'insert', groups: [ 'insert' ] },
        '/',
        { name: 'styles', groups: [ 'styles' ] },
        { name: 'colors', groups: [ 'colors' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'others', groups: [ 'others' ] },
        { name: 'about', groups: [ 'about' ] }
    ];

    
    // config.uiColor = '#AADC6E';
    config.removeButtons = 'Save,NewPage,Preview,Print,Cut,Copy,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Outdent,Indent,BidiLtr,BidiRtl,Language,Flash,Smiley,SpecialChar,About,Subscript,Superscript';
    config.font_names = 'Arial/Arial, Helvetica, sans-serif;Courier New/Courier New, Courier, monospace;Georgia/Georgia, serif;Tahoma/Tahoma, Geneva, sans-serif;Times New Roman/Times New Roman, Times, serif;Verdana/Verdana, Geneva, sans-serif;Roboto/Roboto, sans-serif;Open Sans/Open Sans, sans-serif';

    config.extraPlugins = 'youtube,autolink,mega_image';
    config.contentsCss = ROOT + 'vnt_upload/ckeditor/ck_style.css';

};