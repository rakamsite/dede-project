<nav class="shadow w-full z-50">
    <div class="flex flex-row-reverse md:grid grid-cols-12 bg-white p-4 md:p-0 lg:container mx-auto relative">
        <div class="my-auto col-span-2">
			<?php
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			$phone_number = cmb2_get_option( 'phone_option', 'phone_active' );
			$link_tell_us = cmb2_get_option( 'phone_option', 'link_active' );
            if ( $custom_logo_id ) {
				$custom_logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
				$custom_logo_img = wp_get_attachment_image( $custom_logo_id, 'custom-logo-size' );
				echo '<a class="inline-block w-fit" href="' . esc_url( home_url( '/' ) ) . '" rel="home"><img src=" w-[170px] h-full ' . esc_url( $custom_logo_img[0] ) . '" alt="' . get_bloginfo( 'name' ) . '"></a>';
			} else {
				echo '<a class="inline-block w-fit" href="' . esc_url( home_url( '/' ) ) . '" rel="home"> <img class=" w-[170px] h-full" alt="' . get_bloginfo( 'name' ) . '"src="' . dedeTemplate . '/assets/image/site-dede.svg' . '"/>' . '</a>';
			}
			function car_red_dot() {
				$card_count = WC()->cart->get_cart_contents_count();
				if ( $card_count ) {
					echo '<span class="card-red-dot absolute top-1 right-1 inline-flex h-3 px-1 rounded-full bg-[#E3000F] text-white text-xs">' . $card_count . '</span>';
				} else {
					echo '<span class="card-red-dot absolute top-1 right-1 inline-flex h-3 px-1 rounded-full bg-[#E3000F] text-white text-xs hidden"></span>';
				}
			}
			$firs_name    = false;

			if (is_user_logged_in()){
	            $current_user = wp_get_current_user();
	            $firs_name    = $current_user->first_name;
            }
			?>
        </div>
        <div class="hidden col-span-6 md:flex gap-1 lg:gap-2 xl:gap-10 my-auto">
            <div id="product"
                 class="flex items-center h-[87px] menu-main text-[#525252] transition-all cursor-pointer">
                محصولات
                <svg class="mr-1" width="12" height="6" viewBox="0 0 14 8"
                     fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 0.999939L7 6.99994L13 0.99994" stroke="#525252" stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </div>
            <div id="categories"
                 class="flex items-center h-[87px] menu-main text-[#525252] transition-all cursor-pointer">
                دسته بندی ها
                <svg class="mr-1" width="12" height="6" viewBox="0 0 14 8"
                     fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 0.999939L7 6.99994L13 0.99994" stroke="#525252" stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </div>
            <div id="received"
                 class="flex items-center h-[87px] menu-main text-[#525252] transition-all cursor-pointer">
                دریافت ها
                <svg class="mr-1" width="12" height="6" viewBox="0 0 14 8"
                     fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 0.999939L7 6.99994L13 0.99994" stroke="#525252" stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </div>
            <div id="about" class="flex items-center h-[87px] menu-main text-[#525252] transition-all cursor-pointer">
                نمایه
                <svg class=" mr-1" width="12" height="6" viewBox="0 0 14 8"
                     fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 0.999939L7 6.99994L13 0.99994" stroke="#525252" stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </div>
            <div id="contact_us" class="flex items-center h-[87px] menu-main text-[#525252] transition-all cursor-pointer">
                تماس
                <svg class=" mr-1" width="12" height="6" viewBox="0 0 14 8"
                     fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 0.999939L7 6.99994L13 0.99994" stroke="#525252" stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <div class="col-span-2 flex flex-row-reverse md:flex-row justify-end grow my-auto lg:ml-5 gap-2 lg:gap-8 ">
            <button class="mt-2 open_search_box" aria-label="سرچ">
                <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M38.0625 36.2066L28.1505 26.2946C30.5324 23.4351 31.7202 19.7673 31.4667 16.0544C31.2133 12.3414 29.5381 8.86907 26.7898 6.35974C24.0414 3.8504 20.4313 2.49726 16.7107 2.5818C12.99 2.66634 9.44521 4.18205 6.81363 6.81363C4.18205 9.44521 2.66634 12.99 2.5818 16.7107C2.49726 20.4313 3.8504 24.0414 6.35974 26.7898C8.86907 29.5381 12.3414 31.2133 16.0544 31.4667C19.7673 31.7202 23.4351 30.5324 26.2946 28.1505L36.2066 38.0625L38.0625 36.2066ZM5.25 17.0625C5.25 14.7262 5.94279 12.4424 7.24076 10.4998C8.53874 8.55727 10.3836 7.04323 12.5421 6.14917C14.7005 5.25511 17.0756 5.02119 19.367 5.47697C21.6584 5.93276 23.7632 7.05779 25.4152 8.7098C27.0672 10.3618 28.1922 12.4666 28.648 14.758C29.1038 17.0494 28.8699 19.4245 27.9758 21.5829C27.0818 23.7414 25.5677 25.5863 23.6252 26.8842C21.6826 28.1822 19.3988 28.875 17.0625 28.875C13.9307 28.8715 10.9282 27.6259 8.71364 25.4114C6.49912 23.1968 5.25347 20.1943 5.25 17.0625Z"
                          fill="#BCBCBC"/>
                </svg>
            </button>
            <button onclick="window.open('tel:<?php echo $phone_number ?>')" aria-label="تماس با ما" class="hidden md:block mt-1 ml-2" >
                <svg width="28" height="37" viewBox="0 0 28 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.56267 0.984427L7.12514 0.213561C8.2916 -0.137916 9.54628 -0.0534169 10.6549 0.451286C11.7636 0.955988 12.6505 1.8464 13.15 2.9563L14.7446 6.4992C15.1743 7.45363 15.294 8.51846 15.0869 9.54429C14.8798 10.5701 14.3564 11.5054 13.59 12.219L10.8037 14.8138C10.7692 14.8459 10.741 14.884 10.7203 14.9263C10.4286 15.5214 10.8716 17.1109 12.2593 19.513C13.8246 22.2202 15.0333 23.2902 15.5936 23.1252L19.2505 22.0075C20.252 21.702 21.3241 21.7172 22.3165 22.0509C23.3089 22.3846 24.1719 23.0201 24.7845 23.8683L27.0506 27.0027C27.7621 27.9868 28.0916 29.1954 27.978 30.404C27.8645 31.6125 27.3155 32.7388 26.4331 33.5735L24.4835 35.4159C23.8055 36.0573 22.9823 36.5253 22.0839 36.7801C21.1856 37.0349 20.2389 37.0688 19.3246 36.879C13.8956 35.7505 9.03155 31.3843 4.6908 23.876C0.348501 16.3632 -1.00528 9.96501 0.739044 4.70308C1.03104 3.82214 1.53105 3.02435 2.19684 2.3771C2.86262 1.72985 3.67308 1.25217 4.56267 0.984427ZM5.23262 3.19835C4.69884 3.35893 4.2116 3.64547 3.81207 4.03376C3.41255 4.42206 3.11247 4.9007 2.93721 5.42924C1.43369 9.96347 2.65163 15.7234 6.696 22.7198C10.7373 29.7115 15.1182 33.6429 19.7954 34.6142C20.3443 34.7282 20.9126 34.7078 21.4518 34.5548C21.9911 34.4017 22.4852 34.1206 22.892 33.7354L24.8401 31.8946C25.3154 31.4452 25.6112 30.8388 25.6725 30.188C25.7338 29.5372 25.5565 28.8864 25.1735 28.3563L22.9074 25.2204C22.5776 24.7638 22.1131 24.4217 21.5788 24.242C21.0446 24.0623 20.4674 24.054 19.9282 24.2183L16.262 25.3392C14.2337 25.942 12.3257 24.2538 10.2541 20.6677C8.49899 17.6336 7.89079 15.4412 8.641 13.9103C8.78611 13.6143 8.98369 13.3476 9.2245 13.1225L12.0108 10.5277C12.4236 10.1435 12.7056 9.63981 12.8172 9.08734C12.9288 8.53487 12.8643 7.96138 12.6329 7.44736L11.0383 3.90601C10.7693 3.30822 10.2917 2.82864 9.69458 2.55685C9.09748 2.28505 8.42173 2.23962 7.79354 2.42903L5.23107 3.19989L5.23262 3.19835Z"
                          fill="#BCBCBC"/>
                </svg>
            </button>
            <button class="mb-1 relative" id="card_information" aria-label="سبد خرید" type="button" data-drawer-target="card-information" data-drawer-placement="left"
                    data-drawer-show="card-information" data-drawer-body-scrolling="false"  aria-controls="card-information">
                <svg width="39" height="43" viewBox="0 0 39 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.13233 18.8461V17.4615C7.13233 14.1565 8.43535 10.9869 10.7547 8.64989C13.0741 6.31291 16.2199 5 19.5 5C22.7801 5 25.9259 6.31291 28.2453 8.64989C30.5646 10.9869 31.8677 14.1565 31.8677 17.4615V18.8461M14.0033 27.1538V32.6923M24.9967 27.1538V32.6923M36.9796 21.9477C37.0266 21.5611 36.9921 21.169 36.8785 20.7968C36.7648 20.4246 36.5745 20.0807 36.32 19.7877C36.0625 19.4923 35.7455 19.2555 35.39 19.0932C35.0346 18.9308 34.649 18.8466 34.2587 18.8461H4.74125C4.35102 18.8466 3.96538 18.9308 3.60996 19.0932C3.25455 19.2555 2.93752 19.4923 2.67997 19.7877C2.42548 20.0807 2.23519 20.4246 2.12154 20.7968C2.00788 21.169 1.9734 21.5611 2.02037 21.9477L4.08164 38.563C4.16265 39.2385 4.48761 39.8602 4.99451 40.3097C5.50142 40.7591 6.1548 41.0048 6.83001 40.9999H32.225C32.9002 41.0048 33.5536 40.7591 34.0605 40.3097C34.5674 39.8602 34.8923 39.2385 34.9733 38.563L36.9796 21.9477Z"
                          stroke="#BCBCBC" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
				<?php car_red_dot() ?>
            </button>
            <button id="open_menu_mobile" aria-label="منوی موبایل" type="button" data-drawer-target="mobile-menu" data-drawer-show="mobile-menu" data-drawer-body-scrolling="false"
                    data-drawer-placement="right" aria-controls="mobile-menu" class="md:hidden">
                <svg width="43" height="44" viewBox="0 0 43 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.16667 22C7.16667 21.5138 7.35544 21.0475 7.69144 20.7036C8.02744 20.3598 8.48316 20.1667 8.95834 20.1667L34.0417 20.1667C34.5169 20.1667 34.9726 20.3598 35.3086 20.7036C35.6446 21.0475 35.8333 21.5138 35.8333 22C35.8333 22.4862 35.6446 22.9525 35.3086 23.2964C34.9726 23.6402 34.5169 23.8333 34.0417 23.8333L8.95834 23.8333C8.48316 23.8333 8.02744 23.6402 7.69144 23.2964C7.35544 22.9525 7.16667 22.4862 7.16667 22ZM14.3333 12.8333C14.3333 12.3471 14.5221 11.8808 14.8581 11.537C15.1941 11.1932 15.6498 11 16.125 11L34.0417 11C34.5169 11 34.9726 11.1932 35.3086 11.537C35.6446 11.8808 35.8333 12.3471 35.8333 12.8333C35.8333 13.3196 35.6446 13.7859 35.3086 14.1297C34.9726 14.4735 34.5169 14.6667 34.0417 14.6667L16.125 14.6667C15.6498 14.6667 15.1941 14.4735 14.8581 14.1297C14.5221 13.7859 14.3333 13.3196 14.3333 12.8333ZM21.5 31.1667C21.5 30.6804 21.6888 30.2141 22.0248 29.8703C22.3608 29.5265 22.8165 29.3333 23.2917 29.3333L34.0417 29.3333C34.5169 29.3333 34.9726 29.5265 35.3086 29.8703C35.6446 30.2141 35.8333 30.6804 35.8333 31.1667C35.8333 31.6529 35.6446 32.1192 35.3086 32.463C34.9726 32.8068 34.5169 33 34.0417 33L23.2917 33C22.8165 33 22.3608 32.8068 22.0248 32.463C21.6888 32.1192 21.5 31.6529 21.5 31.1667Z"
                          fill="#BCBCBC"/>
                </svg>
            </button>
        </div>
        <div class="hidden md:block col-span-2 my-auto w-full md:pr-5">
            <?php $adminPanelLink ="'".home_url( '/my-account' )."'"; ?>
            <button <?php  echo is_user_logged_in() ? 'onclick="location.href='.$adminPanelLink.';"' : ''; ?> aria-label="حساب کاربری" class="bg-[#2F2483] w-full rounded-lg h-fit text-white flex py-3 px-5 mt-1/2 flex justify-center  <?php echo ( ! is_user_logged_in() ) ? 'login_register_page' : '' ?> <?php echo !$firs_name ? 'forMyAccountButton':'0'?>" <?php echo !$firs_name ? 'data-my-account="Information"':'0'?>>
                <svg width="21" height="26" viewBox="0 0 21 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.3334 10.3333C13.1868 10.3333 15.5 8.02014 15.5 5.16667C15.5 2.3132 13.1868 0 10.3334 0C7.47988 0 5.16669 2.3132 5.16669 5.16667C5.16669 8.02014 7.47988 10.3333 10.3334 10.3333Z"
                          fill="white"/>
                    <path d="M20.6667 20.0208C20.6667 23.2306 20.6667 25.8333 10.3333 25.8333C0 25.8333 0 23.2306 0 20.0208C0 16.8111 4.62675 14.2083 10.3333 14.2083C16.0399 14.2083 20.6667 16.8111 20.6667 20.0208Z"
                          fill="white"/>
                </svg>
                <a class="mr-2" <?php echo is_user_logged_in() ? 'href="' . home_url( '/my-account' ) . '"' : 'href="#"' ?>>
					<?php
					if ( is_user_logged_in() ) {
						if ( empty( $firs_name ) ) {
							echo "حساب کاربری";
						} else {
							if ( $current_user->roles[0] === "company" ) {
								$CompanyName = get_user_meta( $current_user->ID, "billing_company", true );
								if ( $CompanyName ) {
									echo $CompanyName;
								} else {
									echo "حساب کاربری";
								}
							} else {
								echo $firs_name . ' ' . $current_user->last_name;
							}
						}
					} else {
						echo 'ورود/ثبت نام';
					}
					?>
                </a>
            </button>
        </div>
        <div id="mobile-menu"
             class="block md:hidden fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-full flex flex-col gap-2 space-y-2" aria-hidden="false">
            <div class="flex flex-row items-center text-[#525252] relative">
                <svg width="43" height="44" viewBox="0 0 43 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.16669 22C7.16669 21.5138 7.35545 21.0475 7.69145 20.7036C8.02746 20.3598 8.48317 20.1667 8.95835 20.1667L34.0417 20.1667C34.5169 20.1667 34.9726 20.3598 35.3086 20.7036C35.6446 21.0475 35.8334 21.5138 35.8334 22C35.8334 22.4862 35.6446 22.9525 35.3086 23.2964C34.9726 23.6402 34.5169 23.8333 34.0417 23.8333L8.95835 23.8333C8.48317 23.8333 8.02746 23.6402 7.69145 23.2964C7.35545 22.9525 7.16669 22.4862 7.16669 22ZM14.3334 12.8333C14.3334 12.3471 14.5221 11.8808 14.8581 11.537C15.1941 11.1932 15.6498 11 16.125 11L34.0417 11C34.5169 11 34.9726 11.1932 35.3086 11.537C35.6446 11.8808 35.8334 12.3471 35.8334 12.8333C35.8334 13.3196 35.6446 13.7859 35.3086 14.1297C34.9726 14.4735 34.5169 14.6667 34.0417 14.6667L16.125 14.6667C15.6498 14.6667 15.1941 14.4735 14.8581 14.1297C14.5221 13.7859 14.3334 13.3196 14.3334 12.8333ZM21.5 31.1667C21.5 30.6804 21.6888 30.2141 22.0248 29.8703C22.3608 29.5265 22.8165 29.3333 23.2917 29.3333L34.0417 29.3333C34.5169 29.3333 34.9726 29.5265 35.3086 29.8703C35.6446 30.2141 35.8334 30.6804 35.8334 31.1667C35.8334 31.6529 35.6446 32.1192 35.3086 32.463C34.9726 32.8068 34.5169 33 34.0417 33L23.2917 33C22.8165 33 22.3608 32.8068 22.0248 32.463C21.6888 32.1192 21.5 31.6529 21.5 31.1667Z"
                          fill="#525252"/>
                </svg>
                <p class="text-[18px] w-full">فهرست</p>
                <button type="button" aria-label="منوی موبایل" class="float-left" data-drawer-hide="mobile-menu">
                    <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="27.3989" y="9.00598" width="3.55485" height="27.0169" rx="1.77743"
                              transform="rotate(45 27.3989 9.00598)" fill="#525252"/>
                        <rect x="29.9125" y="28.1099" width="3.55485" height="27.0169" rx="1.77743"
                              transform="rotate(135 29.9125 28.1099)" fill="#525252"/>
                    </svg>
                </button>
            </div>
            <div>
                <a <?php echo is_user_logged_in() ? 'href="' . home_url( '/my-account' ) . '"' : ''; ?> >
                    <button data-drawer-hide="mobile-menu" aria-label="منوی موبایل"
                            class="bg-[#2F2483] w-full rounded-lg h-[50px] text-white flex py-3 px-5 mt-1/2 flex justify-center gap-2 <?php echo ( ! is_user_logged_in() ) ? 'login_register_page' : '' ?>">
                        <p class="mr-2 text-[18px]">
							<?php
							if ( is_user_logged_in() ) {
								$current_user = wp_get_current_user();
								$firs_name    = $current_user->first_name;
								if ( empty( $firs_name ) ) {
									echo "حساب کاربری";
								} else {
									if ( $current_user->roles[0] === "company" ) {
										$CompanyName = get_user_meta( $current_user->ID, "billing_company", true );
										if ( $CompanyName ) {
											echo $CompanyName;
										} else {
											echo "حساب کاربری";
										}
									} else {
										echo $firs_name . ' ' . $current_user->last_name;
									}
								}
							} else {
								echo 'ورود/ثبت نام';
							}
							?>
                        </p>
                        <svg width="21" height="26" viewBox="0 0 21 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.3334 10.3333C13.1868 10.3333 15.5 8.02014 15.5 5.16667C15.5 2.3132 13.1868 0 10.3334 0C7.47988 0 5.16669 2.3132 5.16669 5.16667C5.16669 8.02014 7.47988 10.3333 10.3334 10.3333Z"
                                  fill="white"/>
                            <path d="M20.6667 20.0208C20.6667 23.2306 20.6667 25.8333 10.3333 25.8333C0 25.8333 0 23.2306 0 20.0208C0 16.8111 4.62675 14.2083 10.3333 14.2083C16.0399 14.2083 20.6667 16.8111 20.6667 20.0208Z"
                                  fill="white"/>
                        </svg>

                    </button>
                </a>
            </div>
            <?php if (wp_is_mobile()) : ?>
            <div class="divide-y text-[#525252] text-[18px] ">
                <button aria-label="محصولات" class="p-4 flex justify-between w-full" data-drawer-target="mobile_menu_product"
                        data-drawer-show="mobile_menu_product" data-drawer-placement="right"
                        aria-controls="mobile_menu_product">
                    <p>محصولات</p>
                    <svg width="11" height="23" viewBox="0 0 11 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.259789 12.2165L9.59441 22.7267C9.75016 22.9021 9.95709 23 10.1723 23C10.3875 23 10.5944 22.9021 10.7501 22.7267L10.7602 22.7148C10.836 22.6297 10.8963 22.5274 10.9375 22.4139C10.9787 22.3004 11 22.1782 11 22.0547C11 21.9312 10.9787 21.809 10.9375 21.6955C10.8963 21.582 10.836 21.4796 10.7602 21.3946L1.96993 11.498L10.7602 1.60541C10.836 1.52036 10.8963 1.41797 10.9375 1.30449C10.9787 1.19101 11 1.06881 11 0.945313C11 0.821816 10.9787 0.699612 10.9375 0.586132C10.8963 0.472653 10.836 0.37027 10.7602 0.285212L10.7501 0.273335C10.5944 0.0978797 10.3875 2.60813e-07 10.1723 2.5541e-07C9.95709 2.50007e-07 9.75016 0.0978797 9.59441 0.273335L0.259789 10.7835C0.177695 10.8759 0.112339 10.9871 0.0676826 11.1102C0.0230266 11.2334 1.38734e-07 11.366 1.37136e-07 11.5C1.35538e-07 11.634 0.0230266 11.7666 0.0676826 11.8898C0.112339 12.0129 0.177695 12.1241 0.259789 12.2165Z"
                              fill="#525252"/>
                    </svg>
                </button>
                <button aria-label="دسته بندی ها" class="p-4 flex justify-between w-full" data-drawer-target="mobile_categories_menu"
                        data-drawer-show="mobile_categories_menu" data-drawer-placement="right"
                        aria-controls="mobile_categories_menu">
                    <p>دسته بندی ها</p>
                    <svg width="11" height="23" viewBox="0 0 11 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.259789 12.2165L9.59441 22.7267C9.75016 22.9021 9.95709 23 10.1723 23C10.3875 23 10.5944 22.9021 10.7501 22.7267L10.7602 22.7148C10.836 22.6297 10.8963 22.5274 10.9375 22.4139C10.9787 22.3004 11 22.1782 11 22.0547C11 21.9312 10.9787 21.809 10.9375 21.6955C10.8963 21.582 10.836 21.4796 10.7602 21.3946L1.96993 11.498L10.7602 1.60541C10.836 1.52036 10.8963 1.41797 10.9375 1.30449C10.9787 1.19101 11 1.06881 11 0.945313C11 0.821816 10.9787 0.699612 10.9375 0.586132C10.8963 0.472653 10.836 0.37027 10.7602 0.285212L10.7501 0.273335C10.5944 0.0978797 10.3875 2.60813e-07 10.1723 2.5541e-07C9.95709 2.50007e-07 9.75016 0.0978797 9.59441 0.273335L0.259789 10.7835C0.177695 10.8759 0.112339 10.9871 0.0676826 11.1102C0.0230266 11.2334 1.38734e-07 11.366 1.37136e-07 11.5C1.35538e-07 11.634 0.0230266 11.7666 0.0676826 11.8898C0.112339 12.0129 0.177695 12.1241 0.259789 12.2165Z"
                              fill="#525252"/>
                    </svg>
                </button>
                <button aria-label="دریافت ها" class="p-4 flex justify-between w-full" data-drawer-target="mobile_received_menu"
                        data-drawer-show="mobile_received_menu" data-drawer-placement="right"
                        aria-controls="mobile_received_menu">
                    <p>دریافت ها</p>
                    <svg width="11" height="23" viewBox="0 0 11 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.259789 12.2165L9.59441 22.7267C9.75016 22.9021 9.95709 23 10.1723 23C10.3875 23 10.5944 22.9021 10.7501 22.7267L10.7602 22.7148C10.836 22.6297 10.8963 22.5274 10.9375 22.4139C10.9787 22.3004 11 22.1782 11 22.0547C11 21.9312 10.9787 21.809 10.9375 21.6955C10.8963 21.582 10.836 21.4796 10.7602 21.3946L1.96993 11.498L10.7602 1.60541C10.836 1.52036 10.8963 1.41797 10.9375 1.30449C10.9787 1.19101 11 1.06881 11 0.945313C11 0.821816 10.9787 0.699612 10.9375 0.586132C10.8963 0.472653 10.836 0.37027 10.7602 0.285212L10.7501 0.273335C10.5944 0.0978797 10.3875 2.60813e-07 10.1723 2.5541e-07C9.95709 2.50007e-07 9.75016 0.0978797 9.59441 0.273335L0.259789 10.7835C0.177695 10.8759 0.112339 10.9871 0.0676826 11.1102C0.0230266 11.2334 1.38734e-07 11.366 1.37136e-07 11.5C1.35538e-07 11.634 0.0230266 11.7666 0.0676826 11.8898C0.112339 12.0129 0.177695 12.1241 0.259789 12.2165Z"
                              fill="#525252"/>
                    </svg>
                </button>
                <button aria-label="درباره" class="p-4 flex justify-between w-full" data-drawer-target="mobile_about_menu"
                        data-drawer-show="mobile_about_menu" data-drawer-placement="right"
                        aria-controls="mobile_about_menu">
                    <p>نمایه</p>
                    <svg width="11" height="23" viewBox="0 0 11 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.259789 12.2165L9.59441 22.7267C9.75016 22.9021 9.95709 23 10.1723 23C10.3875 23 10.5944 22.9021 10.7501 22.7267L10.7602 22.7148C10.836 22.6297 10.8963 22.5274 10.9375 22.4139C10.9787 22.3004 11 22.1782 11 22.0547C11 21.9312 10.9787 21.809 10.9375 21.6955C10.8963 21.582 10.836 21.4796 10.7602 21.3946L1.96993 11.498L10.7602 1.60541C10.836 1.52036 10.8963 1.41797 10.9375 1.30449C10.9787 1.19101 11 1.06881 11 0.945313C11 0.821816 10.9787 0.699612 10.9375 0.586132C10.8963 0.472653 10.836 0.37027 10.7602 0.285212L10.7501 0.273335C10.5944 0.0978797 10.3875 2.60813e-07 10.1723 2.5541e-07C9.95709 2.50007e-07 9.75016 0.0978797 9.59441 0.273335L0.259789 10.7835C0.177695 10.8759 0.112339 10.9871 0.0676826 11.1102C0.0230266 11.2334 1.38734e-07 11.366 1.37136e-07 11.5C1.35538e-07 11.634 0.0230266 11.7666 0.0676826 11.8898C0.112339 12.0129 0.177695 12.1241 0.259789 12.2165Z"
                              fill="#525252"/>
                    </svg>
                </button>
                <button aria-label="تماس با ما" class="p-4 flex justify-between w-full" data-drawer-target="contact_us_menu_mobile"
                        data-drawer-show="contact_us_menu_mobile" data-drawer-placement="right"
                        aria-controls="contact_us_menu_mobile">
                    <p>تماس</p>
                    <svg width="11" height="23" viewBox="0 0 11 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.259789 12.2165L9.59441 22.7267C9.75016 22.9021 9.95709 23 10.1723 23C10.3875 23 10.5944 22.9021 10.7501 22.7267L10.7602 22.7148C10.836 22.6297 10.8963 22.5274 10.9375 22.4139C10.9787 22.3004 11 22.1782 11 22.0547C11 21.9312 10.9787 21.809 10.9375 21.6955C10.8963 21.582 10.836 21.4796 10.7602 21.3946L1.96993 11.498L10.7602 1.60541C10.836 1.52036 10.8963 1.41797 10.9375 1.30449C10.9787 1.19101 11 1.06881 11 0.945313C11 0.821816 10.9787 0.699612 10.9375 0.586132C10.8963 0.472653 10.836 0.37027 10.7602 0.285212L10.7501 0.273335C10.5944 0.0978797 10.3875 2.60813e-07 10.1723 2.5541e-07C9.95709 2.50007e-07 9.75016 0.0978797 9.59441 0.273335L0.259789 10.7835C0.177695 10.8759 0.112339 10.9871 0.0676826 11.1102C0.0230266 11.2334 1.38734e-07 11.366 1.37136e-07 11.5C1.35538e-07 11.634 0.0230266 11.7666 0.0676826 11.8898C0.112339 12.0129 0.177695 12.1241 0.259789 12.2165Z"
                              fill="#525252"/>
                    </svg>
                </button>
            </div>
            <?php endif; ?>
            <button aria-label="تماس تلفنی" onclick="window.open('tel:<?php echo $phone_number ?>')" class="rounded-lg bg-[#E3000F] py-3 px-5 h-[50px] text-white flex justify-center gap-2 items-center">
                <p class="text-[18px]">تماس تلفنی</p>
                <svg width="26" height="30" viewBox="0 0 28 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.56267 0.984427L7.12514 0.213561C8.2916 -0.137916 9.54628 -0.0534169 10.6549 0.451286C11.7636 0.955988 12.6505 1.8464 13.15 2.9563L14.7446 6.4992C15.1743 7.45363 15.294 8.51846 15.0869 9.54429C14.8798 10.5701 14.3564 11.5054 13.59 12.219L10.8037 14.8138C10.7692 14.8459 10.741 14.884 10.7203 14.9263C10.4286 15.5214 10.8716 17.1109 12.2593 19.513C13.8246 22.2202 15.0333 23.2902 15.5936 23.1252L19.2505 22.0075C20.252 21.702 21.3241 21.7172 22.3165 22.0509C23.3089 22.3846 24.1719 23.0201 24.7845 23.8683L27.0506 27.0027C27.7621 27.9868 28.0916 29.1954 27.978 30.404C27.8645 31.6125 27.3155 32.7388 26.4331 33.5735L24.4835 35.4159C23.8055 36.0573 22.9823 36.5253 22.0839 36.7801C21.1856 37.0349 20.2389 37.0688 19.3246 36.879C13.8956 35.7505 9.03155 31.3843 4.6908 23.876C0.348501 16.3632 -1.00528 9.96501 0.739044 4.70308C1.03104 3.82214 1.53105 3.02435 2.19684 2.3771C2.86262 1.72985 3.67308 1.25217 4.56267 0.984427ZM5.23262 3.19835C4.69884 3.35893 4.2116 3.64547 3.81207 4.03376C3.41255 4.42206 3.11247 4.9007 2.93721 5.42924C1.43369 9.96347 2.65163 15.7234 6.696 22.7198C10.7373 29.7115 15.1182 33.6429 19.7954 34.6142C20.3443 34.7282 20.9126 34.7078 21.4518 34.5548C21.9911 34.4017 22.4852 34.1206 22.892 33.7354L24.8401 31.8946C25.3154 31.4452 25.6112 30.8388 25.6725 30.188C25.7338 29.5372 25.5565 28.8864 25.1735 28.3563L22.9074 25.2204C22.5776 24.7638 22.1131 24.4217 21.5788 24.242C21.0446 24.0623 20.4674 24.054 19.9282 24.2183L16.262 25.3392C14.2337 25.942 12.3257 24.2538 10.2541 20.6677C8.49899 17.6336 7.89079 15.4412 8.641 13.9103C8.78611 13.6143 8.9837 13.3476 9.2245 13.1225L12.0108 10.5277C12.4236 10.1435 12.7056 9.63981 12.8172 9.08734C12.9288 8.53487 12.8643 7.96138 12.6329 7.44736L11.0383 3.90601C10.7693 3.30822 10.2917 2.82864 9.69458 2.55685C9.09748 2.28505 8.42173 2.23962 7.79354 2.42903L5.23107 3.19989L5.23262 3.19835Z"
                          fill="white"/>
                </svg>
            </button>
            <div class="md:my-auto container mx-auto md:p-0">
                <div id="sib_embed_signup">
                    <div class="forms-builder-wrapper" style="position:relative;margin-left: auto;margin-right: auto;"> <input
                                type="hidden" id="sib_embed_signup_lang" value="en"> <input type="hidden"
                                                                                            id="sib_embed_invalid_email_message" value="لطفا یک ایمیل معتبر وارد نمایید"> <input type="hidden"
                                                                                                                                                                                 name="primary_type" id="primary_type" value="email">
                        <div id="sib_loading_gif_area" style="position: absolute;z-index: 9999;display: none;"> <img
                                    src="https://mail.najva.com/public/theme/version4/assets/images/loader_sblue.gif"
                                    style="display: block;margin-left: auto;margin-right: auto;position: relative;top: 40%;"> </div>
                        <form class="description" id="theform" name="theform"
                              action="https://mail.najva.com/users/subscribeembed/js_id/7rh9/id/1" onsubmit="return false;">
                            <input type="hidden" name="js_id" id="js_id" value="7rh9">
                            <input type="hidden" name="from_url" id="from_url" value="yes">
                            <input type="hidden" name="hdn_email_txt" id="hdn_email_txt" value="">
                            <input type="hidden" name="req_hid" id="req_hid" value="" style="font-size: 13px;">
                            <div class="sib-container rounded ui-sortable flex flex-col gap-2 w-full ml-block-form">
                                <input type="hidden" name="req_hid" id="req_hid" value="" style="font-size: 13px;">
                                <div class="view-messages" style=" margin:5px 0;"> </div> <!-- an email as primary -->
                                <div class="primary-group email-group forms-builder-group ui-sortable" style="">
                                    <div class="row mandatory-email">
                                        <input placeholder="ایمیل" class="p-3 rounded-lg border w-full h-[50px] placeholder:text-center placeholder:text-[#4B5259]" type="text" name="email" id="email" value="" >
                                        <div style="clear:both;"></div>
                                        <div class="hidden-btns">
                                            <a class="btn move" href="#"><i class="fa fa-arrows"></i></a>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                                <button class="flex justify-center items-center gap-2 bg-[#2F2483] rounded-lg text-white py-3 w-full text-[18px] h-[50px]" type="submit" data-editfield="subscribe">
                                    دریافت کاتالوگ
                                    <svg width="32" height="32" viewBox="0 0 32 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 11.625L18 15.5L24 11.625M4 17.4375H6.66667M1.33334 13.5625H6.66667" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M6.66667 9.68742V9.04159C6.66667 8.35644 6.94762 7.69936 7.44772 7.21489C7.94782 6.73042 8.62609 6.45825 9.33334 6.45825H26.6667C27.3739 6.45825 28.0522 6.73042 28.5523 7.21489C29.0524 7.69936 29.3333 8.35644 29.3333 9.04159V21.9583C29.3333 22.6434 29.0524 23.3005 28.5523 23.7849C28.0522 24.2694 27.3739 24.5416 26.6667 24.5416H9.33334C8.62609 24.5416 7.94782 24.2694 7.44772 23.7849C6.94762 23.3005 6.66667 22.6434 6.66667 21.9583V21.3124" stroke="white" stroke-width="1.5" stroke-linecap="round"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <script type="text/javascript"> var sib_prefix = 'sib'; var sib_dateformat = 'dd-mm-yyyy'; </script>
<!--                <script type='text/javascript'-->
<!--                        src='https://mail.najva.com/public/theme/version4/assets/js/src/subscribe-validate.js?v=1701763976'></script>-->

            </div>
        </div>
    </div>
</nav>
<?php
if (wp_is_mobile()){
	get_template_part( 'template/MegaMenu/MobileMegaMenu/Mobile_product_menu' );
	get_template_part( 'template/MegaMenu/MobileMegaMenu/Mobile_categories_menu' );
	get_template_part( 'template/MegaMenu/MobileMegaMenu/Mobile_received_menu' );
	get_template_part( 'template/MegaMenu/MobileMegaMenu/Mobile_about_menu' );
	get_template_part( 'template/MegaMenu/MobileMegaMenu/Mobile_contact_us' );
    get_template_part( 'template/MegaMenu/card-information' );
    get_template_part( 'template/MegaMenu/search' );
}
?>


<?php
if(!wp_is_mobile()){
    get_template_part( 'template/MegaMenu/card-information' );
    get_template_part( 'template/MegaMenu/product_megamenu' );
    get_template_part( 'template/MegaMenu/cats_mega_menu' );
    get_template_part( 'template/MegaMenu/received_mega_menu' );
    get_template_part( 'template/MegaMenu/about_mega_menu' );
    get_template_part( 'template/MegaMenu/search' );
    get_template_part( 'template/MegaMenu/contact_us' );
}
( ! is_user_logged_in() ) ? get_template_part( 'template/LoginRegister' ) : '';

?>

<div id="glassEffectMask" class="w-full h-full bg-black/50 fixed hidden z-40"></div>