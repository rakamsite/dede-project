<?php
$guarantee_status = [ "در انتظار برسی", "درحال برسی", "تایید شد", "رد شد" ];
$args             = array(
	'author'      => get_current_user_id(),
	'post_type'   => 'guarantee',
	'post_status' => 'publish',
	'numberposts' => - 1,
);
$posts            = get_posts( $args );

$policy_text = cmb2_get_option( "guarantee_policy_page", "_dede_guarantee_text_field", "متن تستی شرایط دریافت خدمات گارانتی از شرکت برای محصولاتی که توسط فروشنده به خریدار فروخته شده است به این شرح است که باید برای دریافت گارانتی شرایطی که گفته شد را کالای فروخته شده داشته باشد در غیر این صورت کالا شامل گارانتی نخواهد شد." ) ?>
<div id="guaranty" class="flex flex-col gap-5 hidden">
    <div class="w-full flex gap-5">
        <svg class="scale-150" width="93" height="93" viewBox="0 0 93 93" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_216_2679" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="15" y="5" width="93"
                  height="93">
                <path d="M17.4375 7.75H75.5625V85.25L46.5 64.7687L17.4375 85.25V7.75Z" stroke="white" stroke-width="4"
                      stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M17.4375 7.75H75.5625V31H17.4375V7.75Z" fill="white" stroke="white" stroke-width="4"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </mask>
            <g mask="url(#mask0_216_2679)">
                <path d="M0 0H93V93H0V0Z" fill="black"/>
            </g>
        </svg>
        <div class="flex gap-1 items-center text-[#525252]">
            <p class="text-[15px] font-[500]">در صورت بروز مشکل با محصول خریداری شده و درخواست استفاده از خدمات گارانتی، لطفا فرم زیر را تکمیل تا پس از بررسی با شما تماس حاصل گردد.</p>
        </div>
    </div>
    <div class="md:border-[1px] border-[#4B5259] border-dashed rounded-lg px-2 py-5 grid grid-cols-1 md:grid-cols-3 gap-5">
        <select id="order_id"
                class="block mb-2 text-[18px] font-[500] font-medium text-[#4B5259] p-3 rounded-lg border-[1px] border-[#525252] bg-[#F2F2F2]">
            <option selected>یک سفارش را انتخاب کنید</option>
			<?php
			$args = array(
				'status'      => 'completed',
				'numberposts' => - 1,
				'post_type'   => 'shop_order',
				'orderby'     => 'date',
				'order'       => 'DESC',
				'customer'    => get_current_user_id(),
			);

			$orders = wc_get_orders( $args );

			if ( ! empty( $orders ) ) {
				foreach ( $orders as $order ) {
					$order_id = $order->get_id();
					echo "<option value='$order_id'>D$order_id</option>";
				}
			}
			?>
        </select>
        <select id="order_product_list"
                class="block mb-2 text-[18px] font-[500] font-medium text-[#4B5259] p-3 rounded-lg border-[1px] border-[#525252] bg-[#F2F2F2]">
            <option selected>یک محصول را انتخاب کنید</option>
        </select>
        <input id="product_count" type="number" placeholder="تعداد محصول مورد درخواست"
               class="block mb-2 text-[18px] font-[500] font-medium text-[#4B5259] placeholder:text-[#4B5259] p-3 rounded-lg border-[1px] border-[#525252] bg-[#F2F2F2]"/>
        <button type="button" id="submit_guaranty"
                class="p-3 rounded-lg bg-[#2F2483] text-white md:col-span-3 justify-self-end w-full md:w-1/4">
            ثبت درخواست گارانتی
        </button>
    </div>
    <div class="md:hidden">
        <h1 class="w-full text-right text-[#4B5259] text-[18px]">وضعیت درخواست ها</h1>
        <div class="divide-y">
			<?php
			if ( ! empty( $posts ) ):
			foreach ( $posts as $post ) :
			$order_id = get_post_meta( $post->ID, '_dede_guaranty_order_', true );
			$product_id = get_post_meta( $post->ID, '_dede_guaranty_product_', true );
			$product_count = get_post_meta( $post->ID, '_dede_guaranty_product_count_', true );
			$meta_status = get_post_meta( $post->ID, '_dede_guarantee_status_', true );
			$product = wc_get_product( $product_id );
			$post_id = $post->ID;
			$product_name = $product->get_name();
			$guarantee_status_text = "";
			switch ( $meta_status ) {
				case "2":
					$guarantee_status_text .= "<p class='text-[#008826]'>$guarantee_status[$meta_status]</p>";
					break;
				case "3":
					$guarantee_status_text .= "<p class='text-[#E3000F]'>$guarantee_status[$meta_status]</p>";
					break;
				default:
					$guarantee_status_text .= "<p>$guarantee_status[$meta_status]</p>";
			}

			echo "<div class='w-full flex justify-between p-3'><p>درخواست D$order_id</p><button class='text-[#0058BF]' type='button' data-drawer-target='$post_id' data-drawer-show='$post_id' data-drawer-placement='bottom' aria-controls='$post_id'>مشاهده وضعیت 〱</button></div>";
			?>
            <div id='<?php echo $post_id ?>'
                 class='fixed bottom-0 left-0 right-0 z-40 h-screen pt-10 w-full  bg-white p-2  transition-transform translate-y-full '>
                <div>
                    <div class='w-full relative p-5 mt-10'>
                        <div class='w-full text-center my-auto'><p class='text-[24px] font-[700]'>درخواست g#<?php echo $post_id?></p>
                        </div>
                        <button data-drawer-hide='<?php echo $post_id?>' class='absolute left-0 top-3'>
                            <svg width='54' height='54' viewBox='0 0 54 54' fill='none'
                                 xmlns='http://www.w3.org/2000/svg'>
                                <rect x='38.5374' y='11.6675' width='5' height='38' rx='2.5'
                                      transform='rotate(45 38.5374 11.6675)' fill='#525252'/>
                                <rect x='42.073' y='38.5371' width='5' height='38' rx='2.5'
                                      transform='rotate(135 42.073 38.5371)' fill='#525252'/>
                            </svg>
                        </button>
                    </div>
                    <div class='flex flex-col divide-y mt-10 gap-10 px-10'>
                        <div class='flex justify-between'><p>تاریخ ثبت</p><p class="wallet_add_amount"><?php echo $post->post_date?></p></div>
                        <div class='flex justify-between'><p>شماره سفارش</p><p>D<?php echo $order_id; ?></p></div>
                        <div class='flex justify-between'><p>محصول</p><p class='truncate'><?php echo $product_name?> </p></div>
                        <div class='flex justify-between'><p>وضعیت</p><?php echo $guarantee_status_text ?></div>

                    </div>
                </div>
            </div>
            <?php
				endforeach;
			endif;
			?>
        </div>
    </div>
    <div class="hidden md:block">
        <div class="grid grid-cols-5 text-[#525252] p-3 bg-[#F2F2F2] rounded-lg justify-items-center ">
            <div class="">شماره درخواست</div>
            <div class=""> تاریخ</div>
            <div class="">شماره سفارش</div>
            <div class="">محصول</div>
            <div class="">وضعیت</div>
        </div>
        <div class="divide-y mt-5">
			<?php
			if ( ! empty( $posts ) ):
				foreach ( $posts as $post ) :
					$order_id = get_post_meta( $post->ID, '_dede_guaranty_order_', true );
					$product_id = get_post_meta( $post->ID, '_dede_guaranty_product_', true );
					$product_count = get_post_meta( $post->ID, '_dede_guaranty_product_count_', true );
					$meta_status = get_post_meta( $post->ID, '_dede_guarantee_status_', true );
					$product = wc_get_product( $product_id );

					?>
                    <div class="grid grid-cols-5 justify-items-center items-center py-3">
                        <div class=""><?php echo "درخواستG# " . $product_id ?></div>
                        <div class="wallet_add_amount"><?php echo $post->post_date ?></div>
                        <div class=""><?php echo 'D' . $order_id; ?></div>
                        <div class="px-1"><p class="truncate"><?php echo $product->get_name(); ?></p></div>
                        <div class=""><?php
							switch ( $meta_status ) {
								case "2":
									echo "<p class='text-[#008826]'>$guarantee_status[$meta_status]</p>";
									break;
								case "3":
									echo "<p class='text-[#E3000F]'>$guarantee_status[$meta_status]</p>";
									break;
								default:
									echo "<p>$guarantee_status[$meta_status]</p>";
							}
							?></div>
                    </div>
				<?php endforeach; endif; ?>
        </div>
    </div>
</div>