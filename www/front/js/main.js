jQuery(document).ready(function(){

    $('#limitForm select').change(function(){
        $('#limitForm').submit();
    });

    var sudoSlider = $("#slider").sudoSlider({
        effect: "fade",
        auto:true,
        prevNext:false
    });

    $('.filter-toggle').on('click', function(element){
        element.preventDefault();
        $('.filterBox .content').toggle();
    })

    $('.carousel').carousel();

    $('.date span').tooltip();
    $('.colorBox-a').tooltip();

    $('.calendar-content').emoticonize()

});