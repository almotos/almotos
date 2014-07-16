CKEDITOR.editorConfig = function( config )
{
    config.replaceClass = 'editor';
    config.resize_enabled = false;
    config.toolbarCanCollapse = false;
    config.language = 'en';
    config.uiColor = '#999';
    config.toolbar = 'Estandar';
    config.toolbar_Estandar =
    [
        ['Cut','Copy','Paste','PasteText','PasteFromWord'],
        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
        ['TextColor','BGColor','-','Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
        ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['Link','Unlink','Anchor'],
        ['Image','Flash','Table','HorizontalRule','SpecialChar']
    ];
};
