$(function(){

    $.nette.ext({
        init: function(){
            $('button[data-toggle=popover]').popover();
        },

        complete: function(){
            $('button[data-toggle=popover]').popover();
        }
    })

    tinymce.init({
        language: 'cs',
        selector:'textarea',
        height: 180,
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    });
    $.nette.init();




});
