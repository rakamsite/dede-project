<?php
$main_page_story_short_code = cmb2_get_option('manage_story_locations','main_page_story_shortcode');
if (is_string($main_page_story_short_code)){
    $short_code = do_shortcode($main_page_story_short_code);
    echo <<<HTML
<section class="container mx-auto">
    $short_code
</section>
HTML;
}
?>
