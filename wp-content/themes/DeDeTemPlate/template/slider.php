<?php $slides = cmb2_get_option('DeDeSlder', 'DeDeSliderPage'); ?>
<?php if (!empty($slides)) {
    ?>
    <div class="container mx-auto mt-4 px-4 md:p-0">
        <?php
        $slide_no = 0;
        $slide_image = '';
        $slide_ind = '';
        if (is_array($slides) || is_object($slides) ) {
            foreach ($slides as $key => $slide) {
                $image_src = wp_is_mobile() ? $slide['DeDe_header_slider_image_mobile'] : $slide['DeDe_header_slider_image'];
                $slide_image .= '<div class="w-full h-full"><a href="' . $slide['DeDe_header_slider_url'] . '"><img class="rounded-lg dede_header_image" src="' . $image_src . '" alt="سایت DeDe.ir"></a></div>';
                $slide_no++;
            }
        } ?>
        <!-- Carousel wrapper -->
        <div class="relative md:h-full w-full overflow-hidden">
            <?php echo($slide_image); ?>
        </div>
    </div>
<?php } ?>
