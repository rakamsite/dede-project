<?php

namespace dede_dev_admin_menu;

class dede_dev_woocommerce_order_email
{
    public string $prefix;

    public function __construct()
    {
        $this->prefix = "dede_dev_woocommerce_email";
    }
    public function woocommerce_email(){
        $social_media_list = ["instagram" , "telegram" , "facebook" , "x" , "aparat" , "youtube" , "linkedin"];
        $header_image_id = $_POST['woocommerce_email_header_image_id'];
        $description = $_POST['woocommerce_email_description'];
        $copyright = $_POST['woocommerce_email_footer_copyright'];
        if (!empty($header_image_id)){
            update_option($this->prefix.'_image_id',$header_image_id);
        }
        if (!empty($description)){
            update_option($this->prefix.'_description', $description);
        }
        if (!empty($copyright)){
            update_option($this->prefix.'_copyright' , $copyright);
        }
        foreach ($social_media_list as $item) {
            if (!empty($_POST["woocommerce_email_footer_$item"])) {
                update_option($this->prefix . "_$item", $_POST["woocommerce_email_footer_$item"]);
            }
        }
        for($i=1 ; $i <= 3; $i++){
            if (!empty($_POST["woocommerce_email_product_$i"])){
             update_option($this->prefix."_product_$i" , $_POST["woocommerce_email_product_$i"]);
            }
        }
        wp_send_json_success(["message" => "تغییرات ذخیره شدم.", "options" => ["accept" => "باشه"]]);
    }
}