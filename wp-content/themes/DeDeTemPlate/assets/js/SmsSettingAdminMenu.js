jQuery(function ($) {
	let inputs_condition_id = $("input[data-conditional-id], textarea[data-conditional-id]");
	inputs_condition_id.each(function (index, element) {
		let target_element = $(element).attr("data-conditional-id");
		let condition_value = $(element).attr("data-conditional-value");
		target_element = $(`#${target_element}`);
		if (target_element.is(":checked").toString() === condition_value) {
			$(element).parent().parent().show();
		}else{
			$(element).parent().parent().hide();
		}
		target_element.change(function (){
			console.log($(this).is(":checked"))
			if ($(this).is(":checked").toString() === condition_value) {
				$(element).parent().parent().show();
			}else{
				$(element).parent().parent().hide();
			}
		});
	});
});