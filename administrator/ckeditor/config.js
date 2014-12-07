/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

	config.allowedContent = true;
	/*
	config.filebrowserBrowseUrl = 'http://namplus.local/administrator/ckeditor/elfinder/elfinder.html?mode=file';
    config.filebrowserImageBrowseUrl = 'http://namplus.local/administrator/ckeditor/elfinder/elfinder.html?mode=image';
    config.filebrowserFlashBrowseUrl = 'http://namplus.local/administrator/ckeditor/elfinder/elfinder.html?mode=flash';
    config.filebrowserWindowHeight = '500';
	*/
	config.filebrowserBrowseUrl = 'http://namplus.com.vn/administrator/ckeditor/elfinder/elfinder.html?mode=file';
    config.filebrowserImageBrowseUrl = 'http://namplus.com.vn/administrator/ckeditor/elfinder/elfinder.html?mode=image';
    config.filebrowserFlashBrowseUrl = 'http://namplus.com.vn/administrator/ckeditor/elfinder/elfinder.html?mode=flash';
    config.filebrowserWindowHeight = '500';
    
	config.extraPlugins = 'youtube';
};
