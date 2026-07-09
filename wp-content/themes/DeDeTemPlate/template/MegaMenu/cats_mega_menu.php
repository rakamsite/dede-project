<div id="cats_mega_menu"
     class="absolute w-full bg-white megamenu_items text-[#525252] text-sm h-[320px] z-50 hidden border-t-[1px] border-black">
	<div class="grid grid-cols-7 gap-4 container px-4 py-5 mx-auto h-[300px]">
		<ul class="mainmenu-dede mb-4 space-y-1 w-full border-l-[0.5px] border-[#E9E9E9] h-full col-span-2 overflow-y-auto">
			<li class="w-full relative">
				<button onclick="window.open('https://dede.ir/brands/' , '_self')" class="w-3/4 flex justify-between items-center py-0.5 px-3 after:mt-1 parents-cat-product activeMegaItem rounded-full bg-[#E3000F] after:content-['〱'] text-white" value="brands" id="brands">
					برندها
				</button>
			</li>
			<?php
			$menu_name           = 'cat-menu';
			$locations           = get_nav_menu_locations();
			$menu_items          = wp_get_nav_menu_items( $locations[ $menu_name ] );
			$submenu_items       = array();
			$submenu_output_item = '';
			foreach ( $menu_items as $menu_item ) {
				if ( $menu_item->menu_item_parent == 0 ) {
					?>
					<li class="w-full relative ">
						<button class="w-3/4 flex justify-between items-center py-0.5 px-3 after:mt-1 parents-cat-product" onclick="window.open('<?php echo $menu_item->url ?>' , '_self')" value="<?php echo $menu_item->ID ?>">
							<?php echo $menu_item->title; ?>
						</button>
					</li>
					<?php
				} else {
					$submenu_items[ $menu_item->menu_item_parent ][] = $menu_item;
				}
			}
			foreach ( $submenu_items as $parent_id => $submenu ) {
				$submenu_output_item .= '<div class="hidden megamenu-child-all px-2 products-megamenu-child-' . $parent_id . '">';
				foreach ( $submenu as $item ) {
					$submenu_output_item .= '<a href="' . $item->url . '" target=""><li class="w-full relative"><button class="min-w-[75%] w-fit p-1 text-right  children-cat-product get_child_cat_get_icon_link" value="' . $item->ID . '">' . $item->title . '</button> </li></a>';
				}
				$submenu_output_item .= '</div>';
			}
			?>
		</ul>
		<ul class="submenu mb-4 space-y-1 w-full border-l-[0.5px] border-[#E9E9E9] h-full col-span-2 overflow-y-auto hidden"><?php echo( $submenu_output_item ); ?></ul>
		<div id="product-menu-icon"
		     class="col-span-3 my-auto flex justify-center items-center rounded-lg text-center h-[250px] hidden">
		</div>
		<div class="col-span-5 grid grid-cols-2 place-items-center items-center gap-14 py-5 px-10" id="bran-element">
			<?php
			$menu_name           = 'brand-menu';
			$locations           = get_nav_menu_locations();
			$menu_items          = wp_get_nav_menu_items( $locations[ $menu_name ] );
			$submenu_items       = array();
			$submenu_output_item = '';
			foreach ( $menu_items as $menu_item ) {
				if ( $menu_item->menu_item_parent == 0 ) {
					$menu_img = get_post_meta( $menu_item->ID, '_menu_item_icon', true );
					?>
					<div class=" h-[200px] rounded-lg ">
						<a class="" href="<?php echo $menu_item->url; ?>">
							<?php
							if ( ! empty( $menu_img ) ) {
								echo '<img class="object-fill w-full h-auto rounded-lg" src="' . $menu_img . '" alt="' . $menu_item->title . '" />';
							}
							?>
						</a>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
</div>
