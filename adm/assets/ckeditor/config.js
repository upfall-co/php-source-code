/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.language = 'ko';
    config.filebrowserUploadUrl = '/adm/assets/ckeditor/editor_upload_v2.php' ;
//    config.filebrowserUploadMethod = 'form';
    config.extraPlugins = 'lineheight,youtube,editorplaceholder';
    config.skin = 'bootstrapck';

    config.youtube_responsive = true;
    //Use old embed code:
    config.youtube_older = false;
    config.youtube_privacy = false;
    config.youtube_autoplay = false;
    config.youtube_controls = true;
    config.youtube_disabled_fields = ['txtEmbed', 'chkAutoplay'];
    config.removeButtons = 'Underline,Maximize,Easy,UploadImage,ImageUploader,Save,Html5video,Flash,Button,ImageButton,Select,Form,CheckBox,image,Iframe,SImage';
    config.removeDialogTabs = 'link:upload';
    config.removePlugins = 'iframe,CustomImageUploader,SImage';
    config.height = 700;

    config.contentsCss = [
        'https://webfontworld.github.io/pretendard/Pretendard.css'
    ];

    config.font_names = 'Pretendard/Pretendard, sans-serif;' + config.font_names;
};

