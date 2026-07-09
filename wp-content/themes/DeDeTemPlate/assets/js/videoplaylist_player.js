jQuery(document).ready(function ($) {
    let mediaVideo = $("#player_main_style").get(0);
    let customPlayPauseBtn = $("button#custom_play_pause");
    let title_video = $('#title_holder');
    let player_main_style = $('#player_main_style');

    function togglePlayPauseButton() {
        if (mediaVideo.paused) {
            customPlayPauseBtn.removeClass('hidden');
            title_video.removeClass('hidden');
        } else {
            customPlayPauseBtn.addClass('hidden');
            title_video.addClass('hidden');
        }
    }

    customPlayPauseBtn.on("click tap", function () {
        let post_id = $(this).val();
        $.ajax({
            url: ajax_admin.ajax_url,
            type: 'post',
            data: {
                action: 'increase_video_views',
                post_id,
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
        });
        $(this).addClass('hidden');
        if (mediaVideo.paused) {
            mediaVideo.play();
        } else {
            mediaVideo.pause();
        }
    });
    $('body').on('play', mediaVideo ,  function () {

    })
    mediaVideo.addEventListener("play", function () {
        mediaVideo.controls = !mediaVideo.controls;
        togglePlayPauseButton();
    });
    mediaVideo.addEventListener("pause", function () {
        mediaVideo.controls = !mediaVideo.controls;
        togglePlayPauseButton();
    });
    togglePlayPauseButton();

    $('.selected-video').on('click tap' , function (){
        video_url = $(this).attr('data-video-url');
        video_thumbnail = $(this).attr('data-video-thumbnail');
        video_title = $(this).attr('data-video-title');
        player_main_style.attr('src' , video_url);
        player_main_style.attr('poster' , video_thumbnail);
        title_video.text(video_title);
        togglePlayPauseButton();
    })
});
