jQuery(document).ready(function ($) {
	const container = $("#custom_single_carousel");
	let varData = {
		'action': 'update_variation_price',
		'product_id': $("input#product_id").val(),
	}
	const body = $('body');
	let stock_quantity_manager;
	Fancybox.bind("[data-fancybox]", {
		wheel: "zoom",
		contentDblClick: "toggleZoom",
		Toolbar: {
			display: {
				left: ["infobar"],
				middle: [
					"zoomIn",
					"zoomOut",
					"toggle1to1",
					"rotateCCW",
					"rotateCW",
				],
				right: ["slideshow", "thumbs", "close"],
			},
		},
	});
	
	function get_variant_attr(varData) {
		let variation_selector = $("select.variation_selector");
		if (variation_selector.length) {
			variation_selector.each(function () {
				varData[$(this).attr('id')] = $(this).val();
			});
			get_var_data(varData, $);
			zeroSlider();
			
		}
	}
	
	if (container.length) {
		const options = {
			Dots: false, direction: "rtl", classes: {
				track: "f-carousel__track", slide: "f-carousel__slide  ", isSelected: "is-selected flex justify-center",
			}, Thumbs: {
				type: "classic",
				thumbTpl: '<button class="h-[40px] md:h-[170px] w-[40px] md:w-[170px] mr-2 z-50 " type="button" aria-label="{{GOTO}}"><img class="rounded-lg w-full h-full aspect-square" alt="سایت dede" data-lazy-src="{{%s}}" /></button>\n',
				classes: {
					container: 'f-thumbs py-3 md:px-10'
				}
			}, Navigation: {
				classes: {
					container: "absolute w-full md:flex justify-between py-3 h-[200px] hidden", button: 'z-40'
				},
				prevTpl: '<svg data-carousel-next="true" width="24" height="47" viewBox="0 0 24 47" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.999998 46L23 23.5L0.999997 1" stroke="#525252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </svg>',
				nextTpl: '<svg data-carousel-prev="true" width="24" height="47" viewBox="0 0 24 47" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M23 0.999999L0.999999 23.5L23 46" stroke="#525252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			},
		};
		const post_slider = new Carousel(container[0], options, {Thumbs});
		
		function zeroSlider() {
			post_slider.slideTo(0);
		}
	}
	
	body.on('change', '.variation_selector', function () {
		let varData = {
			'action': 'update_variation_price', 'product_id': $("input#product_id").val()}
		get_variant_attr(varData);
	});
	get_variant_attr(varData);
	$("button.voteUp").on('click tap', function () {
		let commentId = this.value;
		$.ajax({
			type: 'POST', url: ajax_admin.ajax_url, data: {
				action: 'manage_voting_status', voteUp: true, commentId
			}, success: function (response) {
				alert(response.data);
			}
		});
	});
	$("button.voteDown").on('click tap', function () {
		let commentId = this.value;
		$.ajax({
			type: 'POST', url: ajax_admin.ajax_url, data: {
				action: 'manage_voting_status', voteDown: true, commentId
			}, success: function (response) {
				alert(response.data);
			}
		});
	});
	body.on("click tap", '.stock_quantity_manager', function () {
		let product_id = $(this).val(), variation_id = $(this).data('value');
		$.ajax({
			url: ajax_admin.ajax_url,
			type: "POST",
			data: {
				action: 'add_user_to_subscription',
				product_id,
				variation_id
			},
			success: function (response) {
				if (response.success) {
					alert(response.data.msg);
					window.location.reload();
				} else {
					alert(response.data.msg)
				}
			}
		})
	})
	
	
});


function get_var_data(varData, $) {
	let quickView = $("#quick_view_post_viwed"), description, fist_thumbnail_a, fist_thumbnail_img,
		fist_thumbnail_thumbnail_slid, quantity, price, add_to_cart_button, total_price, stock_manager, suk_code,
		quantity_final, unit_quantity, main_unit_display, unit_selected, units_list, unit_blocks,
		subscription_manager_container;
	if (quickView.length > 0) {
		quantity = quickView.find("input#quantity");
		price = quickView.find("#price");
		description = quickView.find("#description");
		fist_thumbnail_a = quickView.find('a#image_fancy_');
		fist_thumbnail_img = quickView.find('img#image_slider_');
		add_to_cart_button = quickView.find(".add-to-card");
		total_price = quickView.find("#total_price");
		stock_manager = quickView.find("#stock_manager");
		suk_code = quickView.find("#sukCode");
		quantity_final = quickView.find("#quantity_final");
		unit_quantity = quickView.find("#unit_quantity");
		main_unit_display = quickView.find("#main_unit_display");
		unit_selected = quickView.find("#unit_selected");
		units_list = quickView.find("#units_list");
		unit_blocks = quickView.find("#unit_blocks");
		subscription_manager_container = quickView.find(".subscription_manager_container");
	} else {
		description = $("div#description");
		fist_thumbnail_a = $('a.first-thumbnail');
		fist_thumbnail_img = $('img.first-thumbnail');
		fist_thumbnail_thumbnail_slid = $('div.f-thumbs__slide > button > img');
		quantity = $("#quantity");
		price = $("#price");
		add_to_cart_button = $("button.add-to-card");
		total_price = $("div#total_price");
		stock_manager = $("div#stock_manager");
		suk_code = $("span#sukCode");
		quantity_final = $("#quantity_final");
		unit_quantity = $("#unit_quantity");
		main_unit_display = $("#main_unit_display");
		unit_selected = $("#unit_selected");
		units_list = $("ul#units_list");
		unit_blocks = $("#unit_blocks");
		subscription_manager_container = $(".subscription_manager_container");
	}
	
	$.ajax({
		type: 'POST', url: ajax_admin.ajax_url, data: varData, success: function (response) {
			let attributes = {};
			let first_attr, second_attr;
			let var_id = response.data.var_id;
			let variations = $(".variation_selector");
			
			response.data.filtered_post.forEach(item => {
				const key = item[Object.keys(item)[0]];
				const value = item[Object.keys(item)[1]];
				first_attr = (Object.keys(item)[0]);
				second_attr = (Object.keys(item)[1]);
				if (!attributes[key]) {
					attributes[key] = []; // ایجاد آرایه جدید برای هر کلید
				}
				attributes[key].push(value); // اضافه کردن مقدار به آرایه مربوطه
			});
			if (variations.length > 0)
				$.each(variations, function (key, value) {
					if (value.name.includes(first_attr)) {
						first_attr = $(value);
					} else if (value.name === second_attr) {
						second_attr = $(value);
					}
				});
			
			first_attr.on('change', function (e) {
				SelectorManager();
			});
			if (response.data.image) {
				fist_thumbnail_a.attr('href', response.data.image);
				fist_thumbnail_img.attr('src', response.data.image).parent().attr('data-thumb-src', response.data.image);
				if (typeof fist_thumbnail_thumbnail_slid !== "undefined") {
					fist_thumbnail_thumbnail_slid.first().attr('src', response.data.image)
				}
			}
			description.html(response.data.description)
			price.html(response.data.html);
			add_to_cart_button.attr('data-var-id', var_id);
			total_price.html(response.data.total_amount);
			stock_manager.html(response.data.stock);
			if (response.data.sukcode) {
				suk_code.html(response.data.sukcode);
			}
			quantity.attr('step', response.data.package_quantity)
				.attr('data-pakage-quantity', response.data.package_quantity)
				.attr('data-min-quantity', response.data.min_quantity)
				.attr('data-max-quantity', response.data.max_quantity)
				.val(response.data.package_quantity);
			quantity_final.text(response.data.package_quantity);
			unit_quantity.val(response.data.package_quantity);
			main_unit_display.text(response.data.main_unit);
			unit_selected.val(response.data.main_unit);
			units_list.html(response.data.units);
			unit_blocks.html(response.data.unit_blocks);
			if (response.data.stock_status === 'outofstock') {
				add_to_cart_button.addClass('hidden');
			} else {
				add_to_cart_button.removeClass('hidden');
			}
			subscription_manager_container.html(response.data.sub_button);
			if (Cookies.get('firstTime')){
				SelectorManager();
				get_var_data(varData, $);
				Cookies.remove('firstTime');
			}
			function SelectorManager() {
				if (second_attr !== undefined) {
					second_attr.children('option').each(function (index, attr) {
						let array = attributes[first_attr.val()] ?? [];
						if (array.includes($(this).val())) {
							$(this).css('display', 'block');
							if ($(this).val() === Object.values(varData)[3]) {
								console.log(attr.value);
								$(this).prop("selected", true);
							}
						} else {
							$(attr).prop("selected", false)
							$(this).css('display', 'none');
						}
					});
				}
			}
		}
	});
}

