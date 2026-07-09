<div id="mobile_categories_menu" aria-hidden="false"
     class="fixed top-0 right-0 z-50 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-full ">
    <div class="flex flex-row items-center text-[#525252] relative">
        <button class="flex gap-2 w-full" type="button" data-drawer-hide="mobile_categories_menu">
            <svg width="11" height="23" viewBox="0 0 11 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.7402 10.7835L1.40559 0.273333C1.24984 0.0978778 1.04291 -5.66508e-07 0.827729 -5.78751e-07C0.612545 -5.90994e-07 0.405618 0.0978778 0.249866 0.273333L0.239815 0.285209C0.164047 0.370267 0.103715 0.47265 0.0624876 0.58613C0.0212602 0.69961 -3.59227e-08 0.821815 -4.13208e-08 0.94531C-4.6719e-08 1.06881 0.0212602 1.19101 0.0624875 1.30449C0.103715 1.41797 0.164047 1.52035 0.239815 1.60541L9.03007 11.502L0.239814 21.3946C0.164046 21.4796 0.103714 21.582 0.0624867 21.6955C0.0212593 21.809 -9.58643e-07 21.9312 -9.64041e-07 22.0547C-9.69439e-07 22.1782 0.0212593 22.3004 0.0624866 22.4139C0.103714 22.5273 0.164046 22.6297 0.239814 22.7148L0.249865 22.7267C0.405617 22.9021 0.612544 23 0.827728 23C1.04291 23 1.24984 22.9021 1.40559 22.7267L10.7402 12.2165C10.8223 12.1241 10.8877 12.0129 10.9323 11.8898C10.977 11.7666 11 11.634 11 11.5C11 11.366 10.977 11.2334 10.9323 11.1102C10.8877 10.9871 10.8223 10.8759 10.7402 10.7835Z"
                      fill="#525252"/>
            </svg>
            <p class="w-full text-right">بازگشت</p>
        </button>
        <button type="button" class="float-left" data-drawer-hide="mobile-menu"
                data-drawer-toggle="mobile_categories_menu">
            <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="27.3989" y="9.00598" width="3.55485" height="27.0169" rx="1.77743"
                      transform="rotate(45 27.3989 9.00598)" fill="#525252"/>
                <rect x="29.9125" y="28.1099" width="3.55485" height="27.0169" rx="1.77743"
                      transform="rotate(135 29.9125 28.1099)" fill="#525252"/>
            </svg>
        </button>
    </div>
    <div class="divide-y text-[18px] text-[#525252] mt-3">
        <button class="p-2 flex items-center w-full justify-between" type="button"
                data-drawer-target="mobile_brand_menu"
                data-drawer-show="mobile_brand_menu" data-drawer-placement="right"
                aria-controls="mobile_brand_menu">
            <p>برند ها</p>
            <svg width="8" height="16" viewBox="0 0 8 16" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M0.188937 8.49844L6.97775 15.8099C7.09102 15.9319 7.24152 16 7.39802 16C7.55451 16 7.70501 15.9319 7.81828 15.8099L7.82559 15.8016C7.88069 15.7424 7.92457 15.6712 7.95455 15.5923C7.98454 15.5133 8 15.4283 8 15.3424C8 15.2565 7.98454 15.1715 7.95455 15.0925C7.92457 15.0136 7.88069 14.9424 7.82559 14.8832L1.43268 7.99862L7.82559 1.11681C7.88069 1.05764 7.92457 0.986415 7.95455 0.907473C7.98454 0.828531 8 0.743518 8 0.657608C8 0.571698 7.98454 0.486687 7.95455 0.407744C7.92457 0.328801 7.88069 0.257578 7.82559 0.198408L7.81828 0.190146C7.70501 0.0680897 7.55451 1.89682e-07 7.39802 1.85753e-07C7.24152 1.81823e-07 7.09102 0.0680897 6.97775 0.190146L0.188937 7.50156C0.129232 7.56586 0.0817008 7.64319 0.0492237 7.72887C0.0167466 7.81455 9.65106e-08 7.90679 9.5399e-08 8C9.42875e-08 8.09321 0.0167466 8.18545 0.0492237 8.27113C0.0817008 8.35681 0.129232 8.43415 0.188937 8.49844Z"
                      fill="#525252"/>
            </svg>
        </button>
		<?php
		$menu_name           = 'cat-menu';
		$locations           = get_nav_menu_locations();
		$menu_items          = wp_get_nav_menu_items( $locations[ $menu_name ] );
		function has_submenu($menu_item_id, $menu_items) {
			foreach ($menu_items as $menu_item) {
				if ($menu_item->menu_item_parent == $menu_item_id) {
					return true;
				}
			}
		}
		$submenu_items       = array();
		$submenu_output_item = '';
		foreach ( $menu_items as $menu_item ) {
			if ( $menu_item->menu_item_parent == 0 ) {
				?>
                <button class="p-2 flex items-center w-full justify-between" type="button"
					<?php if (has_submenu($menu_item->ID , $menu_items)) : ?>
                        data-drawer-target="<?php echo $menu_item->ID ?>"
                        data-drawer-show="<?php echo $menu_item->ID ?>" data-drawer-placement="right"
                        aria-controls="<?php echo $menu_item->ID; ?>"
					<?php endif; ?>
                >
                    <p><?php echo $menu_item->title; ?></p>
                    <svg width="8" height="16" viewBox="0 0 8 16" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.188937 8.49844L6.97775 15.8099C7.09102 15.9319 7.24152 16 7.39802 16C7.55451 16 7.70501 15.9319 7.81828 15.8099L7.82559 15.8016C7.88069 15.7424 7.92457 15.6712 7.95455 15.5923C7.98454 15.5133 8 15.4283 8 15.3424C8 15.2565 7.98454 15.1715 7.95455 15.0925C7.92457 15.0136 7.88069 14.9424 7.82559 14.8832L1.43268 7.99862L7.82559 1.11681C7.88069 1.05764 7.92457 0.986415 7.95455 0.907473C7.98454 0.828531 8 0.743518 8 0.657608C8 0.571698 7.98454 0.486687 7.95455 0.407744C7.92457 0.328801 7.88069 0.257578 7.82559 0.198408L7.81828 0.190146C7.70501 0.0680897 7.55451 1.89682e-07 7.39802 1.85753e-07C7.24152 1.81823e-07 7.09102 0.0680897 6.97775 0.190146L0.188937 7.50156C0.129232 7.56586 0.0817008 7.64319 0.0492237 7.72887C0.0167466 7.81455 9.65106e-08 7.90679 9.5399e-08 8C9.42875e-08 8.09321 0.0167466 8.18545 0.0492237 8.27113C0.0817008 8.35681 0.129232 8.43415 0.188937 8.49844Z"
                              fill="#525252"/>
                    </svg>
                </button>
				<?php
			} else {
				$submenu_items[ $menu_item->menu_item_parent ][] = $menu_item;
			}
		}
		foreach ( $submenu_items as $parent_id => $submenu ) {
			$submenu_output_item .= '<div id="' . $parent_id . '" class="fixed top-0 right-0 z-[60] h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-full ">';
			$submenu_output_item .= '<div class="flex flex-row items-center text-[#525252] relative "> <button class="flex gap-2 w-full" type="button" data-drawer-hide="' . $parent_id . '"> <svg width="11" height="23" viewBox="0 0 11 23" fill="none" xmlns="http://www.w3.org/2000/svg">  <path d="M10.7402 10.7835L1.40559 0.273333C1.24984 0.0978778 1.04291 -5.66508e-07 0.827729 -5.78751e-07C0.612545 -5.90994e-07 0.405618 0.0978778 0.249866 0.273333L0.239815 0.285209C0.164047 0.370267 0.103715 0.47265 0.0624876 0.58613C0.0212602 0.69961 -3.59227e-08 0.821815 -4.13208e-08 0.94531C-4.6719e-08 1.06881 0.0212602 1.19101 0.0624875 1.30449C0.103715 1.41797 0.164047 1.52035 0.239815 1.60541L9.03007 11.502L0.239814 21.3946C0.164046 21.4796 0.103714 21.582 0.0624867 21.6955C0.0212593 21.809 -9.58643e-07 21.9312 -9.64041e-07 22.0547C-9.69439e-07 22.1782 0.0212593 22.3004 0.0624866 22.4139C0.103714 22.5273 0.164046 22.6297 0.239814 22.7148L0.249865 22.7267C0.405617 22.9021 0.612544 23 0.827728 23C1.04291 23 1.24984 22.9021 1.40559 22.7267L10.7402 12.2165C10.8223 12.1241 10.8877 12.0129 10.9323 11.8898C10.977 11.7666 11 11.634 11 11.5C11 11.366 10.977 11.2334 10.9323 11.1102C10.8877 10.9871 10.8223 10.8759 10.7402 10.7835Z" fill="#525252"/>  </svg> <p class="w-full text-right">بازگشت</p> </button> <button type="button" class="float-left" data-drawer-hide="mobile_categories_menu" data-drawer-toggle="' . $parent_id . '"> <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg"> <rect x="27.3989" y="9.00598" width="3.55485" height="27.0169" rx="1.77743" transform="rotate(45 27.3989 9.00598)" fill="#525252"/> <rect x="29.9125" y="28.1099" width="3.55485" height="27.0169" rx="1.77743" transform="rotate(135 29.9125 28.1099)" fill="#525252"/> </svg> </button> </div>';
			$submenu_output_item .="<div class='divide-y text-[#525252]'>";
            foreach ( $submenu as $item ) {
				$submenu_output_item.= "<div>";
				$submenu_output_item.= "<a href='$item->url' target='_blank' class='p-2 flex items-center w-full'>";
				$submenu_output_item.= "<p class='text-center'>";
				$submenu_output_item.= $item->title;
				$submenu_output_item.= "</p>";
				$submenu_output_item.= "</a>";
				$submenu_output_item.= "</div>";
			}
			$submenu_output_item .= '</div>';
			$submenu_output_item .= '</div>';
		}
		?>
    </div>
    <div id="mobile_brand_menu"
         class="fixed top-0 right-0 z-[60] h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-full">
        <div class="flex flex-row items-center text-[#525252] relative">
            <button class="flex gap-2 w-full" type="button" data-drawer-hide="mobile_brand_menu">
                <svg width="11" height="23" viewBox="0 0 11 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7402 10.7835L1.40559 0.273333C1.24984 0.0978778 1.04291 -5.66508e-07 0.827729 -5.78751e-07C0.612545 -5.90994e-07 0.405618 0.0978778 0.249866 0.273333L0.239815 0.285209C0.164047 0.370267 0.103715 0.47265 0.0624876 0.58613C0.0212602 0.69961 -3.59227e-08 0.821815 -4.13208e-08 0.94531C-4.6719e-08 1.06881 0.0212602 1.19101 0.0624875 1.30449C0.103715 1.41797 0.164047 1.52035 0.239815 1.60541L9.03007 11.502L0.239814 21.3946C0.164046 21.4796 0.103714 21.582 0.0624867 21.6955C0.0212593 21.809 -9.58643e-07 21.9312 -9.64041e-07 22.0547C-9.69439e-07 22.1782 0.0212593 22.3004 0.0624866 22.4139C0.103714 22.5273 0.164046 22.6297 0.239814 22.7148L0.249865 22.7267C0.405617 22.9021 0.612544 23 0.827728 23C1.04291 23 1.24984 22.9021 1.40559 22.7267L10.7402 12.2165C10.8223 12.1241 10.8877 12.0129 10.9323 11.8898C10.977 11.7666 11 11.634 11 11.5C11 11.366 10.977 11.2334 10.9323 11.1102C10.8877 10.9871 10.8223 10.8759 10.7402 10.7835Z"
                          fill="#525252"/>
                </svg>
                <p class="w-full text-right">بازگشت</p>
            </button>
            <button type="button" class="float-left" data-drawer-hide="mobile_brand_menu"
                    data-drawer-toggle="mobile_categories_menu">
                <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="27.3989" y="9.00598" width="3.55485" height="27.0169" rx="1.77743"
                          transform="rotate(45 27.3989 9.00598)" fill="#525252"/>
                    <rect x="29.9125" y="28.1099" width="3.55485" height="27.0169" rx="1.77743"
                          transform="rotate(135 29.9125 28.1099)" fill="#525252"/>
                </svg>
            </button>
        </div>
        <div class="mt-2 divide-y text-[18px] text-[#525252]">
			<?php
			$menu_name           = 'brand-menu';
			$locations           = get_nav_menu_locations();
			$menu_items          = wp_get_nav_menu_items( $locations[ $menu_name ] );
			$submenu_items       = array();
			foreach ( $menu_items as $menu_item ) {
				if ( $menu_item->menu_item_parent == 0 ) {
					?>
                    <div>
                        <a href="<?php echo $menu_item->url; ?>"
                           class="p-2 flex items-center w-full">
                            <p class="text-center">
								<?php echo $menu_item->title; ?>
                            </p>
                        </a>
                    </div>
					<?php
				}
			}
			?>
        </div>
    </div>
	<?php echo($submenu_output_item); ?>
</div>
