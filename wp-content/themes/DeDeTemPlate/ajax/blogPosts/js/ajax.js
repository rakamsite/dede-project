jQuery(document).ready(function ($){
    let paged = 2;
    let lodmore_animation = $('.loadmore-animation');
    function loadMorePosts(post_type ='post') {
        $.ajax({
            url: ajax_admin.ajax_url, // متغیر جی‌کوئری وردپرس که به آدرس دسترسی دارد
            type: 'post',
            data: {
                action: 'load_more_posts',
                post_type,
                paged: paged,
            },
            beforeSend:function () {
              lodmore_animation.removeClass('hidden')
            },
            success: function (response) {
                lodmore_animation.addClass('hidden')
                $('#blog_posts').append(response);
                paged++;
            },
        });
    }
    $('.load-more-button').on('click', function () {
        const post_type = $(this).data('value');
        loadMorePosts(post_type);
    });
});