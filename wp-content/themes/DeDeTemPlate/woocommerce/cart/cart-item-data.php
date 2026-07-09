<?php
/**
 * Cart item data (when outputting non-flat)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-item-data.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     2.4.0
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<button class="text-blue-600 mx-2 showMore-card w-fit" type="button">جزئیات...</button>
<div class="variation text-base lg:text-xs w-full absolute md:top-1/2 right-0 md:-translate-y-1/2 bg-[#FFEDAF] rounded-lg text-[#6A6A6A] p-2 md:mr-2 hidden">
    <button class="showMore-card" type="button">X</button>
    <div class="w-full " >
        <?php foreach ($item_data as $data) : ?>
            <p class=""><?php echo $data['key']; ?>:<?php echo $data['display']; ?></p>
        <?php endforeach; ?>
    </div>
</div>
