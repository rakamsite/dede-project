jQuery(function ($) {
    const _video_type = $("#_video_type"),
        _video_vertical_link = $("#_video_vertical_link").parent().parent(),
        _video_horizontal_link = $("#_video_horizontal_link").parent().parent();
        _video_horizontal_link.slideUp();
        _video_vertical_link.slideUp();
        if (_video_type.val() !== ''){
            video_direction_control(_video_type.val())
        }
        _video_type.on("change", function () {
            video_direction_control(this.value)
        })
    function video_direction_control(value){
        if (_video_vertical_link.prop('class').includes(value)) {
            _video_vertical_link.slideDown();
            _video_horizontal_link.slideUp();
        }else {
            _video_vertical_link.slideUp();
            _video_horizontal_link.slideDown();
        }
    }
    $('.related_post_select_2').select2({
        placeholder: 'محصول موردنظر را جستجو کنید...',
        allowClear: true,
        multiple: true,
        width: '100%',
    });
});