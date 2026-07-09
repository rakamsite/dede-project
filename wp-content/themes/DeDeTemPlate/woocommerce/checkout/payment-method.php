<?php
/**
 * Output a single payment method
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment-method.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$is_bacs= $gateway->id == "bacs";
$payment_id = get_option('buy_condition_option_key')['buy_condition_payment_id'];

?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> mt-1 flex flex-wrap group my-2">
    <input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio peer" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />

    <label class=" flex text-gray-400 peer-checked:text-black" for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
        <?php echo str_replace('<img' , '<img class="w-10 mx-2" ' , $gateway->get_icon()) ?><p class="mx-2 "><?php echo $gateway->get_title(); ?></p>
    </label>
    <?php
    if ($is_bacs){
        echo '<input class="w-full border border-gray-400 disabled peer-checked:border-gray-900 rounded p-2 text-center mt-2 tracking-[1rem] " max="'.strlen($payment_id).'" id="payment_id" type="text" placeholder="">';
    }
    ?>
</li>
