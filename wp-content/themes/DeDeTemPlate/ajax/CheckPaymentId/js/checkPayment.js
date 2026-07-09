jQuery(function ($) {
	const $targetEl = $('#checkPaymentId');
	const options = {
		placement: 'center-center',
		backdrop: 'dynamic',
		backdropClasses:
			'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
		closable: true,
	};
	const modal = new Modal($targetEl[0], options);
	$targetEl.find("button").on("click", function () {
		modal.hide();
	});
	const submit_button = $("#submit_order"),body = $("body");
	let payment_method ="input[name='payment_method']";
	let payment_id = "input#payment_id";
	$(payment_method).each(function (index,el) {
		if (el.value === "bacs") {
			if ($(el).is(":checked")) {
				console.log("Radio button selected!");
				submit_button.attr("disabled", true);
			}
		}
	});
	body.on("change" , payment_method, function () {
		if (this.value === "bacs") {
			console.log("Radio button selected!");
			submit_button.attr("disabled", true);
		}else {
			submit_button.removeAttr("disabled");
		}
	});
	if ($(payment_id).length > 0) {
		body.on("input" , payment_id , function (){
			let thisInput = $(this);
			if (this.value.length === parseInt($(this).attr("max"))) {
				thisInput.addClass("animate-pulse").attr("disabled", true);
				$.ajax({
					url: ajax_admin.ajax_url,
					type: "POST",
					data:{
						action: 'check_payment_id',
						payment_id:$(payment_id).val(),
					},
					success:function (res){
						if (res.success) {
							thisInput.removeClass("animate-pulse");
							submit_button.removeAttr("disabled", true);
						}else{
							thisInput.removeClass("animate-pulse").attr("disabled", false);
							modal.show();
						}
					}
				})
			}
		})
	}
});