<div id="after_add_to_card"
     class="fixed top-1/2 md:left-1/2 transform -translate-y-1/2 md:-translate-x-1/2 z-[999999] hidden p-4 overflow-x-hidden overflow-y-auto md:w-[650px] w-full md:h-[350px] bg-gray-50 text-black flex flex-col-reverse rounded-lg shadow-lg ">
	<div class="grid grid-cols-1 md:grid-cols-3 md:gap-5 gap-1 mt-5 md:mt-0 w-full">
		<a href="<?php echo esc_url(wc_get_cart_url())  ?>" class="bg-[#2F2483] text-white text-center text-[18px] font-[500] py-2 rounded-lg"> سبد خرید</a>
		<a href="<?php echo esc_url(wc_get_checkout_url()) ?>" class="bg-[#2F2483]  text-center text-white text-[18px] font-[500] py-2 rounded-lg">ثبت سفارش</a>
		<button class="close_after_add_to_cart bg-[#2F2483] text-white text-[18px] font-[500] py-2 rounded-lg">ادامه خرید</button>
	</div>
	<div class="flex justify-start w-full my-auto item_added_to_cart"></div>
	<div class="w-full p-3 flex justify-between">
		<p class="text-lg md:text-[24px] font-[700] add_to_cart_status_after_add_to_cart"></p>
		<button class="close_after_add_to_cart"  data-modal-target="after_add_to_card" type="button">
			<svg aria-hidden="true"
			     class="h-8 text-[#D9D9D9] cursor-pointer"
			     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
				<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
				      d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
			</svg>
		</button>
	</div>
</div>