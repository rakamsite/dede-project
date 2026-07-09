<?php

namespace dede_dev_run_plugin;
use dede_dev_admin_menu\dede_dev_admin_menu;
use dede_dev_admin_menu\dede_dev_ajax_exel_importer_handler;
use dede_dev_admin_menu\dede_dev_ajax_pdf_maker;
use dede_dev_admin_menu\dede_dev_woocommerce_order_email;

class dede_dev_run_plugin
{
    public function run(){
        (new dede_dev_admin_menu)->run();
        $ajax_handler = new dede_dev_ajax_exel_importer_handler();
        add_action("wp_ajax_dede_dev_exel_extractor" , [$ajax_handler ,"exel_extractor"]);
        $pdf_handler = new dede_dev_ajax_pdf_maker();
        add_action("wp_ajax_dede_dev_pdf_maker" , [$pdf_handler ,"PDF_maker"]);
        $woocommerce_email = new dede_dev_woocommerce_order_email();
        add_action("wp_ajax_dede_dev_woocommerce_email" , [$woocommerce_email , "woocommerce_email"]);
    }
}