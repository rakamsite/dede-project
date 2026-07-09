<?php $link_tell_us = cmb2_get_option('phone_option', 'link_active'); ?>
<div id="contact_us_menu"
     class="absolute w-full bg-white megamenu_items text-[#525252] text-sm h-[320px] z-50 hidden border-t-[1px] border-black">
    <div class="container mx-auto grid grid-cols-3 h-[300px]">
        <div class="col-span-2 flex flex-col flex-wrap items-start h-[300px] pt-6">
            <?php
            $locations = get_nav_menu_locations();
            if (is_nav_menu($locations['contact-us'])) {
                $menu_items = wp_get_nav_menu_items($locations['contact-us']);
                foreach ($menu_items as $menu_item) {
                    if ($menu_item->menu_item_parent == 0) {
                        $menu_img = get_post_meta($menu_item->ID, '_menu_item_icon', true);
                        ?>
                        <a class=" flex flex-col justify-center h-auto w-1/2" href="<?php echo $menu_item->url; ?>">
                            <div class="flex items-center gap-3 rounded-lg p-2">
                                <?php
                                if (!empty($menu_img)) {
                                    echo '<img class="object-fill w-7 h-7" src="' . $menu_img . '" alt="' . $menu_item->title . '" />';
                                }
                                ?>
                                <p class="text-center text-[#0058BF] text-base  ">
                                    <?php echo $menu_item->title; ?>
                                </p>

                            </div>
                        </a>
                        <?php
                    }
                }
            }
            ?>
        </div>
        <a class="self-end justify-self-end bg-[#2F2483] text-white p-4 w-1/2 text-center rounded-lg"
           href="<?php echo $link_tell_us ?>">بیشتر ... </a>
    </div>
</div>
