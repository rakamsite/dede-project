jQuery(document).ready(function ($) {
	let search_cat = $("select#select_category"),
		search_parent = $("select#select_parent_id"),
		post_per_page = $("select#post_per_page_selector"),
		page_selector_pointer = $("select#page_selector_pointer")
	;
	let Loading = $("div#loading");
	let product_list = $("tbody#product_list");
	let nextPage = $('#nextPage-pc');
	let prevPage = $('#prevPage-pc');
	let pagination = $(".pagination-search-list-and-price");
	let search_input = $("input#search");
	let pageSearched = 1;
	let search_query;
	
	function getFormResult(formDate) {
		page_selector_pointer.val(pageSearched);
		search_input.blur();
		if (search_cat.val() !== 0) {
			formDate.search_cat = search_cat.val();
		}
		if (search_parent.val() !== 0) {
			formDate.search_parent = search_parent.val();
		}
		formDate.post_per_page = post_per_page.val();
		if ('virtualKeyboard' in navigator) {
			navigator.virtualKeyboard.hide();
		}
		$.ajax({
			url: ajax_admin.ajax_url,
			type: "post",
			data: formDate,
			success: function (res) {
				Loading.addClass('hidden')
				if (res.success === true) {
					product_list.html(res.data.html)
					let pages = res.data.pages,
						total_posts = res.data.total_posts;
					if (pages > 1) {
						pagination.removeClass('hidden');
						if (pageSearched === 1){
							pointerElementGenerator(pages, pageSearched ,total_posts );
						}
						$("html, body").animate({scrollTop: 0}, "slow");
						
					} else {
						pagination.addClass('hidden');
					}
				} else {
					alert(res.data)
				}
			},
			error: function (error) {
				console.log(error);
			}
		});
	}
	
	function pointerElementGenerator(pages, pageSearched, total_posts) {
		let post_per_page_value = parseInt(post_per_page.val(), 10); // اطمینان از عدد بودن مقدار
		page_selector_pointer.html('');
		
		// گرد کردن به بالا برای نمایش تمام صفحات
		let totalPages = Math.ceil(total_posts / post_per_page_value);
		
		for (let i = 1; i <= totalPages; i++) {
			let optionText = `${i} از ${totalPages}`;
			let optionElement = $(`<option value='${i}'>${optionText}</option>`);
			
			if (i === pageSearched) {
				optionElement.prop('selected', true);
			}
			
			page_selector_pointer.append(optionElement);
		}
	}
	search_input.on("input", function () {
		search_query = this.value;
		if (search_query.length !== 0) {
			let formData = {
				action: 'product_list_and_price',
				searchProduct: true,
				search_query
			}
			let search = setTimeout(function () {
				Loading.removeClass("hidden")
				getFormResult(formData)
			}, 1000);
			$(this).on("input", function () {
				clearTimeout(search);
			});
		} else {
			alert("لطفا کلید واژه برای جوستوجو وارد کنید.")
		}
	});
	
	$("form#productListAndPrice").on("submit", function (e) {
		search_query = $("input#search").val();
		e.preventDefault();
		if (search_query.length !== 0) {
			let formData = {
				action: 'product_list_and_price',
				searchProduct: true,
				search_query
			}
			getFormResult(formData);
			Loading.removeClass("hidden")
		} else {
			alert("لطفا کلید واژه برای جوستوجو وارد کنید.")
		}
	});
	
	function onLoading() {
		let formData = {
			action: 'product_list_and_price',
			searchProduct: true,
		}
		getFormResult(formData);
		Loading.removeClass("hidden")
	}
	
	onLoading();
	prevPage.on('click tap', function () {
		search_query = $("input#search").val();
		if (pageSearched === 1) {
			return;
		}
		pageSearched--;
		let formData = {
			action: 'product_list_and_price',
			searchProduct: true,
			pageSearched: pageSearched
		}
		if (search_query.length !== 0) {
			formData.search_query = search_query;
		}
		getFormResult(formData, pageSearched);
		Loading.removeClass("hidden")
	})
	nextPage.on('click tap', function () {
		search_query = $("input#search").val();
		pageSearched++;
		let formData = {
			action: 'product_list_and_price',
			searchProduct: true,
			pageSearched: pageSearched
		}
		if (search_query.length !== 0) {
			formData.search_query = search_query;
		}
		getFormResult(formData, pageSearched);
		Loading.removeClass("hidden")
	});
	page_selector_pointer.on('change', function () {
		search_query = $("input#search").val();
		pageSearched = this.value;
		let formData = {
			action: 'product_list_and_price',
			searchProduct: true,
			pageSearched: this.value
		}
		if (search_query.length !== 0) {
			formData.search_query = search_query;
		}
		getFormResult(formData, pageSearched);
		Loading.removeClass("hidden")
	});
	search_cat.on("change" , function (){
		let cat_id = this.value,
		parents_option;
		if (cat_id !== 0){
			$.ajax({
				url: ajax_admin.ajax_url,
				type: "post",
				data: {
					action:'get_parent_post_id_by_cat_id',
					cat_id
				},
				success: function (res){
					if (res.success){
						parents_option = res.data.products;
						search_parent.html(parents_option);
					}
					console.log(res)
				}
			});
			if (search_parent.val() === "0"){
				update_searching();
			}else {
				search_parent.val(0)
				update_searching();
			}
		}
	})
	search_parent.on("change" , function (){
		update_searching();
	})
	post_per_page.on("change" , function (){
		update_searching();
	});
	function update_searching(){
		search_query = $("input#search").val();
		let formData = {
			action: 'product_list_and_price',
			searchProduct: true,
			pageSearched: this.value
		}
		if (search_query.length !== 0) {
			formData.search_query = search_query;
		}
		pageSearched = 1;
		getFormResult(formData, pageSearched);
		Loading.removeClass("hidden")
		
	}
});