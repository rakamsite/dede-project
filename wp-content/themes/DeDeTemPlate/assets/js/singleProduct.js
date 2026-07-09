jQuery(document).ready(function ($) {
  let body = $("body");
  let comment = $(".comment");
  let quantity_unit_manager = false;
  let startX = 0;
  let endX = 0;
  const carousel = $('#related_product_container');
  const items = $('[data-carousel-item]');
  const prevButton = $('[data-carousel-prev]');
  const nextButton = $('[data-carousel-next]');

  $(".excerpt-style > ul").addClass("list-disc marker:text-[#E3000F]");
  let coupon_input = $("#coupon_code") , coupon_button = $("[name=apply_coupon]");
  if (coupon_input.length !== 0 && coupon_button.length !== 0) {
    coupon_input.on("input", function (event) {
      let value = $(this).val();
      if (value.length > 0) {
        coupon_button.addClass('bg-[#2F2483]');
        coupon_button.addClass('hover:bg-[#E3000F] ');
        coupon_button.removeClass('bg-[#979797]');
      }else{
        coupon_button.removeClass('bg-[#2F2483]');
        coupon_button.removeClass('hover:bg-[#E3000F] ');
        coupon_button.addClass('bg-[#979797]');
      }
    })
  }
  function quantityManager(type) {
    const QuickView = findQuickView();
    let quantity,
      main_unit,
      main_unit_radio,
      unit_selected,
      unit_quantity,
      price_final,
      main_unit_display,
      price_final_symbol,
      quantity_final,
      sub_controller,
      sub_unit;
    if (QuickView) {
      main_unit = QuickView.find("#order_main_unit_name");
      main_unit_radio = QuickView.find("#order_main_unit");
      quantity = QuickView.find("#quantity");
      quantity_final = QuickView.find("#quantity_final");
      main_unit_display = QuickView.find("#main_unit_display");
      price_final_symbol = QuickView.find(
        ".price_final > span > bdi > .woocommerce-Price-currencySymbol",
      ).text();
      price_final = QuickView.find(".price_final > span > bdi").text();
      unit_selected = QuickView.find("#unit_selected");
      unit_quantity = QuickView.find("#unit_quantity");
      sub_controller = QuickView.find("#sub_controller");
      sub_unit = QuickView.find("#sub_unit");
    } else {
      quantity = $("#quantity");
      main_unit = $("#order_main_unit_name");
      main_unit_radio = $("#order_main_unit");
      quantity_final = $("#quantity_final");
      main_unit_display = $("#main_unit_display");
      price_final_symbol = $(
        ".price_final > span > bdi > .woocommerce-Price-currencySymbol",
      ).text();
      price_final = $(".price_final > span > bdi").text();
      unit_selected = $("#unit_selected");
      unit_quantity = $("#unit_quantity");
      sub_controller = $("#sub_controller");
      sub_unit = $("#sub_unit");
    }
    let quantity_val = parseInt(quantity.val());
    let pakage_quantity = parseInt(quantity.attr("data-pakage-quantity"));
    let New_quantity_val;
    let main_unit_quantity_val;
    let val_sub_controller = parseInt(sub_controller.val());
    if (quantity_val === 0 || typeof quantity_val !== "number") {
      return false;
    }
    
    price_final = price_final.replace(".", "");
    price_final = price_final.replace(/[^0-9]/g, "");
    if (type === "P") {
      main_unit_quantity_val = quantity_val + 1;
      quantity.val(main_unit_quantity_val);
    } else if (type === "N") {
      main_unit_quantity_val = quantity_val - 1;
      if (main_unit_radio.is(":checked")) {
        if (quantity_val === pakage_quantity) {
          return false;
        }
      } else {
        if (quantity_val === 1) {
          return false;
        }
      }
      quantity.val(main_unit_quantity_val);
    }
    if (main_unit_radio.is(":checked")) {
      if (type === "P") {
        quantity.val(quantity_val + pakage_quantity);
      } else if (type === "N") {
        quantity.val(quantity_val - pakage_quantity);
      }
      New_quantity_val = parseInt(quantity.val());
    } else {
      New_quantity_val = parseInt(quantity.val()) * pakage_quantity;
    }

    quantity_final.text(New_quantity_val);
    main_unit_display.text(main_unit.text());
    unit_quantity.val(quantity.val());
    calculateTotalPrice(price_final, price_final_symbol, New_quantity_val);
  }

  body.on("change" , "#order_main_unit" , function (){
    quantityManager('x');
  })
  function findQuickView() {
    let QuickView = body.find("#quick_view_post_viwed");
    if (QuickView.length > 0) {
      return QuickView;
    } else {
      return null;
    }
  }

  function calculateTotalPrice(
    price_final,
    price_final_symbol,
    quantity_final_val,
  ) {
    price_final = price_final.replace(".", "");
    price_final = price_final.replace(/[^0-9]/g, "");
    let number = price_final * quantity_final_val;
    let formattedNumber = number.toLocaleString("fa-IR") +"&nbsp;"+ price_final_symbol;
    $("div#total_price").html(formattedNumber);
  }

  body.on(
    "click tap",
    "button#quantityUp, button#quantityUp_quick",
    function () {
      quantityManager("P");
    },
  );

  body.on(
    "click tap",
    "button#quantityDown, button#quantityDown_quick",
    function () {
      quantityManager("N");
    },
  );

  body.on("blur", "#quantity", function () {
    let val = this.value;
    let pakage_quantity = parseInt($(this).attr("data-pakage-quantity"));
    if ($("#order_main_unit").is(":checked")) {
      if (val % pakage_quantity !== 0) {
        alert(
          `مقدار وارد شده صحیح نیست . باید مقدار بر ${pakage_quantity} بخش پذیر باشد.`,
        );
        $(this).val(pakage_quantity);
      }
    }

    if (val.trim() === "") {
      $(this).val(pakage_quantity);
    }
    quantityManager("x");
  });

  body.on("click tap", "#order_main_unit", function () {
    let val = $(this).val();
    const QuickView = findQuickView();
    let quantity,
      main_unit,
      main_unit_radio,
      unit_selected,
      unit_quantity,
      price_final,
      main_unit_display,
      price_final_symbol,
      quantity_final;
    if (QuickView) {
      main_unit = QuickView.find("#order_main_unit_name");
      main_unit_radio = QuickView.find("#order_main_unit");
      quantity = QuickView.find("#quantity");
      quantity_final = QuickView.find("#quantity_final");
      main_unit_display = QuickView.find("#main_unit_display");
      price_final_symbol = QuickView.find(
        ".price_final > span > bdi > .woocommerce-Price-currencySymbol",
      ).text();
      price_final = QuickView.find(".price_final > span > bdi").text();
      unit_selected = QuickView.find("#unit_selected");
      unit_quantity = QuickView.find("#unit_quantity");
    } else {
      quantity = $("#quantity");
      main_unit = $("#order_main_unit_name");
      main_unit_radio = $("#order_main_unit");
      quantity_final = $("#quantity_final");
      main_unit_display = $("#main_unit_display");
      price_final_symbol = $(
        ".price_final > span > bdi > .woocommerce-Price-currencySymbol",
      ).text();
      price_final = $(".price_final > span > bdi").text();
      unit_selected = $("#unit_selected");
      unit_quantity = $("#unit_quantity");
    }
    if (QuickView) {
      quantity = QuickView.find("#quantity");
    } else {
      quantity = $("#quantity");
      price_final_symbol = $(
        ".price_final > span > bdi > .woocommerce-Price-currencySymbol",
      ).text();
    }
    quantity.attr("data-pakage-quantity", val);
    quantity.val(val);

    let quantity_final_val = parseInt(quantity.val());
    quantity_final.text(quantity_final_val);
    main_unit_display.text(main_unit.text());
    unit_selected.val(main_unit.text());
    unit_quantity.val(parseInt(quantity.val()));

    calculateTotalPrice(quantity_final, price_final_symbol, quantity_final_val);
  });

  body.on("change", "#sub_controller, #sub_unit", function () {
    sub_unit_controller();
  });

  function sub_unit_controller() {
    let quantity = $("#quantity");
    let QuickView = findQuickView();
    let Selector = $("#sub_controller");
    let main_unit = $("#order_main_unit_name").text();
    let sub_unit_name = Selector.find("option:selected").text();
    let quantity_final = $("#quantity_final");
    let main_unit_display = $("#main_unit_display");
    let price_final_symbol, price_final;
    let unit_selected = $("#unit_selected");
    let unit_quantity = $("#unit_quantity");
    if (QuickView) {
      unit_selected = QuickView.find("#unit_selected");
      unit_quantity = QuickView.find("#unit_quantity");
      quantity = QuickView.find("#quantity");
      main_unit = QuickView.find("#order_main_unit_name").text();
      sub_unit_name = Selector.find("option:selected").text();
      price_final_symbol = QuickView.find(
        ".price_final > span > bdi > .woocommerce-Price-currencySymbol",
      ).text();
      price_final = QuickView.find(".price_final > span > bdi").text();
    } else {
      price_final_symbol = $(
        ".price_final > span > bdi > .woocommerce-Price-currencySymbol",
      ).text();
      price_final = $(".price_final > span > bdi").text();
    }

    if (Selector.val() === "0" && sub_unit_name === "") {
      return;
    }
    quantity.val("1");
    quantity.attr("data-pakage-quantity", Selector.val());
    let quantity_final_val =
      parseInt(quantity.val()) * parseInt(Selector.val());
    quantity_final.text(quantity_final_val);
    main_unit_display.text(main_unit);
    unit_selected.val(sub_unit_name);
    unit_quantity.val(parseInt(quantity.val()));

    calculateTotalPrice(price_final, price_final_symbol, quantity_final_val);
  }

  $("video.comment-videos").each(function () {
    const imgUrl = getVideoFirstFrame($(this)[0]);
    $(this).siblings("img").attr("src", imgUrl);
  });

  $(".comment_date").each(function () {
    let orgData = new Date($(this).text());
    let ShamsI = new persianDate(orgData);
    let ShamsIString = ShamsI.format("YYYY/MM/DD");
    $(this).text(ShamsIString);
  });

  $("button[date-sorter-type]").on("click", function () {
    let sortType = $(this).attr("date-sorter-type");
    let commentContainer = $(".comments-container");
    let dateSorter = [];
    let valuableSorter = [];
    let unhidden = 0;
    commentContainer.find(".comment").each(function () {
      let comment = $(this);
      let date = comment.attr("data-date");
      let rate = comment.attr("data-vote-up");
      dateSorter.push({ date: new Date(date), comment: comment });
      valuableSorter.push({ rate: rate, comment: comment });

      if (!comment.hasClass("hidden")) {
        unhidden++;
        $(this).addClass("hidden");
      }
    });
    if (sortType === "valuable") {
      valuableSorter.sort((a, b) => b.rate - a.rate);
    } else if (sortType === "newest") {
      dateSorter.sort((a, b) => b.date - a.date);
    } else if (sortType === "oldest") {
      dateSorter.sort((a, b) => a.date - b.date);
    }
    commentContainer.empty();

    if (sortType === "newest" || sortType === "oldest") {
      for (let i = 0; i < dateSorter.length; i++) {
        if (i < unhidden) {
          commentContainer.append(dateSorter[i].comment.removeClass("hidden"));
        } else {
          commentContainer.append(dateSorter[i].comment);
        }
      }
    } else {
      for (let i = 0; i < valuableSorter.length; i++) {
        if (i < unhidden) {
          commentContainer.append(
            valuableSorter[i].comment.removeClass("hidden"),
          );
        } else {
          commentContainer.append(valuableSorter[i].comment);
        }
      }
    }
  });

  $("button#more_comment").on("click", function () {
    comment.removeClass("hidden");
    $(this).addClass("hidden");
  });

  body.on("click", "button#prev", function () {
    let scrollContainer = $("#inductor");
    scrollContainer.animate({ scrollLeft: "+=170" }, 300);
  });

  body.on("click", "button#next", function () {
    const scrollContainer = $("#inductor");
    scrollContainer.animate({ scrollLeft: "-=170" }, 300);
  });

  function getVideoFirstFrame(video) {
    const canvas = document.createElement("canvas");
    const videoWidth = video.videoWidth;
    const videoHeight = video.videoHeight;
    canvas.width = videoWidth;
    canvas.height = videoHeight;
    const ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0);
    return canvas.toDataURL("image/png");
  }
  carousel.on('touchstart', (e) => {
    startX = e.touches[0].clientX;
  });
  carousel.on('touchend', (e) => {
    endX = e.changedTouches[0].clientX;
    handleSwipe();
  });
  function handleSwipe() {
    const diff = startX - endX;
    if (diff > 50) {
      nextButton.click();
    } else if (diff < -50) {
      prevButton.click();
    }
  }
  
});

