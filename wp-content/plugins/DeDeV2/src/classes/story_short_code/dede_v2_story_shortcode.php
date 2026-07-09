<?php

namespace classes\story_short_code;

use classes\video_post_type\dede_v2_post_type_main;
use WP_Query;

class dede_v2_story_shortcode
{
    public dede_v2_post_type_main $video_post_type;

    public function __construct()
    {
        $this->video_post_type = new dede_v2_post_type_main();
    }

    function run(): void
    {
        add_shortcode('vid_category', [$this, 'short_code_html_callback']);
        add_action('wp_ajax_nopriv_story_player_ajax', [$this, "story_player_ajax"]);
        add_action('wp_ajax_story_player_ajax', [$this, "story_player_ajax"]);
        add_action('wp_ajax_nopriv_story_like_updater', [$this, "story_like_updater_callback"]);
        add_action('wp_ajax_story_like_updater', [$this, "story_like_updater_callback"]);
        add_action('wp_ajax_nopriv_story_comment_section_information', [$this, "story_comment_section_information_callback"]);
        add_action('wp_ajax_story_comment_section_information', [$this, "story_comment_section_information_callback"]);
        add_action('wp_ajax_nopriv_story_comment_insert', [$this, "story_comment_insert_callback"]);
        add_action('wp_ajax_story_comment_insert', [$this, "story_comment_insert_callback"]);
    }

    function short_code_html_callback($atts): string
    {
        wp_enqueue_style('story_style_shortcode', dede_v2_url . '/assets/css/story.css' , ['main-style-DeDe']);
        wp_enqueue_script('story_script_shortcode', dede_v2_url . '/assets/js/story.js');
        wp_localize_script('story_script_shortcode', 'admin', ['url' => admin_url('admin-ajax.php')]);
        $atts = shortcode_atts([
            'id' => 0
        ], $atts);

        ob_start();

        $get_video_posts = new WP_Query(array(
            'post_type' => 'video',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'date',
            'tax_query' => [
                [
                    'taxonomy' => 'vid_category',
                    'terms' => (int)$atts['id'],
                    'field' => 'term_id',
                    'operator' => 'IN',
                ]
            ]
        ));
        if ($get_video_posts->have_posts()) {
            $stories = '';
            while ($get_video_posts->have_posts()) {
                $get_video_posts->the_post();
                $post_id = get_the_ID();
                $post_name = get_the_title($post_id);
                $circle_cover = (string)get_post_meta($post_id, $this->video_post_type->circle_cover, true);
                $stories .= <<<HTML
<button class="!v2_aspect-square !v2_w-20 !v2_h-20 md:!v2_h-28 md:!v2_w-28 !v2_text-center story !v2_cursor-pointer" value="$post_id" >
    <img src="{$circle_cover}" alt="{$post_name}" style="height: 0; width: 0" class=" !v2_w-20 !v2_h-20 md:!v2_h-28 md:!v2_w-28 v2_aspect-square v2_rounded-full v2_border-2 v2_border-[#2F2483]" />
    <span class="v2_text-xs">$post_name</span>
</button>
HTML;
            }
            $comment_submit_area = '';
            if (!is_user_logged_in()) {
                $comment_submit_area = <<<HTML
                        <div class="flex px-5 justify-between flex-col gap-3 items-center v2_absolute v2_top-1/2 v2_-translate-y-1/2">
                            <p>برای این که بتوانید نظر دهید ، ابتدار لازم است وارد شوید یا ثبت نام کنید.</p>
                                <button data-drawer-hide="mobile-menu" class="bg-[#2F2483] w-fit rounded-lg h-fit text-white flex py-3 px-5 mt-1/2 flex justify-between login_register_page">
                                    <svg width="21" height="26" viewBox="0 0 21 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.3334 10.3333C13.1868 10.3333 15.5 8.02014 15.5 5.16667C15.5 2.3132 13.1868 0 10.3334 0C7.47988 0 5.16669 2.3132 5.16669 5.16667C5.16669 8.02014 7.47988 10.3333 10.3334 10.3333Z" fill="white"></path>
                                        <path d="M20.6667 20.0208C20.6667 23.2306 20.6667 25.8333 10.3333 25.8333C0 25.8333 0 23.2306 0 20.0208C0 16.8111 4.62675 14.2083 10.3333 14.2083C16.0399 14.2083 20.6667 16.8111 20.6667 20.0208Z" fill="white"></path>
                                    </svg>
                                    <span class="mr-2">ورود به حساب کاربری </span>
                                </button>
                            </div>
                        HTML;
            } else {
                $comment_submit_area = <<<HTML
                <textarea id="story_comment_content" name="story_comment_content" class="w-full border border-black rounded-lg p-4" rows="8" aria-required="true" placeholder="بنویسید ... *"></textarea>
HTML;

            }
            echo <<<HTML
<div id='story_player_main_container' class='hidden flex v2_justify-center v2_items-center v2_top-0 v2_right-0 v2_fixed w-full h-full v2_z-50'>
    <div class="v2_bg-white md:v2_aspect-[9/16] v2_h-full v2_w-full md:v2_w-auto md:v2_h-[700px] v2_relative overflow-hidden v2_z-40 v2_max-h-screen">
        <div id="story_comment_drawer_main" class="v2_absolute v2_h-full v2_w-full v2_bg-white v2_z-40 transition-transform -translate-x-full overflow-y-auto v2_flex v2_flex-col v2_justify-between">
            <button class="v2_w-full v2_flex v2_justify-start v2_p-5 story_comment_close_button v2_sticky v2_top-0 bg-white">
               <svg width="38" height="30" viewBox="0 0 38 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                   <path d="M21.1528 0.799417C21.6618 0.28755 22.352 0 23.0718 0C23.7915 0 24.4818 0.28755 24.9908 0.799417L37.2053 13.0861C37.7141 13.5981 38 14.2925 38 15.0165C38 15.7405 37.7141 16.4348 37.2053 16.9469L24.9908 29.2335C24.4789 29.7309 23.7932 30.0061 23.0815 29.9999C22.3699 29.9937 21.6891 29.7065 21.1858 29.2003C20.6826 28.6941 20.3971 28.0092 20.3909 27.2933C20.3847 26.5775 20.6583 25.8878 21.1528 25.3728L28.5004 17.7469H2.71433C1.99444 17.7469 1.30404 17.4592 0.795008 16.9471C0.285973 16.4351 0 15.7406 0 15.0165C0 14.2923 0.285973 13.5979 0.795008 13.0858C1.30404 12.5738 1.99444 12.2861 2.71433 12.2861H28.5004L21.1528 4.66017C20.6439 4.14815 20.358 3.45379 20.358 2.72979C20.358 2.00579 20.6439 1.31144 21.1528 0.799417Z" fill="#3F3F3F"/>
               </svg>
            </button>
            <div id="story_comment_section" class=""></div>
            <button id="story_submit_comment_button" class="v2_sticky v2_p-4 v2_bg-[#2F2483] v2_flex v2_justify-between v2_items-center w-full text-white v2_bottom-0">
                <div class="v2_flex v2_items-center v2_gap-2">
                    <svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.0225 0C13.9243 0 11.8467 0.426766 9.9082 1.25593C7.96972 2.0851 6.20838 3.30043 4.72473 4.83252C1.72837 7.92674 0.0450347 12.1234 0.0450347 16.4993C0.0324141 20.3089 1.30971 24.0033 3.65595 26.9433L0.460449 30.2432C0.238749 30.4752 0.0885671 30.7698 0.0288527 31.0899C-0.0308617 31.4099 0.00256742 31.7411 0.124922 32.0416C0.257628 32.3385 0.472758 32.5879 0.743006 32.7583C1.01325 32.9287 1.32643 33.0123 1.64278 32.9985H16.0225C20.26 32.9985 24.3239 31.2602 27.3203 28.166C30.3167 25.0718 32 20.8751 32 16.4993C32 12.1234 30.3167 7.92674 27.3203 4.83252C24.3239 1.73831 20.26 0 16.0225 0ZM16.0225 29.6987H5.49336L6.97926 28.1643C7.27684 27.8551 7.44388 27.4369 7.44388 27.0011C7.44388 26.5652 7.27684 26.147 6.97926 25.8379C4.88715 23.6798 3.58433 20.8395 3.29276 17.8007C3.0012 14.762 3.73894 11.7128 5.38028 9.17281C7.02162 6.63277 9.46502 4.75898 12.2942 3.87068C15.1234 2.98239 18.1633 3.13454 20.896 4.30122C23.6287 5.4679 25.8852 7.57692 27.281 10.269C28.6767 12.961 29.1254 16.0695 28.5507 19.0649C27.9759 22.0603 26.4132 24.7572 24.1288 26.6962C21.8443 28.6352 18.9796 29.6963 16.0225 29.6987Z" fill="white"/>
                    </svg>
                    <p>ارسال نظر</p>
                </div>
                <svg width="23" height="18" viewBox="0 0 23 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.197 17.5203C9.88893 17.8275 9.47113 18 9.0355 18C8.59987 18 8.18207 17.8275 7.87398 17.5203L0.481013 10.1483C0.17302 9.84112 -1.15766e-06 9.42451 -1.11968e-06 8.99011C-1.0817e-06 8.55571 0.17302 8.1391 0.481013 7.83188L7.87398 0.45987C8.18384 0.161454 8.59883 -0.00366909 9.02959 6.36285e-05C9.46035 0.00379635 9.8724 0.176085 10.177 0.479827C10.4816 0.783566 10.6544 1.19446 10.6581 1.62399C10.6619 2.05353 10.4963 2.46735 10.197 2.77632L5.74974 7.35188L21.3571 7.35189C21.7928 7.35189 22.2107 7.52448 22.5188 7.83171C22.8269 8.13894 23 8.55563 23 8.99011C23 9.42459 22.8269 9.84128 22.5188 10.1485C22.2107 10.4557 21.7928 10.6283 21.3571 10.6283L5.74974 10.6283L10.197 15.2039C10.505 15.5111 10.678 15.9277 10.678 16.3621C10.678 16.7965 10.505 17.2131 10.197 17.5203Z" fill="white"/>
                </svg>
            </button>
        </div>
        <div id="story_submit_comment_container" class="v2_absolute v2_h-full v2_w-full v2_bg-white v2_z-40 transition-transform -translate-x-full overflow-y-auto v2_flex v2_flex-col v2_justify-between">
            <button class="v2_w-full v2_flex v2_justify-start v2_p-5 v2_sticky v2_top-0 bg-white story_submit_comment_drawer_close">
               <svg width="38" height="30" viewBox="0 0 38 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                   <path d="M21.1528 0.799417C21.6618 0.28755 22.352 0 23.0718 0C23.7915 0 24.4818 0.28755 24.9908 0.799417L37.2053 13.0861C37.7141 13.5981 38 14.2925 38 15.0165C38 15.7405 37.7141 16.4348 37.2053 16.9469L24.9908 29.2335C24.4789 29.7309 23.7932 30.0061 23.0815 29.9999C22.3699 29.9937 21.6891 29.7065 21.1858 29.2003C20.6826 28.6941 20.3971 28.0092 20.3909 27.2933C20.3847 26.5775 20.6583 25.8878 21.1528 25.3728L28.5004 17.7469H2.71433C1.99444 17.7469 1.30404 17.4592 0.795008 16.9471C0.285973 16.4351 0 15.7406 0 15.0165C0 14.2923 0.285973 13.5979 0.795008 13.0858C1.30404 12.5738 1.99444 12.2861 2.71433 12.2861H28.5004L21.1528 4.66017C20.6439 4.14815 20.358 3.45379 20.358 2.72979C20.358 2.00579 20.6439 1.31144 21.1528 0.799417Z" fill="#3F3F3F"/>
               </svg>
            </button>
            <div id="comment_area_section" class="overflow-y-auto v2_px-5 flex items-center justify-center">
                $comment_submit_area
            </div>
            <button id="story_submit_comment_final_button" class="v2_sticky v2_p-4 v2_bg-[#E3000F] v2_flex v2_justify-between v2_items-center w-full text-white v2_bottom-0">
                <div class="v2_flex v2_items-center v2_gap-2">
                    <svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.0225 0C13.9243 0 11.8467 0.426766 9.9082 1.25593C7.96972 2.0851 6.20838 3.30043 4.72473 4.83252C1.72837 7.92674 0.0450347 12.1234 0.0450347 16.4993C0.0324141 20.3089 1.30971 24.0033 3.65595 26.9433L0.460449 30.2432C0.238749 30.4752 0.0885671 30.7698 0.0288527 31.0899C-0.0308617 31.4099 0.00256742 31.7411 0.124922 32.0416C0.257628 32.3385 0.472758 32.5879 0.743006 32.7583C1.01325 32.9287 1.32643 33.0123 1.64278 32.9985H16.0225C20.26 32.9985 24.3239 31.2602 27.3203 28.166C30.3167 25.0718 32 20.8751 32 16.4993C32 12.1234 30.3167 7.92674 27.3203 4.83252C24.3239 1.73831 20.26 0 16.0225 0ZM16.0225 29.6987H5.49336L6.97926 28.1643C7.27684 27.8551 7.44388 27.4369 7.44388 27.0011C7.44388 26.5652 7.27684 26.147 6.97926 25.8379C4.88715 23.6798 3.58433 20.8395 3.29276 17.8007C3.0012 14.762 3.73894 11.7128 5.38028 9.17281C7.02162 6.63277 9.46502 4.75898 12.2942 3.87068C15.1234 2.98239 18.1633 3.13454 20.896 4.30122C23.6287 5.4679 25.8852 7.57692 27.281 10.269C28.6767 12.961 29.1254 16.0695 28.5507 19.0649C27.9759 22.0603 26.4132 24.7572 24.1288 26.6962C21.8443 28.6352 18.9796 29.6963 16.0225 29.6987Z" fill="white"/>
                    </svg>
                    <p>ثبت نظر</p>
                </div>
                <svg class="v2_w-8 v2_h-8 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                </svg>
            </button>
        </div>
        <div class="v2_absolute v2_top-0 v2_bg-gradient-to-b v2_from-black items-start v2_flex v2_p-4 w-full v2_h-1/5 v2_z-30">
            <button class="close_story_button text-whie">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
                    <path fill="white" d="m12 13.4l-4.9 4.9q-.275.275-.7.275t-.7-.275t-.275-.7t.275-.7l4.9-4.9l-4.9-4.9q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l4.9 4.9l4.9-4.9q.275-.275.7-.275t.7.275t.275.7t-.275.7L13.4 12l4.9 4.9q.275.275.275.7t-.275.7t-.7.275t-.7-.275z"/>
                </svg>
            </button>
        </div>
        <div id="story_video_container" class="h-full w-full"></div>
        <div class="v2_flex v2_flex-col v2_absolute v2_bottom-0 v2_z-10 v2_w-full ">
            <div class="v2_flex v2_px-4">
                <div id="story_content_expert" class="v2_text-white v2_w-full v2_h-20 v2_transition-all v2_overflow-hidden v2_pl-2 v2_text-sm v2_text-justify"></div>
                <div class="v2_self-end v2_flex v2_flex-col v2_gap-1">
                    <button id="story_like_button" class="v2_text-white v2_flex v2_flex-col">
                        <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.1 15.55L10 15.65L9.89 15.55C5.14 11.24 2 8.39 2 5.5C2 3.5 3.5 2 5.5 2C7.04 2 8.54 3 9.07 4.36H10.93C11.46 3 12.96 2 14.5 2C16.5 2 18 3.5 18 5.5C18 8.39 14.86 11.24 10.1 15.55ZM14.5 0C12.76 0 11.09 0.81 10 2.08C8.91 0.81 7.24 0 5.5 0C2.42 0 0 2.41 0 5.5C0 9.27 3.4 12.36 8.55 17.03L10 18.35L11.45 17.03C16.6 12.36 20 9.27 20 5.5C20 2.41 17.58 0 14.5 0Z" />
                        </svg>
                        <span id="story_like_count" class="text-xs text-center w-full">0</span>
                    </button>
                    <button id="story_comments_button" class="v2_text-white v2_flex v2_flex-col ">
                        <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.0282 0C8.71497 0 7.41461 0.258658 6.20135 0.761205C4.9881 1.26375 3.8857 2.00035 2.95712 2.92893C1.08175 4.8043 0.0281864 7.34784 0.0281864 10C0.0202874 12.309 0.819723 14.5481 2.28819 16.33L0.288186 18.33C0.149429 18.4706 0.0554325 18.6492 0.0180584 18.8432C-0.0193158 19.0372 0.0016069 19.2379 0.0781863 19.42C0.161244 19.5999 0.29589 19.7511 0.465033 19.8544C0.634176 19.9577 0.830187 20.0083 1.02819 20H10.0282C12.6804 20 15.2239 18.9464 17.0993 17.0711C18.9746 15.1957 20.0282 12.6522 20.0282 10C20.0282 7.34784 18.9746 4.8043 17.0993 2.92893C15.2239 1.05357 12.6804 0 10.0282 0ZM10.0282 18H3.43819L4.36819 17.07C4.55444 16.8826 4.65898 16.6292 4.65898 16.365C4.65898 16.1008 4.55444 15.8474 4.36819 15.66C3.05877 14.352 2.24336 12.6305 2.06088 10.7888C1.87839 8.94705 2.34013 7.09901 3.36741 5.55952C4.3947 4.02004 5.92398 2.88436 7.6947 2.34597C9.46543 1.80759 11.368 1.8998 13.0784 2.60691C14.7888 3.31402 16.201 4.59227 17.0746 6.22389C17.9482 7.85551 18.2291 9.73954 17.8693 11.555C17.5096 13.3705 16.5315 15.005 15.1017 16.1802C13.672 17.3554 11.8789 17.9985 10.0282 18Z" fill="white"/>
                        </svg>
                         <span id="story_comment_count" class="text-xs text-center w-full">0</span>
                    </button>
                </div>
            </div>
            <div class="v2_w-full v2_px-4 v2_flex v2_gap-2 text-white v2_pb-5">
                <input id="story_range_video_timer" value="0" min="0" step="0.01" max="100" type="range" class="v2_w-full v2_rotate-180 v2_transition-all" />
                <span id="story_timer" class="v2_w-fit">00:00</span>
            </div>
            <div id="video_call_to_action_button"></div>
        </div>
        <div id="story_bottom_cover" class="v2_absolute v2_bottom-0 v2_bg-gradient-to-t v2_from-black v2_flex v2_justify-between v2_p-4 w-full v2_h-1/3"></div>
    </div>
</div>
HTML;

            echo <<<HTML
            <div class="  v2_w-full v2_relative v2_cursor-grab v2_group" id="story-container">
                <div id="story-wrapper" class="v2_relative v2_py-5 v2_flex v2_gap-3 v2_overflow-hidden ">
                    $stories
                </div>
                    <button type="button" id="story-scroll-left"
                            class="v2_transition-opacity v2_absolute v2_top-1/2 v2_-translate-y-1/2 v2_right-0 hidden md:group-hover:v2_flex v2_z-30 v2_items-center v2_justify-center v2_h-full v2_cursor-pointer v2_group focus:v2_outline-none">
                        <span class="v2_inline-flex v2_items-center v2_justify-center v2_w-10 v2_h-10 v2_rounded-full v2_bg-[#2F2483]/80 group-hover:v2_bg-[#2F2483] group-focus:v2_ring-4 group-focus:v2_ring-white group-focus:v2_outline-none">
                            <svg class="v2_w-4 v2_h-4 v2_text-white rtl:v2_rotate-180"
                                 aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="2"
                                      d="M5 1 1 5l4 4"/>
                            </svg>
                            <span class="sr-only">Previous</span>
                        </span>
                    </button>
                    <button type="button" id="story-scroll-right"
                            class=" v2_transition-opacity v2_absolute v2_top-1/2 v2_-translate-y-1/2 v2_left-0 hidden md:group-hover:v2_flex v2_z-30 x v2_items-center v2_justify-center v2_h-full v2_cursor-pointer v2_group focus:v2_outline-none">
                        <span class="v2_inline-flex v2_items-center v2_justify-center v2_w-10 v2_h-10 v2_rounded-full v2_bg-[#2F2483]/80 group-hover:v2_bg-[#2F2483] group-focus:v2_ring-4 group-focus:v2_ring-white group-focus:v2_outline-none">
                            <svg class="v2_w-4 v2_h-4 v2_text-white rtl:v2_rotate-180"
                                 aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="2"
                                      d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="sr-only">Next</span>
                        </span>
                    </button>
            </div>
HTML;
        }

        wp_reset_postdata();

        return ob_get_clean();
    }

    function story_player_ajax(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $video_id = $_POST['post_id'];
            $video = get_post($video_id);
            if ($video) {
                $video_title = $video->post_title;
                $video_content = $video->post_content;
                $video_type = get_post_meta($video_id, $this->video_post_type->video_type, true);
                $video_link = match ($video_type) {
                    'vertical' => get_post_meta($video_id, $this->video_post_type->vertical_link, true),
                    'horizontal' => get_post_meta($video_id, $this->video_post_type->horizontal_link, true),
                    default => '',
                };
                $video_likes = get_post_meta($video_id, $this->video_post_type->video_likes, true);
                $video_likes = empty($video_likes) ? 0 : $video_likes;
                $video_comments = get_comments_number($video_id);
                $video_cover = get_post_meta($video_id, $this->video_post_type->video_cover, true);
                $video_button_text = get_post_meta($video_id, $this->video_post_type->button_text, true);
                $video_button_img = get_post_meta($video_id, $this->video_post_type->button_image, true);
                $video_button_url = get_post_meta($video_id, $this->video_post_type->button_link, true);
                $call_to_action = '';
                $like_button_style = 'v2_fill-white';

                if (is_user_logged_in()) {
                    $user_id = get_current_user_id();
                    $like_button_style = $this->check_user_liked_video($user_id, $video_id) ? 'v2_fill-rose-600' : 'v2_fill-white';
                }

                if (!empty($video_button_text) && !empty($video_button_img) && !empty($video_button_url)) {
                    $call_to_action = <<<HTML
                <a href="$video_button_url" target="_blank" class="flex justify-between px-3 py-2 items-center bg-[#2F2483]">
                    <div class="flex items-center text-white gap-2">
                        <img class="w-10 h-10 aspect-square" src="$video_button_img" alt="$video_button_text">
                        <span>$video_button_text</span>
                    </div>
                    <svg width="23" height="18" viewBox="0 0 23 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.197 17.5203C9.88893 17.8275 9.47113 18 9.0355 18C8.59987 18 8.18207 17.8275 7.87398 17.5203L0.481013 10.1483C0.17302 9.84112 -1.15766e-06 9.42451 -1.11968e-06 8.99011C-1.0817e-06 8.55571 0.17302 8.1391 0.481013 7.83188L7.87398 0.45987C8.18384 0.161454 8.59883 -0.00366909 9.02959 6.36285e-05C9.46035 0.00379635 9.8724 0.176085 10.177 0.479827C10.4816 0.783566 10.6544 1.19446 10.6581 1.62399C10.6619 2.05353 10.4963 2.46735 10.197 2.77632L5.74974 7.35188L21.3571 7.35189C21.7928 7.35189 22.2107 7.52448 22.5188 7.83171C22.8269 8.13894 23 8.55563 23 8.99011C23 9.42459 22.8269 9.84128 22.5188 10.1485C22.2107 10.4557 21.7928 10.6283 21.3571 10.6283L5.74974 10.6283L10.197 15.2039C10.505 15.5111 10.678 15.9277 10.678 16.3621C10.678 16.7965 10.505 17.2131 10.197 17.5203Z" fill="white"/>
                    </svg>
                </a>
HTML;
                }

                $video_data = [
                    'video_id' => $video_id,
                    'video_title' => $video_title,
                    'video_content' => $video_content,
                    'video_link' => $video_link,
                    'video_likes' => $video_likes,
                    'video_comments' => $video_comments,
                    'video_cover' => $video_cover,
                    'video_call_to_action_button' => $call_to_action,
                    'video_like_button_style' => $like_button_style
                ];
                wp_send_json_success($video_data);
            }
            wp_send_json_error('Error:No1');
        } else {
            wp_send_json_error("نه نشد");
        }
    }

    function story_like_updater_callback(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $video_id = intval($_POST['video_id']);
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                $liked_video = $this->check_user_liked_video($user_id, $video_id);

                if ($liked_video) {
                    wp_send_json_error(['msg' => 'شما قبلا این ویدیو را لایک کردید .']);
                } else {
                    $liked = get_user_meta($user_id, $this->video_post_type->liked_videos, true);
                    if (!is_array($liked)) {
                        $liked = [];
                    }

                    $liked[$video_id] = true;
                    $like_video = update_user_meta($user_id, $this->video_post_type->liked_videos, $liked);

                    if ($like_video) {
                        $current_likes = intval(get_post_meta($video_id, $this->video_post_type->video_likes, true));
                        $new_likes = $current_likes + 1;
                        update_post_meta($video_id, $this->video_post_type->video_likes, $new_likes);

                        wp_send_json_success(['like_counter' => $new_likes]);
                    } else {
                        wp_send_json_error(['msg' => 'مشکلی در هنگام لایک کردن . دوباره امتحان کنید.']);
                    }
                }
            } else {
                wp_send_json_error(['msg' => 'برای لایک کردن نیاز هست که ابتدا وارد شوید یا ثبت نام کنید.']);
            }
        } else {
            wp_send_json_error('Error:No1');
        }
    }

    function story_comment_section_information_callback(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $video_id = intval($_POST['video_id']);

            wp_send_json_success(['comment_html_data' => $this->get_post_comments_html($video_id)]);
        }
    }

    function get_post_comments_html($post_id): bool|string
    {
        $post_id = intval($post_id);
        if (get_post_status($post_id) === false) {
            return '<p class="v2_px-5">پستی با این شناسه یافت نشد.</p>';
        }
        if (post_password_required($post_id)) {
            return '<p class="v2_px-5">این پست رمزدار است و امکان نمایش کامنت‌ها وجود ندارد.</p>';
        }
        $comments = get_comments(array(
            'post_id' => $post_id,
            'status' => 'approve',
        ));
        if (empty($comments)) {
            return '<div class="v2_h-1/2 v2_w-full v2_flex v2_items-center v2_justify-center v2_absolute v2_top-1/2 v2_-translate-y-1/2">هیچ کامنتی برای این پست وجود ندارد.</div>';
        }
        ob_start();
        echo '<div id="comments" class="comments-area mx-auto w-full h-full v2_overflow-y-auto mt-2 divide-y-2">';
        echo '<ul class="space-y-4">';
        wp_list_comments(array(
            'callback' => [$this, 'custom_comment_tailwind'],
            'style' => 'ul',
            'short_ping' => true,
            'avatar_size' => 0,
            'max_depth' => 3,
            'reverse_top_level' => false,
        ), $comments);
        echo '</ul>';
        echo '<div class="comment-pagination mt-2 flex justify-center gap-2 [&_a]:bg-[#2F2483]/80 [&_span]:bg-[#2F2483] [&_*]:text-white [&_*]:p-2 [&_*]:rounded p-4 ">';
        paginate_comments_links(array(
            'prev_text' => '« قبلی',
            'next_text' => 'بعدی »',
        ));
        echo '</div>';
        echo '</div>';
        return ob_get_clean();
    }

    function custom_comment_tailwind($comment, $args, $depth): void
    {
        $GLOBALS['comment'] = $comment;
        $is_admin = user_can($comment->user_id, 'administrator');
        $user = wp_get_current_user();
        if ($is_admin){
            $comment_display_name = "DeDe.ir";
        }else{
            $comment_display_name = $user->first_name . ' ' . $user->last_name;
        }
        ?>
        <li id="comment-<?php comment_ID(); ?>"
            class="rounded-lg p-4 space-y-2 border-b <?php echo $depth > 1 ? 'ml-8 border-l-2 border-gray-300 pl-4' : ''; ?>">
            <span class="<?php echo $is_admin ? 'bg-[#2F2483] text-white' : 'bg-gray-300'; ?> px-1 rounded">
                <?php echo $comment_display_name; ?>
            </span>
            <div class="comment-body text-gray-700 text-sm leading-relaxed">
                <p class="<?php echo $is_admin ? 'text-[#2F2483]' : ''; ?>">
                    <?php echo wp_kses_post($comment->comment_content); ?>
                </p>
            </div>
        </li>
        <?php
    }

    function story_comment_insert_callback(): void
    {
        if (is_user_logged_in()) {
            $comment_content = sanitize_textarea_field($_POST['comment_content']);
            $video_id = intval($_POST['video_id']);
            $user = wp_get_current_user();
            if(!empty($user->first_name) && !empty($user->last_name)) {
                $comment_id =wp_insert_comment([
                    "comment_content" => $comment_content,
                    "comment_post_ID" => $video_id,
                    "user_id" => $user->ID,
                ]);
                if (is_int($comment_id)){
                    wp_send_json_success();
                }else{
                    wp_send_json_error(['msg'=>'مشکلی در ارسال کامنت']);
                }
            }else{
                wp_send_json_error(['msg'=>'برای در میان گذاشتن نظرات یا سوالات خود ، میبایست پروفایل خود را تکمیل نمایید.']);
            }
        } else {
            wp_send_json_error(['msg' => 'ابتدا وارد شوید یا ثبت نام کنید .']);
        }

    }

    function check_user_liked_video($user_id, $video_id): bool
    {
        $user_liked_video = get_user_meta($user_id, $this->video_post_type->liked_videos, true);
        return is_array($user_liked_video) && isset($user_liked_video[$video_id]);
    }
}