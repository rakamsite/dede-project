jQuery(function ($) {
    const $storyWrapper = $("#story-wrapper");
    const story = $('.story');
    const body = $('body')
    const storyModalContainer = $('#story_player_main_container');
    const story_video_container = $("#story_video_container");
    const close_story_button = '.close_story_button';
    const story_content_expert = $("#story_content_expert");
    const story_like_count = $("#story_like_count");
    const story_comment_count = $("#story_comment_count");
    const story_like_button = $("#story_like_button")
    const story_comments_button = $("#story_comments_button")
    const story_comment_close_button = ".story_comment_close_button";
    const story_comment_section = $("#story_comment_section")
    const video_call_to_action_button = $("#video_call_to_action_button");
    const story_range_video_timer = $("#story_range_video_timer");
    const story_timer = $("#story_timer");
    const story_bottom_cover = $("#story_bottom_cover")
    const ajax_url = admin.url;
    const comment_drawer_container = $("#story_comment_drawer_main");
    const story_submit_comment_button = $("#story_submit_comment_button");
    const story_comment_content = $("#story_comment_content");
    const story_submit_comment_container = $("#story_submit_comment_container");
    const story_submit_comment_drawer_close = ".story_submit_comment_drawer_close";
    const story_submit_comment_final_button = $("#story_submit_comment_final_button");
    const comment_area_section = $("#comment_area_section");
    const after_submited_comment_section = "#after_submited_comment_section";
    const loading_element = '    <div role="status" class="v2_absolute v2_top-0 v2_w-full v2_h-full v2_bg-white v2_flex v2_justify-center v2_items-center loading_spiner_dede">\n' + '        <svg aria-hidden="true" class="v2_w-36 aspect-square v2_text-white animate-spin v2_fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>\n' + '        <span class="sr-only">Loading...</span>\n' + '    </div>\n';
    const comment_drawer_options = {
        placement: 'center-center',
        backdrop: false,
        bodyScrolling: false,
        edge: false,
        edgeOffset: '',
        backdropClasses: '',
        onHide: () => {
        },
        onShow: () => {
        },
        onToggle: () => {
        },
    };
    
    let video_id;
    const storyModalOptions = {
        placement: 'center',
        backdrop: 'dynamic',
        backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-20',
        closable: true,
        bodyScrolling:false,
        onHide : ()=>{
            story_video_container.find("video")[0].pause();
        }
    };
    const story_modal = new Modal(storyModalContainer[0], storyModalOptions);
    const story_comment_drawer = new Drawer(comment_drawer_container[0], comment_drawer_options);
    const story_submit_comment_drawer = new Drawer(story_submit_comment_container[0], comment_drawer_options);
    
    let isDragging = false;
    let startX;
    let scrollLeft, scrollAmount = 200;
    let wrapperWidth = $storyWrapper.width();
    let wrapperChildrenWidth=0;
    $storyWrapper.children().each(function(index,el) {
        wrapperChildrenWidth += el.offsetWidth;
    })
    if (wrapperChildrenWidth < wrapperWidth){
        $storyWrapper.addClass("justify-center")
    }
    $storyWrapper.on("touchstart", function (e) {
        isDragging = true;
        startX = e.originalEvent.touches[0].pageX;
        scrollLeft = $storyWrapper.scrollLeft();
    });
    
    $storyWrapper.on("touchend", function () {
        isDragging = false;
    });
    
    $storyWrapper.on("touchmove", function (e) {
        if (!isDragging) return;
        const x = e.originalEvent.touches[0].pageX;
        const walk = (x - startX) * 1;
        $storyWrapper.scrollLeft(scrollLeft - walk);
    });
    $("#story-scroll-right").on("click", function () {
        $storyWrapper.animate({scrollLeft: $storyWrapper.scrollLeft() - scrollAmount}, 300);
    });
    
    $("#story-scroll-left").on("click", function () {
        $storyWrapper.animate({scrollLeft: $storyWrapper.scrollLeft() + scrollAmount}, 300);
    });
    
    story.on('click tap', function () {
        story_video_container.html(loading_element)
        story_like_count.html(0);
        story_comment_count.html(0);
        story_range_video_timer.val(0)
        story_modal.show();
        $.ajax({
            type: "POST", url: ajax_url, data: {
                action: "story_player_ajax", post_id: this.value
            }, success: function (res) {
                const status = res.success;
                const data = res.data;
                if (status) {
                    video_id = data.video_id;
                    let likes = formatLikeCommentCount(data.video_likes)
                    let comments = formatLikeCommentCount(data.video_comments)
                    story_content_expert.html(`<h4>${data.video_title ?? ''}</h4>`)
                    story_content_expert.append(data.video_content ?? '')
                    story_like_count.html(likes);
                    story_comment_count.html(comments);
                    story_like_button.find('svg').addClass(data.video_like_button_style)
                    let video_element = $(`<video class="w-full h-full v2_object-center" autoplay="autoplay" preload="metadata" poster="${data.video_cover}"><source src="${data.video_link}" type="video/mp4"></video>`);
                    story_video_container.html(video_element);
                    video_call_to_action_button.html(data.video_call_to_action_button ?? '');
                    story_range_video_timer.on('change', function () {
                        video_element.get(0).currentTime = this.value;
                    })
                    video_element.on('timeupdate', function () {
                        let currentTime = this.currentTime;
                        story_range_video_timer.val(currentTime);
                        story_timer.text(formatSeconds(currentTime))
                    })
                    
                    video_element.on('loadedmetadata', function () {
                        const duration = this.duration;
                        story_timer.html(formatSeconds(duration));
                        story_range_video_timer.prop('max', Math.floor(duration))
                    })
                    video_element.on('click tap', function () {
                        const videoElement = this;
                        
                        if (videoElement.paused) {
                            videoElement.play().catch(error => {
                                console.error("Playback failed:", error);
                            });
                        } else {
                            videoElement.pause();
                        }
                    });
                }
            }
        })
    })
    
    story_content_expert.on('click tap', function () {
        $(this).toggleClass('v2_h-20 v2_h-80 v2_overflow-hidden v2_overflow-y-auto');
        story_bottom_cover.toggleClass('v2_h-1/3 v2_h-full')
    })
    
    body.on('click tap' ,close_story_button, function () {
        story_video_container.find("video")[0].pause();
        story_modal.hide();
    });
    story_like_button.on("click tap", function () {
        $.ajax({
            url: ajax_url, type: 'POST', data: {
                action: 'story_like_updater', video_id
            }, success: function (res) {
                let status = res.success;
                let data = res.data;
                if (status) {
                    let likes = formatLikeCommentCount(data.like_counter)
                    story_like_count.text(likes);
                    story_like_button.find('svg').removeClass('v2_fill-white');
                    story_like_button.find('svg').addClass('v2_fill-rose-600');
                } else {
                    alert(data.msg)
                }
            }
        })
    })
    story_comments_button.on('click tap', function () {
        story_comment_section.html(loading_element)
        $.ajax({
            url: ajax_url, type: 'POST', data: {
                action: 'story_comment_section_information', video_id
            }, success: function (res) {
                let status = res.success;
                let data = res.data;
                if (status) {
                    story_comment_section.html(data.comment_html_data)
                }
            }
        })
        story_comment_drawer.show();
    })
    body.on('click tap',story_comment_close_button, function () {
        story_comment_drawer.hide();
    })
    story_submit_comment_button.on("click tap", function () {
        story_submit_comment_drawer.show()
    })
    body.on("click tap",story_submit_comment_drawer_close, function () {
        story_submit_comment_drawer.hide();
        comment_area_section.find('#after_submited_comment_section').remove();
        body.addClass("overflow-hidden")
    })
    $('[data-modal-target=LoginRegisterModal]').on('tap click', function () {
        if (story_submit_comment_drawer.isVisible()) {
            $("[modal-backdrop]").removeClass('md:z-30')
        }
    });
    story_submit_comment_final_button.on('click tap' , function () {
        let comment_content = story_comment_content.val();
        if (comment_content !== '') {
            story_submit_comment_container.append(loading_element)
            $.ajax({
                url:ajax_url,
                type : "POST",
                data:{
                    action:'story_comment_insert',
                    comment_content,
                    video_id
                },
                success:function(res){
                    let status = res.success;
                    let data = res.data;
                    if (status){
                        comment_area_section.append("" +
                            "<div id='after_submited_comment_section' class=\"v2_absolute v2_top-0 v2_h-full v2_w-full v2_flex v2_flex-col v2_gap-3 v2_justify-center v2_items-center v2_bg-white v2_z-40\">\n" +
                            "<p>نظر شما با موفقیت ارسال شد</p>" +
                            "<p>و پس از تایید نمایش داده خواهد شد.</p>" +
                            "<button class='v2_flex v2_gap-2 v2_bg-[#2F2483] v2_p-2 v2_px-10 v2_text-white story_submit_comment_drawer_close story_comment_close_button'>" +
                            "<svg width=\"24\" height=\"18\" viewBox=\"0 0 24 18\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n" +
                            "<path d=\"M13.3596 0.47965C13.6811 0.17253 14.1171 0 14.5717 0C15.0262 0 15.4622 0.17253 15.7837 0.47965L23.4981 7.85167C23.8195 8.15888 24 8.57549 24 9.00989C24 9.44429 23.8195 9.8609 23.4981 10.1681L15.7837 17.5401C15.4603 17.8385 15.0273 18.0037 14.5778 17.9999C14.1283 17.9962 13.6984 17.8239 13.3805 17.5202C13.0627 17.2164 12.8824 16.8055 12.8785 16.376C12.8746 15.9465 13.0474 15.5327 13.3596 15.2237L18.0003 10.6481H1.71431C1.25965 10.6481 0.823606 10.4755 0.50211 10.1683C0.180615 9.86106 0 9.44438 0 9.00989C0 8.57541 0.180615 8.15872 0.50211 7.85149C0.823606 7.54426 1.25965 7.37167 1.71431 7.37167H18.0003L13.3596 2.7961C13.0382 2.48889 12.8577 2.07227 12.8577 1.63788C12.8577 1.20348 13.0382 0.786863 13.3596 0.47965Z\" fill=\"white\"/>\n" +
                            "</svg>\n" +
                            "<span>بازگشت</span>" +
                            "</button>"+
                            "</div>\n")
                    }else{
                        alert(data.msg)
                    }
                    story_submit_comment_container.find(".loading_spiner_dede").remove();
                    
                }
            })
        }else if(story_comment_content.length === 0){
            alert('ابتدا وارد شوید یا ثبت نام کنید.')
        }else{
            alert('نظر خود را بنویسید');
        }
    })
})

function formatSeconds(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.ceil(seconds % 60);
    return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
}
function formatLikeCommentCount(num) {
    if (num >= 1_000_000) {
        return (num / 1_000_000).toFixed(1).replace(/\.0+$/, '') + 'M';
    } else if (num >= 1_000) {
        return (num / 1_000).toFixed(1).replace(/\.0+$/, '') + 'K';
    }
    return num.toString();
}