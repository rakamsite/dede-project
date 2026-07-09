<?php get_header(); ?>
<div class="container mx-auto py-20 ">
	<p class="text-[#525252] text-2xl ">
		صفحه ای که به دنبال آن بودید وجود ندارد!
	</p>
	<div class="mt-20">
		<p class="text-[#525252]">برای دسترسی بهتر میتوانید به یکی از صفحات زیر بروید</p>
		<div class="mt-5">
			<?php
			wp_nav_menu(array(
				'menu' => '404-menu',
				'container'      => 'ul',
				'menu_class'     => 'list-disc mr-5 text-[#0058BF] text-underline underline underline-offset-2 ',
				'menu_id'        => 'menu-id',
				'fallback_cb'    => false,
			));
			?>
		</div>
		<p class="text-[#4B5259] text-lg mt-10 mb-8 font-bold">یا جست و جو کنید</p>
		<div class="container relative">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="search" name="s" class="rounded-full w-full border-[1px] border-black p-5 text-lg font-bold placeholder:text-gray-700 " placeholder="جوست و جو ..." />
                <button class="absolute left-4 top-3 ">
                    <svg width="47" height="48" viewBox="0 0 47 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M42.5938 43.7494L31.5018 32.4214C34.1673 29.1533 35.4964 24.9616 35.2128 20.7182C34.9292 16.4748 33.0546 12.5064 29.979 9.63863C26.9035 6.77081 22.8637 5.22436 18.7001 5.32098C14.5365 5.4176 10.5697 7.14984 7.62481 10.1574C4.67995 13.1649 2.98379 17.2161 2.88919 21.4683C2.79459 25.7205 4.30882 29.8462 7.11688 32.9872C9.92495 36.1282 13.8106 38.0427 17.9656 38.3323C22.1206 38.622 26.225 37.2645 29.425 34.5424L40.517 45.8704L42.5938 43.7494ZM5.87503 21.8704C5.87503 19.2003 6.6503 16.5902 8.10279 14.3702C9.55529 12.1501 11.6198 10.4198 14.0352 9.39798C16.4506 8.3762 19.1084 8.10885 21.6726 8.62975C24.2368 9.15065 26.5922 10.4364 28.4408 12.3244C30.2895 14.2124 31.5485 16.6179 32.0585 19.2366C32.5686 21.8554 32.3068 24.5698 31.3063 27.0366C30.3058 29.5034 28.6115 31.6118 26.4377 33.0952C24.2639 34.5786 21.7082 35.3704 19.0938 35.3704C15.5891 35.3664 12.2292 33.9428 9.75101 31.4119C7.27285 28.881 5.87892 25.4496 5.87503 21.8704Z" fill="#BCBCBC"/>
                    </svg>
                </button>
            </form>
		</div>
	</div>
</div>
<?php get_footer();
