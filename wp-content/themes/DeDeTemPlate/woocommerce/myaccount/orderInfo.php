<?php
$order_id = isset($_GET['order']) ? intval($_GET['order']) : 0; // بررسی و اعتبارسنجی ورودی GET
$user_id = get_current_user_id();

if ($order_id && $user_id) {
    $order = wc_get_order($order_id);

    if ($order && $order->get_user_id() === $user_id) {
        $order_address = !empty($order->get_billing_address_1()) ? $order->get_billing_address_1() : $order->get_shipping_address_1();
        $user_data = get_userdata($user_id);
        $order_status = $order->get_status();

        if ($order_status === "completed") { ?>
            <div class="w-full font-[700] text-[20px] text-[#4B5259] flex">
                <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.5 12.4583C10.2292 12.4583 9.01039 11.9534 8.11178 11.0548C7.21317 10.1562 6.70833 8.93741 6.70833 7.66658H8.625C8.625 8.42908 8.9279 9.16035 9.46707 9.69952C10.0062 10.2387 10.7375 10.5416 11.5 10.5416C12.2625 10.5416 12.9938 10.2387 13.5329 9.69952C14.0721 9.16035 14.375 8.42908 14.375 7.66658H16.2917C16.2917 8.93741 15.7868 10.1562 14.8882 11.0548C13.9896 11.9534 12.7708 12.4583 11.5 12.4583ZM11.5 2.87492C12.2625 2.87492 12.9938 3.17782 13.5329 3.71699C14.0721 4.25615 14.375 4.98742 14.375 5.74992H8.625C8.625 4.98742 8.9279 4.25615 9.46707 3.71699C10.0062 3.17782 10.7375 2.87492 11.5 2.87492ZM18.2083 5.74992H16.2917C16.2917 5.12067 16.1677 4.49758 15.9269 3.91623C15.6861 3.33488 15.3332 2.80665 14.8882 2.3617C14.4433 1.91675 13.915 1.5638 13.3337 1.323C12.7523 1.08219 12.1293 0.958252 11.5 0.958252C10.2292 0.958252 9.01039 1.46309 8.11178 2.3617C7.21317 3.26031 6.70833 4.47909 6.70833 5.74992H4.79167C3.72792 5.74992 2.875 6.60284 2.875 7.66658V19.1666C2.875 19.6749 3.07693 20.1624 3.43638 20.5219C3.79582 20.8813 4.28333 21.0833 4.79167 21.0833H18.2083C18.7167 21.0833 19.2042 20.8813 19.5636 20.5219C19.9231 20.1624 20.125 19.6749 20.125 19.1666V7.66658C20.125 7.15825 19.9231 6.67074 19.5636 6.3113C19.2042 5.95185 18.7167 5.74992 18.2083 5.74992Z"
                          fill="#4B5259"/>
                </svg>
                <p class="mr-3">اطلاعات سفارش</p>
            </div>
            <div class="my-5 md:mt-10 ">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 font-[500] text-[15px] text-[#525252]">
                    <ul class="space-y-2">
                        <li>کد پیگیری سفارش : <?php echo $order_id ?></li>
                        <li class="flex space-x-1 space-x-reverse "><p>تاریخ ثبت سفارش : </p>
                            <p class="wallet_add_amount"><?php echo $order->get_date_created()->format('Y-m-d '); ?></p>
                        </li>
                        <li class="flex space-x-1 space-x-reverse "><p class="">وضعیت سفارش : </p>
                            <p class="text-[#008826] "> تکمیل شد</p></li>
                    </ul>
                    <div class="col-span-2">
                        <ul class="space-y-2">
                            <li class="flex space-x-1 space-x-reverse"><p>نام تحویل گیرنده</p> :
                                <p><?php echo $user_data->first_name . " " . $user_data->last_name; ?> </p></li>
                            <li class="flex space-x-1 space-x-reverse"><p>شماره موبایل</p> :
                                <p><?php echo $user_data->user_login ?> </p></li>
                            <li class="flex space-x-1 space-x-reverse"><p>آدرس تحویل</p> :
                                <p><?php echo $order_address ?> </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class=" border-y w-full my-2 py-3 grid grid-cols-1 md:grid-cols-3 gap-5 md:gap-2 items-center">
                <div class="w-full md:w-auto text-center">
                    کل مبلغ پرداخت شده : <?php echo wc_price($order->get_total()); ?>
                </div>
                <div class="w-full md:w-auto text-center">
                    اعتبار واریز شده به کیف پول
                    : <?php echo wc_price(get_post_meta($order_id, '_wallet_charge_per_order_', true)) ?>
                </div>
                <div class="md:flex md:justify-end w-full md:w-auto">
                    <button class="w-full md:w-1/2 p-3 text-white bg-[#2F2483] rounded-lg">دریافت فاکتور سفارش</button>
                </div>
            </div>
            <div class="md:py-2">
            <div class="flex gap-2 font-[700] text-[20px] text-[#4B5259]">
                <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.19957 7.15383V6.53845C3.19957 5.06956 3.75801 3.66084 4.75203 2.62217C5.74605 1.58351 7.09424 1 8.5 1C9.90576 1 11.2539 1.58351 12.248 2.62217C13.242 3.66084 13.8004 5.06956 13.8004 6.53845V7.15383M6.14425 10.8461V13.3077M10.8557 10.8461V13.3077M15.9913 8.53229C16.0114 8.3605 15.9966 8.18621 15.9479 8.02079C15.8992 7.85537 15.8177 7.70255 15.7086 7.57229C15.5982 7.44101 15.4623 7.33579 15.31 7.26364C15.1577 7.19149 14.9924 7.15406 14.8252 7.15383H2.17482C2.00758 7.15406 1.8423 7.19149 1.68998 7.26364C1.53766 7.33579 1.4018 7.44101 1.29142 7.57229C1.18235 7.70255 1.1008 7.85537 1.05209 8.02079C1.00338 8.18621 0.988602 8.3605 1.00873 8.53229L1.89213 15.9169C1.92685 16.2171 2.06612 16.4934 2.28336 16.6932C2.50061 16.8929 2.78063 17.0022 3.07001 17H13.9536C14.2429 17.0022 14.5229 16.8929 14.7402 16.6932C14.9574 16.4934 15.0967 16.2171 15.1314 15.9169L15.9913 8.53229Z"
                          stroke="#4B5259" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p>سبد خرید</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-10 mt-5 relative">
                <?php
                foreach ($order->get_items() as $item_id => $item) {
                    $product = $item->get_product();
                    $product_name = $product->get_name();
                    $product_price = $product->get_price();
                    $product_quantity = $item->get_quantity();
                    $product_id = $product->get_id();
                    $product_link = $product->get_permalink();
                    $product_image = wp_get_attachment_image_url($product->get_image_id(), 'full');
                    echo '<div class="relative flex flex-col items-center justify-center gap-2 text-[#525252]">';
                    echo "<span class='rounded-full absolute px-2.5 top-1 right-6 md:right-3 text-white bg-[#E3000F] text-[18px]'>$product_quantity</span>";
                    echo "<img class='rounded-lg w-full ' src='$product_image'/>";
                    echo '<a href="' . $product_link . '"><h3 class="text-center text-[15px] font-[500]">' . $product_name . '</h3></a>';
                    echo '<p>قیمت: ' . wc_price($product_price) . '</p>';
                    echo "<button data-modal-target='submitComment' data-modal-toggle='submitComment' class='w-full text-center bg-[#E3000F] rounded-lg text-white text-[15px] p-1 product-submit' data-product-title='$product_name' value='$product_id'>ثبت دیدگاه</button>";
                    echo '</div>';
                } ?>
            </div>
            <?php
        }else{
            echo 'سفارش تکمیل نشده است';
        }
    }else{
        echo 'این سفارش جزو سفارشات شما نیست یا وجود ندارد.';
    }
} else {
    echo 'این سفارش جزو سفارشات شما نیست یا وجود ندارد.';
}