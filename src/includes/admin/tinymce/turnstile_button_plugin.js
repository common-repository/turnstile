
(function() {
    tinymce.PluginManager.add('turnstile_button', function( editor, url ) {
        console.log('url tinymce', url);
        editor.addButton( 'turnstile_button', {
            title : 'Turnstile Shortcode', // title of the button
			image : url + "/icon_turnstile_24.png",  // path to the button's image

			onclick : function() {
				var shortcode = "[turnstile_more] Content to hide [/turnstile_more]";
				tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			}
        });
    });
})();

