<?php
add_action('cmb2_admin_init', function () {
	$cmb_options = new_cmb2_box(array(
		'id' => 'dedePopup',
		'title' => "مدیریت پاپ آپ",
		'object_types' => array('options-page'),
		'option_key' => 'DeDePopUp', // The option key and admin menu page slug.
		'menu_title' => "پاپ آپ", // Falls back to 'title' (above).
		'parent_slug' => 'dede-theme-settings', // Make options page a submenu item of the themes menu.
	));
	// Add four sub-fields for each part of the submenu
	for ($i = 1; $i <= 4; $i++) {
		$group_field_id = $cmb_options->add_field(array(
			'id' => "dede_main_popup_$i",
			'type' => 'group',
			'name' => " تنظیمات پاپ آپ$i",
		));

		$cmb_options->add_group_field($group_field_id, array(
			'name' => 'تیتر پاپ آپ',
			'id' => "dede_title_popup_$i",
			'type' => 'text',
		));

		$cmb_options->add_group_field($group_field_id, array(
			'name' => 'توضیحات پاپ آپ',
			'id' => "dede_description_popup_$i",
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 5,
			),
		));

		$cmb_options->add_group_field($group_field_id, array(
			'name' => 'تصویر پاپ آپ',
			'id' => "dede_popup_image_url_$i",
			'type' => 'file', // نوع فیلد تصویر
		));

		$cmb_options->add_group_field($group_field_id, array(
			'name' => 'متن دکمه پاپ آپ',
			'id' => "dede_button_text_$i",
			'type' => 'text',
		));

		$cmb_options->add_group_field($group_field_id, array(
			'name' => 'تصویر آیکن',
			'id' => "dede_button_icon_$i",
			'type' => 'file', // نوع فیلد تصویر
		));
	}
});
