<?php
/*
Template Name: قالب معمولی برگه
*/
wp_enqueue_script("single-product-js", dedeTemplate . '/assets/js/singleProduct.js', array('jquery'), '1.0', false,);
wp_enqueue_script("single-product-ajax", dedeTemplate . '/ajax/singleProduct/js/singleProductAjax.js', array('jquery'), '1.0', true,);

get_header();
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		?>
        <div class="w-full bg-[#F2F2F2] mb-5 under_header">
            <div class=" py-3 container mx-auto px-5 md:grid flex flex-col justify-center space-y-5 md:space-y-0 grid-cols-1 md:grid-cols-2 gap-0 place-content-between place-items-center">
                <div class="justify-self-start">
                    <h4><?php echo get_the_title() ?></h4>
                </div>
                <div class="justify-self-end ">
                    <div class="flex gap-2 text-sm">
                        <a href="<?php echo home_url( '/' ) ?>" class="text-[#0058BF]">صفحه اصلی</a>
                        /
                        <a href="<?php echo get_the_permalink() ?>"><?php echo get_the_title() ?> </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mx-auto">
            <div class="text-justify px-5 md:px-0 prose prose-a:text-[#0058BF] prose-a:no-underline prose-ul:list-inside prose-ol:list-inside !max-w-full break-words">
		        <?php the_content(); ?>
            </div>
        </div>
		<?php
	}
}
get_footer();