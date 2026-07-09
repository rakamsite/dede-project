<?php
add_action( 'wp_nav_menu_item_custom_fields', function ( $menu_id, $menu_item ) {
	?>
    <p class="field-icon description-wide">
        <label for="edit-menu-item-icon-<?php echo esc_attr( $menu_item->ID ); ?>">
            لینک آیکن:
            <input type="text" id="edit-menu-item-icon-<?php echo esc_attr( $menu_item->ID ); ?>"
                   class="widefat code edit-menu-item-icon"
                   name="menu-item-icon[<?php echo esc_attr( $menu_item->ID ); ?>]"
                   value="<?php echo esc_attr( get_post_meta( $menu_item->ID, '_menu_item_icon', true ) ); ?>"/>
        </label>
    </p>
	<?php
}, 10, 4 );

add_action( 'wp_update_nav_menu_item', function ( $menu_id, $menu_item_db_id, $menu_item_args ) {
	if ( isset( $_REQUEST['menu-item-icon'][ $menu_item_db_id ] ) ) {
		$icon_value = $_REQUEST['menu-item-icon'][ $menu_item_db_id ];
		update_post_meta( $menu_item_db_id, '_menu_item_icon', $icon_value );
	}
}, 10, 3 );

function display_custom_menu_icons( $item_output, $item, $depth, $args ) {
	$icon = get_post_meta( $item->ID, '_menu_item_icon', true );
	if ( ! empty( $icon ) ) {
		$icon_output = '<span class="menu-icon">' . esc_html( $icon ) . '</span>';
		$item_output = str_replace( '</a>', $icon_output . '</a>', $item_output );
	}
	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'display_custom_menu_icons', 10, 4 );
