/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	//config.uiColor = '#AADC6E';
    //CKEDITOR.config.language = 'en'; 
    //config.extraPlugins = 'youtube,tableresize,slideshow,backup';
    config.enterMode = CKEDITOR.ENTER_BR;
    config.height = '350';
    
    config.filebrowserBrowseUrl = 'http://namplus.local/ckeditor/elfinder/elfinder.html?mode=file';
    config.filebrowserImageBrowseUrl = 'http://namplus.local/ckeditor/elfinder/elfinder.html?mode=image';
    config.filebrowserFlashBrowseUrl = 'http://namplus.local/ckeditor/elfinder/elfinder.html?mode=flash';
    config.filebrowserWindowHeight = '500';
    

};
