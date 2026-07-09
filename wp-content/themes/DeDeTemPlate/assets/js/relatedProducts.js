// $(function ($) {
// 	const related_product_wrapper = $("#related_product_wrapper");
//
// 	let observer = new MutationObserver(function (mutationsList) {
// 		mutationsList.forEach(function (mutation) {
// 			if (mutation.type === "attributes" && mutation.attributeName === "class") {
// 				let target = $(mutation.target);
//
// 				// اگر کلاس z-20 اضافه شده باشد
// 				if (target.hasClass("z-20")) {
// 					let newHeight = target.outerHeight(true); // دریافت ارتفاع کامل (با margin)
// 					related_product_wrapper.height(newHeight);
// 					console.log("ارتفاع جدید:", newHeight);
// 					console.log(target)
// 				}
// 			}
// 		});
// 	});
//
// 	let config = { attributes: true, attributeFilter: ["class"], subtree: true };
// 	observer.observe(related_product_wrapper[0], config);
// });
