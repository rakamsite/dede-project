<?php $link_tell_us = cmb2_get_option('phone_option', 'link_active'); ?>
<div id="contact_us_menu_mobile" aria-hidden="false"
     class="fixed top-0 right-0 z-50 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-full">
    <div class="flex flex-row items-center text-[#525252] relative">
        <button class="flex gap-2 w-full" type="button" data-drawer-hide="contact_us_menu_mobile">
            <svg width="11" height="23" viewBox="0 0 11 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.7402 10.7835L1.40559 0.273333C1.24984 0.0978778 1.04291 -5.66508e-07 0.827729 -5.78751e-07C0.612545 -5.90994e-07 0.405618 0.0978778 0.249866 0.273333L0.239815 0.285209C0.164047 0.370267 0.103715 0.47265 0.0624876 0.58613C0.0212602 0.69961 -3.59227e-08 0.821815 -4.13208e-08 0.94531C-4.6719e-08 1.06881 0.0212602 1.19101 0.0624875 1.30449C0.103715 1.41797 0.164047 1.52035 0.239815 1.60541L9.03007 11.502L0.239814 21.3946C0.164046 21.4796 0.103714 21.582 0.0624867 21.6955C0.0212593 21.809 -9.58643e-07 21.9312 -9.64041e-07 22.0547C-9.69439e-07 22.1782 0.0212593 22.3004 0.0624866 22.4139C0.103714 22.5273 0.164046 22.6297 0.239814 22.7148L0.249865 22.7267C0.405617 22.9021 0.612544 23 0.827728 23C1.04291 23 1.24984 22.9021 1.40559 22.7267L10.7402 12.2165C10.8223 12.1241 10.8877 12.0129 10.9323 11.8898C10.977 11.7666 11 11.634 11 11.5C11 11.366 10.977 11.2334 10.9323 11.1102C10.8877 10.9871 10.8223 10.8759 10.7402 10.7835Z"
                      fill="#525252"/>
            </svg>
            <p class="w-full text-right">بازگشت</p>
        </button>
        <button type="button" class="float-left" data-drawer-hide="mobile-menu"
                data-drawer-toggle="contact_us_menu_mobile">
            <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="27.3989" y="9.00598" width="3.55485" height="27.0169" rx="1.77743"
                      transform="rotate(45 27.3989 9.00598)" fill="#525252"/>
                <rect x="29.9125" y="28.1099" width="3.55485" height="27.0169" rx="1.77743"
                      transform="rotate(135 29.9125 28.1099)" fill="#525252"/>
            </svg>
        </button>
    </div>

    <div class="flex flex-col ">
        <?php
        $locations = get_nav_menu_locations();
        if (is_nav_menu($locations['contact-us'])) {
            $menu_items = wp_get_nav_menu_items($locations['contact-us']);
            foreach ($menu_items as $menu_item) {
                if ($menu_item->menu_item_parent == 0) {
                    $menu_img = get_post_meta($menu_item->ID, '_menu_item_icon', true);
                        ?>
                        <a class=" flex flex-col justify-center h-auto w-full" href="<?php echo $menu_item->url; ?>">
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
        <a class="bg-[#2F2483] mt-5 text-white p-4 text-center rounded-lg"
           href="<?php echo $link_tell_us ?>">بیشتر ... </a>
    </div>
</div>
