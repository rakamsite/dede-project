<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * My Account navigation.
 *
 * @since 10.10
 */
$current_user_id     = get_current_user_id();
$defulte_profile     = get_avatar_url( $current_user_id );
$user_profile        = get_user_meta( $current_user_id, "_dede_profile_picture_", true );
$get_avatar          = ( ! empty( $user_profile ) ) ? $user_profile : $defulte_profile;
$all_user_meta       = get_user_meta( $current_user_id );
$comment_information = cmb2_get_option( 'Comment_section_key', 'illegal_text' );

?>
<div class="mx-auto container flex relative py-5 md:py-10 gap-10">
    <div id="LoadingMyAccount"
         class="absolute w-full h-full bg-black/25 rounded-lg flex justify-center items-center hidden z-[60]">
        <svg class="animate-spin -ml-1 mr-3 h-32 w-32 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
    <div id="log-out-prompt" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <button type="button" class="absolute top-3 right-2.5 text-gary-500 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="log-out-prompt">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-6 text-center">
                    <svg class="mx-auto mb-4 text-yellow-700 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500">آیا میخواهید از حساب کاربری خود خارج شوید ؟</h3>
                    <button data-modal-hide="log-out-prompt"  type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                        خیر
                    </button>
                    <button id="accept_to_exit_main_account" value="<?php echo wp_logout_url( home_url( '/' ) ); ?>" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 ">بله</button>
                </div>
            </div>
        </div>
    </div>
    <div class="basis-2/12 h-fit border-[1px] border-black rounded-lg p-2 overflow-x-hidden text-[#525252] hidden md:block">
        <div class="relative">
            <img src="<?php echo $get_avatar; ?>" class="w-full object-cover  rounded-full" alt=""/>
            <label for="ProfilePicture"
                   class="absolute bottom-8 text-center text-[#0058BF] font-[700] text-[14px] w-full cursor-pointer">
                بارگذاری یا تغییر تصویر
                <input type="file" name="ProfilePicture" id="ProfilePicture" accept="image/png, image/jpeg"
                       style="display: none"/>
            </label>
        </div>
        <div class="divide-y pt-10 text-[18px] font-[500]">
            <button data-url="MyAccount" type="button" id="MyAccountButton"
                    class="w-full py-3 hover:bg-[#E9E9E9] myAccountButton"> حساب من
            </button>
            <button data-url="Information" type="button" id="InformationButton"
                    class="w-full py-3 hover:bg-[#E9E9E9] myAccountButton">اطلاعات
            </button>
            <button data-url="WalletInformation" type="button" id="WalletInformationButton"
                    class="w-full py-3 hover:bg-[#E9E9E9] myAccountButton"> اعتبار نقدی
            </button>
            <button data-url="orders" data-main="<?php echo home_url( '/my-account' ); ?>" type="button"
                    id="ordersButton" class="w-full py-3 hover:bg-[#E9E9E9] myAccountButton"> سفارشات
            </button>
            <button data-url="guaranty" type="button" id="guarantyButton"
                    class="w-full py-3 hover:bg-[#E9E9E9] myAccountButton hidden"> گارانتی
            </button>
            <a href="/after-sales" class="w-full py-3 hover:bg-[#E9E9E9] myAccountButton block text-center">
                گارانتی
            </a>
            <button data-url="StaticPassword" type="button" id="StaticPasswordButton"
                    class="w-full py-3 hover:bg-[#E9E9E9] myAccountButton"> رمز ثابت
            </button>
            <button data-modal-target="log-out-prompt" data-modal-toggle="log-out-prompt"
                    type="button" id="MyAccountButton"
                    class="w-full py-3 hover:bg-[#E9E9E9]"> خروج از حساب
            </button>
        </div>
    </div>
    <div id="myAcountContetnLoader" class="w-full basis-12/12 md:basis-10/12 overflow-x-hidden px-5 md:p-0">
        <div class="w-full flex justify-between py-5 md:hidden">
            <label for="ProfilePicture" class="relative flex items-center gap-1">
                <img src="<?php echo $get_avatar; ?>" class="w-[80px] h-[80px] object-cover rounded-full"/>
                <input type="file" name="ProfilePicture" id="ProfilePicture" accept="image/png, image/jpeg"
                       style="display: none"/>
                <p class="text-sm text-[#0058BF]">بارگذاری تصویر</p>
            </label>
            <button data-url="Information" class="text-[18px] text-[#0058BF] flex gap-2 items-center myAccountButton"
                    id="mobile_page_controller">
                <p>ویرایش اطلاعات</p>
                <svg width="5" height="11" viewBox="0 0 5 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.118086 5.84268L4.36109 10.8693C4.43189 10.9532 4.52595 11 4.62376 11C4.72157 11 4.81563 10.9532 4.88643 10.8693L4.89099 10.8636C4.92543 10.8229 4.95286 10.774 4.9716 10.7197C4.99034 10.6654 5 10.607 5 10.5479C5 10.4888 4.99034 10.4304 4.9716 10.3761C4.95286 10.3218 4.92543 10.2729 4.89099 10.2322L0.895425 5.49905L4.89099 0.767807C4.92543 0.727127 4.95286 0.678161 4.9716 0.623888C4.99034 0.569615 5 0.51117 5 0.452107C5 0.393044 4.99034 0.334598 4.9716 0.280324C4.95286 0.226051 4.92543 0.177086 4.89099 0.136406L4.88643 0.130726C4.81563 0.0468122 4.72157 1.18551e-07 4.62376 1.16095e-07C4.52595 1.1364e-07 4.43189 0.0468122 4.36109 0.130726L0.118086 5.15732C0.0807703 5.20153 0.051063 5.25469 0.0307648 5.3136C0.0104666 5.3725 6.6351e-08 5.43592 6.55868e-08 5.5C6.48226e-08 5.56408 0.0104666 5.6275 0.0307648 5.6864C0.051063 5.74531 0.0807703 5.79848 0.118086 5.84268Z"
                          fill="#0058BF"/>
                </svg>

            </button>
        </div>
		<?php
		get_template_part( 'woocommerce/myaccount/MainMyAccount' );
		if ( function_exists( 'dede_store_features_render_customer_profile' ) ) {
			dede_store_features_render_customer_profile( 'account' );
		} else {
			get_template_part( 'woocommerce/myaccount/Information' );
		}
		get_template_part( 'woocommerce/myaccount/WalletInformation' );
		get_template_part( 'woocommerce/myaccount/orders' );
		get_template_part( 'woocommerce/myaccount/guarantee' );
		get_template_part( 'woocommerce/myaccount/password' );
		?>
    </div>
    <div id="submitComment" aria-hidden="true"
         class="w-full h-screen overflow-y-scroll md:overflow-y-auto md:h-auto md:w-3/5 hidden p-5 bg-white md:drop-shadow-md md:shadow md:rounded-lg fixed md:top-1/2 top-0 md:left-1/2 md:transform md:-translate-x-1/2 md:-translate-y-1/2 z-50">
        <div class="w-full h-full">
            <div class="w-full relative border-b border-[#4B5259] flex items-start pb-2">
                <button data-modal-target='submitComment' data-modal-toggle='submitComment'>
                    <svg class="absolute left-0" width="54" height="54" viewBox="0 0 54 54" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <rect x="38.5371" y="11.6675" width="5" height="38" rx="2.5"
                              transform="rotate(45 38.5371 11.6675)"
                              fill="#D9D9D9"/>
                        <rect x="42.0732" y="38.5371" width="5" height="38" rx="2.5"
                              transform="rotate(135 42.0732 38.5371)"
                              fill="#D9D9D9"/>
                    </svg>
                </button>
                <div class="space-y-1 text-[#525252]">
                    <p class="text-[20px] font-[700]">ثبت دیدگاه برای <strong class="comment-title"> </strong></p>
                    <p class="text-[15px] font-[500]">در صورت تایید دیدگاه شما مبلغ <span
                                class="text-[#008826]"><?php echo wc_price( '20000' ) ?></span> به کیف پول شما واریز
                        میگردد.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-5">
                <div class="grid grid-cols-1 gap-5 items-start text-[#4B5259] ">
                    <div class="w-full flex justify-between items-center basis-full">
                        <p class="text-[#525252] text-[18px] font-[500]">امتیاز دهید!</p>
                        <div class="flex items-center justify-end  space-x-3 space-x-reverse">
                            <label class="cursor-pointer">
                                <svg class="rating-stars w-11 h-11 text-[#D9D9D9]" viewBox="0 0 42 40"
                                     fill="currentColor"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 0L25.9393 15.2016H41.9232L28.992 24.5967L33.9313 39.7984L21 30.4033L8.06872 39.7984L13.008 24.5967L0.0767574 15.2016H16.0607L21 0Z"/>
                                </svg>
                                <input class="FavStar hidden" type="radio" name="FavStar" value="1">
                            </label>
                            <label class="cursor-pointer">
                                <svg class="rating-stars w-11 h-11 text-[#D9D9D9]" viewBox="0 0 42 40"
                                     fill="currentColor"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 0L25.9393 15.2016H41.9232L28.992 24.5967L33.9313 39.7984L21 30.4033L8.06872 39.7984L13.008 24.5967L0.0767574 15.2016H16.0607L21 0Z"/>
                                </svg>
                                <input class="FavStar hidden" type="radio" name="FavStar" value="2">
                            </label>
                            <label class="cursor-pointer">
                                <svg class="rating-stars w-11 h-11 text-[#D9D9D9]" viewBox="0 0 42 40"
                                     fill="currentColor"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 0L25.9393 15.2016H41.9232L28.992 24.5967L33.9313 39.7984L21 30.4033L8.06872 39.7984L13.008 24.5967L0.0767574 15.2016H16.0607L21 0Z"/>
                                </svg>
                                <input class="FavStar hidden" type="radio" name="FavStar" value="3">
                            </label>
                            <label class="cursor-pointer">
                                <svg class="rating-stars w-11 h-11 text-[#D9D9D9]" viewBox="0 0 42 40"
                                     fill="currentColor"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 0L25.9393 15.2016H41.9232L28.992 24.5967L33.9313 39.7984L21 30.4033L8.06872 39.7984L13.008 24.5967L0.0767574 15.2016H16.0607L21 0Z"/>
                                </svg>
                                <input class="FavStar hidden" type="radio" name="FavStar" value="4">
                            </label>
                            <label class="cursor-pointer">
                                <svg class="rating-stars w-11 h-11 text-[#D9D9D9]" viewBox="0 0 42 40"
                                     fill="currentColor"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 0L25.9393 15.2016H41.9232L28.992 24.5967L33.9313 39.7984L21 30.4033L8.06872 39.7984L13.008 24.5967L0.0767574 15.2016H16.0607L21 0Z"/>
                                </svg>
                                <input class="FavStar hidden" type="radio" name="FavStar" value="5">
                            </label>
                        </div>
                    </div>
                    <div class="w-full basis-full relative">
                        <label for="comment-section"
                               class="absolute bg-white right-4 px-3 -top-3 font-[700] text-[14px]">متن
                            نظر</label>
                        <textarea placeholder="برای ما بنویسید ..." required id="comment-section" name="comment-section"
                                  class="p-5 h-32 w-full border border-[#4B5259] rounded-lg"></textarea>
                    </div>
                    <div>
                        <h2 class="text-[18px]">ارسال محتوا</h2>
                        <p>حداکثر ۵ عکس ۵ مگابایت و ۳ ویدیو با فرمت MP4 تا ۱۰۰ مگابایت</p>
                    </div>
                    <div class="grid grid-cols-12 gap-2">
                        <label for="fileInput"
                               class="p-5 border border-dashed border-[#4B5259] col-span-2 rounded-lg flex flex-col items-center gap-2 cursor-pointer">
                            <svg width="38" height="38" viewBox="0 0 38 38" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <rect x="38" y="16.5" width="5" height="38" rx="2.5" transform="rotate(90 38 16.5)"
                                      fill="#0058BF"/>
                                <rect x="21.5" y="38" width="5" height="38" rx="2.5"
                                      transform="rotate(-180 21.5 38)" fill="#0058BF"/>
                            </svg>
                            <p class="text-[#0058BF]">افزودن</p>
                            <input class="hidden" type="file" id="fileInput" name="fileInput"
                                   accept="image/*,video/mp4"/>
                        </label>
                        <div id="previewContainer" class="col-span-10 grid grid-cols-4 gap-2 "></div>
                    </div>
                    <div>
                        <button
                                class="p-4 submitComment text-white text-[20px] font-[700] text-center w-full bg-[#E3000F] rounded-lg">
                            ثبت دیدگاه
                        </button>
                    </div>
                </div>
                <div class=" pr-10 prose prose-p:my-0.5 ">
                    <?php if ($comment_information):
                    echo $comment_information;
                    else:
                    ?>
                    <p class="font-bold">دیگران را با نوشتن نظرات خود، برای انتخاب این محصول راهنمایی کنید.</p>
                    <div class="">
                        <p>لطفا پیش از ارسال نظر، خلاصه قوانین زیر را مطالعه کنید:</p>
                        <p>لازم است محتوای ارسالی منطبق برعرف و شئونات جامعه و با بیانی رسمی و عاری از لحن تند، تمسخرو
                            توهین باشد.</p>
                        <p> از ارسال لینک‌ سایت‌های دیگر و ارایه‌ی اطلاعات شخصی نظیر شماره تماس، ایمیل و آی‌دی شبکه‌های
                            اجتماعی پرهیز کنید.
                        </p>
                        <p>
                            در نظر داشته باشید هدف نهایی از ارائه‌ی نظر درباره‌ی کالا ارائه‌ی اطلاعات مشخص و مفید برای
                            راهنمایی سایر کاربران در فرآیند انتخاب و خرید یک محصول است.
                        </p>
                        <p>
                            با توجه به ساختار بخش نظرات، از پرسیدن سوال یا درخواست راهنمایی در این بخش خودداری کرده و
                            سوالات
                            خود را در بخش «چت سایت» مطرح کنید.
                        </p>
                        <p>
                            افزودن عکس و ویدیو به نظرات:
                        </p>
                        <p>
                            با مطالعه‌ی <a href="<?php echo get_privacy_policy_url() ?>">این لینک</a> می‌توانید
                            مفید‌ترین الگوی عکاسی از کالایی که خریداری کرده‌اید را مشاهده
                            کنید.
                        </p>
                        <p>
                            با مطالعه‌ی این لینک می‌توانید مفید‌ترین الگوی عکاسی از کالایی که خریداری کرده‌اید را مشاهده
                            کنید.
                        </p>
                        <p>
                            هرگونه نقد و نظر در خصوص DeDe.ir، مشکلات دریافت خدمات و درخواست کالا را با ایمیل
                            <span class="underline">sales@ajaxir.com</span> یا با شماره‌ی <span class="underline">91008585 - 021</span>
                            در میان بگذارید و از نوشتن آن‌ها در بخش نظرات
                            خودداری کنید.
                        </p>
                        <?php endif; ?>
                    </div>
            </div>
        </div>
    </div>
</div>

</div>
<div id="myAccountNotification" class="fixed top-0 md:right-5 w-full md:w-auto md:pt-20 z-[60]"></div>