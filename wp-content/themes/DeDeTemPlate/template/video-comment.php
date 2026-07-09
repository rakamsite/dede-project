<?php
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area mx-auto w-full mt-10 divide-y-2">
    <?php if (have_comments()) : ?>
        <ul class="space-y-4 ">
            <?php
            wp_list_comments(array(
                'style'       => 'ul',
                'short_ping'  => true,
                'avatar_size' => 0,
                'callback'    => 'custom_comment_tailwind',
                'max_depth'   => 3,
                'reverse_top_level'=>false
            ));
            ?>
        </ul>
        <!-- صفحه‌بندی کامنت‌ها -->
        <div class="comment-pagination mt-6 flex justify-center gap-2 [&_a]:bg-[#2F2483]/80 [&_span]:bg-[#2F2483] [&_*]:text-white [&_*]:p-2 [&_*]:rounded p-4">
            <?php
            paginate_comments_links(array(
                'prev_text' => '« قبلی',
                'next_text' => 'بعدی »',
            ));
            ?>
        </div>
    <?php endif; ?>
</div>

<?php
function custom_comment_tailwind($comment, $args, $depth): void
{
    $GLOBALS['comment'] = $comment;
    $is_admin = user_can($comment->user_id, 'administrator');
    $user = wp_get_current_user();
    if ($is_admin){
        $comment_display_name = $comment->comment_author;
    }else{
        $comment_display_name = $user->first_name . ' ' . $user->last_name;
    }
    ?>
    <li id="comment-<?php comment_ID(); ?>" class=" rounded-lg border-b p-4 space-y-2">
        <span class=" <?php echo $is_admin ? 'bg-[#2F2483] text-white' : 'bg-gray-300' ?> px-1 rounded"><?php echo $comment_display_name ?></span>
        <div class="comment-body text-gray-700 text-sm leading-relaxed" >
            <p class=" <?php echo $is_admin ? 'text-[#2F2483]' : '' ?>"><?php echo $comment->comment_content ?></p>
        </div>
    </li>
<?php } ?>
