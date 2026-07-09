<?php


use classes\abandoned_cart\dede_v2_abandoned_cart;
use classes\ajax_handler\dede_v2_abandoned_ajax_handler;
use classes\ajax_handler\dede_v2_stock_quantity_ajax_handler;
use classes\date_convert\dede_dev2_date_convert;
use classes\functionality\dede_v2_scheduling;
use classes\stock_quantity_handler\dede_v2_stock_quantity;
use classes\story_short_code\dede_v2_story_shortcode;
use classes\video_post_type\dede_v2_post_type_main;
use classes\woocommerce_sms\dede_v2_completed_order;


class dede_v2_run_plugin
{
    function run(): void
    {
        (new dede_v2_scheduling())->run();
        (new dede_v2_abandoned_cart())->run();
        (new dede_v2_abandoned_ajax_handler())->run();
        (new dede_v2_completed_order())->run();
        (new dede_v2_stock_quantity_ajax_handler())->run();
        (new dede_v2_stock_quantity())->run();
        (new dede_v2_post_type_main())->run();
        (new dede_v2_story_shortcode())->run();
        (new dede_dev2_date_convert())->run();
    }
}