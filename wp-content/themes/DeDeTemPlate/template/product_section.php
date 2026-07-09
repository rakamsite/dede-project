<?php
$locations3 = get_nav_menu_locations();
    $menu_items = wp_get_nav_menu_items( $locations3['brand-menu'] );

if ( ! empty( $menu_items ) ){
?>
<div class="container my-12 relative text-center md:flex items-center justify-center mx-auto hidden">
    <hr class="border-[0.5px] border-[#525252]/50 w-full mx-auto my-auto"/>
    <p class="font-bold text-[#525252] text-2xl mt-0 mb-auto absolute -top-5 bg-white px-10">از کدام برند قصد خرید
        دارید؟</p>
</div>
<div class='md:grid grid-cols-2 place-items-center items-center gap-10 h-[200px] container mx-auto hidden'>
	<?php
	echo "";
	foreach ( $menu_items as $menu_item ) {
		$menu_img = get_post_meta( $menu_item->ID, '_menu_item_icon', true );
		?>
        <div class=" h-[200px] ">
            <a href="<?php echo $menu_item->url; ?>" target="_blank">
				<?php
				if ( ! empty( $menu_img ) ) {
					echo '<img class="object-fill w-auto h-full " src="' . $menu_img . '" alt="' . $menu_item->title . '" />';
				}
				?>
            </a>
        </div>
		<?php
	}
	}
	?>
</div>
<div class="container my-12 relative text-center flex items-center justify-center mx-auto hidden">
    <hr class="border-[0.5px] border-[#525252]/50 w-full mx-auto my-auto"/>
    <p class="font-bold text-[#525252] text-2xl mt-0 mb-auto absolute -top-5 bg-white px-10">محصولات </p>
</div>
<div class="container mx-auto mt-12">
    <div class="grid grid-cols-1 px-4 md:px-0 md:grid-cols-3 gap-5">
		<?php
		$respons    = '';
        $args = array(
            'taxonomy'   => 'product_cat',
            'parent'     => 0,
            'meta_query' => array(
                array(
                    'key'     => 'ordering_custom',
                    'value'   => 99,
                    'type'    => 'numeric',
                    'compare' => '<',
                ),
            ),
            'orderby'    => 'meta_value_num',
            'hide_empty' => true,
        );
        $categories = get_terms( $args );
		foreach ( $categories as $category ) {
			$brand_name    = get_term_meta( $category->term_id, 'brand_name', true );
			$brand_link    = get_term_meta( $category->term_id, 'brand_url', true );
			$thumbnail_id  = get_term_meta( $category->term_id, 'thumbnail_id', true );
			$category_url  = get_term_link( $category->term_id, 'product_cat' ); // For WooCommerce categories, use 'product_cat' as the taxonomy name
			$thumbnail_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );
			$respons       .= '<div class="shadow-xl">';
			$respons       .= '<div class="w-full rounded-t-lg text-lg text-white font-[900] text-center bg-[#2F2483] py-2"><h2>' . $category->name . '</h2></div>';
			$respons       .= '<div class="grid grid-cols-2 gap-4 md:py-2 md:px-2">';
			$respons       .= '<a href="' . $category_url . '"><img class="border-[#D9D9D9] border-[0.5px] rounded-lg m-2 aspect-square w-full" src="' . $thumbnail_url . '" alt="'.$category->name.'"></a>';
			$respons       .= '<div class="text-[14px] text-[#5E5E5E] py-5 font-[700] overflow-y-auto aspect-square divide-y divide-y-[0.5px] divide-[#525252]/25 pl-2">';

			$term_id   = $category->term_id;
			$term_slug = $category->slug;
			$args      = array(
				'post_type'      => 'product',
				'posts_per_page' => - 1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $term_slug,
					),
				),
			);
			$products  = new WP_Query( $args );
			if ( $products->have_posts() ) {
				$attributes_count=0;
				while ( $products->have_posts() ) {
					$products->the_post();
					$respons .= '<div class="flex py-2">';
					$respons .= '<img class="w-5 h-5" title="' . get_the_title() . '" alt="' . get_the_title() . '" src="' . dedeTemplate . '/assets/image/Vector.svg' . '"/>';
					$respons .= '<a class="mr-2" href="' . get_the_permalink() . '">' . get_the_title() . '</a>';
					$respons .= '</div>';
                    $args = array(
						'post_type'     => 'product_variation',
						'post_status'   => array( 'publish' ),
						'numberposts'   => -1,
						'orderby'       => 'menu_order',
						'order'         => 'asc',
						'post_parent'   => get_the_ID()
					);
					$variations = get_posts( $args );
					if ( empty( $variations ) ) {
						$attributes_count++;
					} else {
						foreach ( $variations as $variation ) {
							$attributes_count++;
						}
					}				}
			}
			$respons .= '</div>';
			$respons .= '</div>';
			$respons .= '<div class="flex bg-[#cdcdcd] rounded-b-lg text-xs text-[#2F2483] py-3 w-full divide-x-reverse divide-x-2 divide-gray-50 font-[700] mt-4 md:mt-auto"><div class="w-full text-center basis-2/4">تنوع محصولات: ' . $attributes_count . ' عدد</div><div class="w-full text-center basis-2/3">برند: <a href="' . $brand_link . '">' . $brand_name . '</a></div><div class="w-full text-center basis-1/3"><a href="' . $category_url . '">اطلاعات بیشتر </a></div></div>';
			$respons .= '</div>';
		}

		echo $respons;
		wp_reset_postdata();
		?>
    </div>
</div>
