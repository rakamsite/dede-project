<?php
add_action( 'admin_menu', function () {
	add_menu_page(
		'تنظیمات قالب', // Page title
		'تنظیمات قالب', // Menu title
		'manage_options', // Capability required to access the menu
		'dede-theme-settings', // Menu slug
		'', // Callback function to display the menu page
		'dashicons-admin-generic', // Icon URL or dashicon name
		30 // Menu position
	);
	add_submenu_page( 'dede-theme-settings', "شهر و استان", "شهر و استان", 'administrator', 'dede-theme-settings', 'add_city_state_callback' );
} );

get_template_part( 'adminMenu/cart' );
get_template_part( 'adminMenu/popup' );
get_template_part( 'adminMenu/slider' );
get_template_part( 'adminMenu/wallet' );
get_template_part( 'adminMenu/specialSell' );
get_template_part( 'adminMenu/tellUs' );
get_template_part( 'adminMenu/Comment' );
get_template_part( 'adminMenu/product' );
get_template_part( 'adminMenu/licence' );
get_template_part( 'adminMenu/buyCondition' );
get_template_part( 'adminMenu/magMenu' );
get_template_part( 'adminMenu/storyLocations' );
get_template_part( 'adminMenu/SmsSection' );
get_template_part( 'adminMenu/OrderManager' );

function add_city_state_callback(): void
{ ?>
    <button type="button" id="update" name="update" class="button" style="width: 90%; padding: 10px; margin-top:20px">
        بروز رسانی لیست شهر و استان ها
    </button>
    <script>
        jQuery(document).ready(function ($) {
            $("button#update").on('click', function () {
                $.ajax({
                    url: "<?php echo admin_url("admin-ajax.php") ?>",
                    type: 'post',
                    data: {
                        action: "add_state_and_city"
                    },
                    success: function (res) {
                        alert(res.data)
                    },
                    error: function (err) {
                        console.log(err);
                        reject(err);
                    }

                });
            });
        });
    </script>
	<?php
}