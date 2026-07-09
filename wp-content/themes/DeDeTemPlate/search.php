<?php get_header(); ?>
<?php if ( have_posts() ) : ?>
    <header class="page-header">
        <h1 class="w-full text-[24px] font-[700] text-center py-10">کلمه جوستوجو شده
              شما: <?php echo get_search_query(); ?></h1>
    </header>
    <div class="grid grid-cols-4 gap-5">
	<?php while ( have_posts() ) :
		the_post(); ?>
        <div class="drop-shadow-md w-full">
            <img class="w-full object-fill rounded-t-lg" src="<?php echo get_the_post_thumbnail_url() ?>">
            <p class="truncate w-full text-center p-3 text-[20px] font-[700] text-[#525252]"><?php echo get_the_title(); ?></p>
            <a href="<?php echo get_the_permalink()?>" class="">
                <button class="w-full p-2 rounded-b-lg bg-[#2F2483] text-white text-[18px] font-[500]">
                    اطلاعات بیشتر
                </button>
            </a>
        </div>
	<?php endwhile; ?>
    </div>
	<?php the_posts_navigation(); ?>
<?php endif; get_footer(); ?>