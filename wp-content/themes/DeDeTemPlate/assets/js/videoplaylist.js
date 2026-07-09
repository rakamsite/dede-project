jQuery(document).ready(function ($) {
    let body = $('body');
    function openMediaUploader(button) {
        let selectedVideos = [];
        let video_upload = wp.media({
            title: 'انتخاب کنید',
            button: {
                text: 'انتخاب'
            },
            multiple: false
        });
        video_upload.on('select', function () {
            let attachments = video_upload.state().get('selection').toJSON();
            attachments.forEach(function (attachment) {
                if (selectedVideos.indexOf(attachment.id) === -1) {
                    selectedVideos.push(attachment.id);
                }
            });
            $(button).prev().attr('value', selectedVideos.join(','));
        });
        video_upload.open();
        file_frame.open();
    }

    // اضافه کردن رویداد کلیک به دکمه‌های مورد نظر
    $(body).on('click','.video_playlist_upload_button', function (event) {
        event.preventDefault();
        openMediaUploader(this);
    });
    let counter = $("input#counter")
    body.on('click' , 'button.button_remove_slide', function (event){
        let counter_val = parseFloat(counter.val());
        let newVal = counter_val -= 1 ;
        $(`#${event.target.value}`).remove();
        // counter.attr("value",newVal);
    });
    body.on('click' ,'button#add_video' , function (){
        let counter_val = parseFloat(counter.val());
        let newVal = counter_val += 1 ;

        counter.attr("value",newVal);
        $("div#metaboxes").append(`<div style="background-color:#f0f0f1 ; padding:50px"  id="video_${counter_val}">
            <p>
                <label for="video_playlist_video_id_${counter_val}">انتخاب ویدیو:</label><br>
                <input type="text" id="video_playlist_video_id_${counter_val}" name="video_playlist_video_id_${counter_val}" class="input_take_info"
                       value="" size="50">
                <input type="button" class="button video_playlist_upload_button" value="انتخاب ویدیو">
            </p>
            <p>
                <label for="video_playlist_thumbnail_id_${counter_val}"> انتخاب تامبنیل:</label><br>
                <input type="text" id="video_playlist_thumbnail_id_${counter_val}" name="video_playlist_thumbnail_id_${counter_val}"
                       class="input_take_info" value="" size="50">
                <input type="button" class="button video_playlist_upload_button" value="انتخاب تامبنیل">
            </p>
            <p>
                <label for="video_playlist_title_id_${counter_val}">عنوان ویدئو</label><br>
                <input type="text" id="video_playlist_title_id_${counter_val}" name="video_playlist_title_id_${counter_val}"
                       value="" size="50">
            </p>
            <button type="button" class="button button_remove_slide" value="video_${counter_val}">
                حذف این اسلاید
            </button>
        </div>`);
    });

});
