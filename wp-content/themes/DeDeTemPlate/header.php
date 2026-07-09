<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php require 'template/title.php'?>
	<?php wp_head(); ?>
</head>
<body style="font-family:Vazirmatn" class="w-full h-full flex flex-col">
<header class="w-full">
    <?php get_template_part('template/nav');
    get_template_part('template/add_to_cart');
    ?>
</header>