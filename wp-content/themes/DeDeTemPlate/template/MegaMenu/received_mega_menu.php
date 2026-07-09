<div id="received_mega_menu"
     class="absolute w-full h-[320px] bg-white megamenu_items text-[#525252] text-sm h-[300px] z-50 hidden border-t-[1px] border-black">
    <div class="col-span-5 container mx-auto flex flex-row justify-evenly items-center justify-evenly h-[300px]">
		<?php
		$locations  = get_nav_menu_locations();
        if (is_nav_menu($locations['received-menu'])){
            $menu_items = wp_get_nav_menu_items( $locations['received-menu'] );
            foreach ( $menu_items as $menu_item ) {
                if ( $menu_item->menu_item_parent == 0 ) {
                    $menu_img = get_post_meta( $menu_item->ID, '_menu_item_icon', true );
                    ?>
                    <div>
                        <a  class=" flex flex-col justify-center h-auto w-full"  href="<?php echo $menu_item->url; ?>" >
                            <div class="bg-[#E9E9E9]/50 hover:bg-[#E3000F] icon-holder-img-megamenu rounded-lg p-2">
                                <?php
                                if ( ! empty( $menu_img ) ) {
                                    echo '<img class="object-fill w-full h-auto  max-h-[130px] max-w-[130px] rounded-lg icon-img-megamenu filter-gray" src="' . $menu_img . '" alt="' . $menu_item->title . '" />';
                                }
                                ?>
                            </div>
                            <div class="flex justify-center items-center min-w-[130px] mt-3">
                                <p class="text-center text-black text-sm">
                                    <?php echo $menu_item->title; ?>
                                </p>
                            </div>
                        </a>
                    </div>
                    <?php
                }
            }
        }
		?>
	</div>
</div>
