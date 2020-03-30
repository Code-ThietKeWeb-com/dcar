CKEDITOR.plugins.add( 'mega_image', {

  // The plugin initialization logic goes inside this method.
  init: function( editor ) {

    // Define the editor command that inserts a timestamp.
    editor.addCommand( 'insertMega_image', {
      // Define the function that will be fired when the command is executed.
      exec: function( editor ) {
        // var now = new Date();
        //
        // // Insert the timestamp into the document.
        // editor.insertHtml( 'The current date and time is: <em>' + now.toString() + '</em>' );
        // console.log(editor);
        // editor.insertHtml( '122121' );
        editor_gallery = editor;

        //tb_show('Gallery Image',gallery_link);

        $.fancybox.open({
          type : 'iframe',
          src : gallery_link ,
          opts : {
            iframe : {
              css : {
                width: '1020px',
                height: '520px'
              }
            },
            afterClose : function() {

            }
          }
        },{
          toolsbar:false,
          smallBtn : true,
          baseClass : 'popupMedia'
        });

        //window.open(gallery_link ,'targetWindow','toolbar=no')
      }
    });

    // Create the toolbar button that executes the above command.
    editor.ui.addButton( 'Mega_image', {
      label: 'Insert Gallery Image',
      command: 'insertMega_image',
      toolbar: 'insert',
      icon : this.path + 'icons/icon.png'
    });
  }
});