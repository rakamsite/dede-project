jQuery(document).ready(function ($) {
    let body = $('body');
    let comment = $('.comment');
    let quantity = $("#quantity");
    let price_final;
    let main_unit_display;
    let price_final_symbol;
    let quantity_final;
    let unit_selected = $("#unit_selected");
    let unit_quantity = $("#unit_quantity");
    $('.excerpt-style > ul').addClass('list-disc marker:text-[#E3000F]')

    function quantityManager(type) {
        const QuickView = findQuickView();
        let quantity, main_unit, main_unit_radio, quantity_final, main_unit_display, price_final_symbol, price_final, unit_selected, unit_quantity;

        if (QuickView) {
            main_unit = QuickView.find("#order_main_unit_name");
            main_unit_radio = QuickView.find("#order_main_unit");
            quantity = QuickView.find("#quantity");
            quantity_final = QuickView.find('#quantity_final');
            main_unit_display = QuickView.find('#main_unit_display');
            price_final_symbol = QuickView.find(".price_final > span > bdi > .woocommerce-Price-currencySymbol").text();
            price_final = QuickView.find(".price_final > span > bdi").text();
            unit_selected = QuickView.find("#unit_selected");
            unit_quantity = QuickView.find("#unit_quantity");
        } else {
            quantity = $("#quantity");
            main_unit = $("#order_main_unit_name");
            main_unit_radio = $("#order_main_unit");
            quantity_final = $('#quantity_final');
            main_unit_display = $('#main_unit_display');
            price_final_symbol = $(".price_final > span > bdi > .woocommerce-Price-currencySymbol").text();
            price_final = $(".price_final > span > bdi").text();
        }

        let quantity_val = parseInt(quantity.val());
        let pakage_quantity = parseInt(quantity.attr("data-pakage-quantity"));
        let final_quantity = parseInt(quantity_final.text());
        if (quantity_final.text() === '') {
            final_quantity = quantity_val;
        }
        price_final = price_final.replace(/\.00/, '');
        price_final = price_final.replace(/[^0-9]/g, '');

        if (type === "P") {
            quantity.val(quantity_val + 1)
        } else if (type === "N") {
            quantity.val(quantity_val - 1)
        }

        let New_quantity_val;
        if (main_unit_radio.is(":checked")) {
            if (type === "P") {
                quantity.val(quantity_val + pakage_quantity);
            } else if (type === "N") {
                quantity.val(quantity_val - pakage_quantity)
            }
            New_quantity_val = parseInt(quantity.val());
        } else {
            New_quantity_val = parseInt(quantity.val()) * pakage_quantity;
        }

        quantity_final.text(New_quantity_val);
        main_unit_display.text(main_unit.text());

        let number = price_final * New_quantity_val;
        let formattedNumber = price_final_symbol + number.toLocaleString('en-US', {minimumFractionDigits: 2});
        $("div#total_price").html(formattedNumber);
        unit_quantity.val(New_quantity_val);
    }

    function findQuickView() {
        let QuickView = body.find('#quick_view_post_viwed');
        if (QuickView.length > 0) {
            return QuickView;
        } else {
            return null;
        }
    }


    body.on("click tap", 'button#quantityUp', function () {
        quantityManager("P");
    });

    body.on('click tap', 'button#quantityDown', function () {
        quantityManager("N");
    });

    body.on("click tap", 'button#quantityUp_quick', function () {
        quantityManager("P");
    });

    body.on('click tap', 'button#quantityDown_quick', function () {
        quantityManager("N");
    });

    quantity.on("input", function () {
        if ($(this).val() === "") {
            return;
        }
        quantityManager("X");
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

    $("video.comment-videos").each(function () {
        const imgUrl = getVideoFirstFrame($(this)[0]);
        $(this).siblings("img").attr("src", imgUrl);
    });

    $('.comment_date').each(function () {
        let orgData = new Date($(this).text());
        let ShamsI = new persianDate(orgData);
        let ShamsIString = ShamsI.format('YYYY/MM/DD');
        $(this).text(ShamsIString);
    });

    $("button[date-sorter-type]").on('click', function () {
        let sortType = $(this).attr('date-sorter-type');
        let commentContainer = $(".comments-container");
        let dateSorter = [];
        let valuableSorter = [];
        let unhidden = 0;
        commentContainer.find(".comment").each(function () {
            let comment = $(this);
            let date = comment.attr('data-date');
            let rate = comment.attr('data-vote-up');
            dateSorter.push({date: new Date(date), comment: comment});
            valuableSorter.push({rate: rate, comment: comment});

            if (!comment.hasClass("hidden")) {
                unhidden++;
                $(this).addClass('hidden')
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
                    commentContainer.append(dateSorter[i].comment.removeClass('hidden'));
                } else {
                    commentContainer.append(dateSorter[i].comment);
                }
            }
        } else {
            for (let i = 0; i < valuableSorter.length; i++) {
                if (i < unhidden) {
                    commentContainer.append(valuableSorter[i].comment.removeClass('hidden'));
                } else {
                    commentContainer.append(valuableSorter[i].comment);
                }
            }

        }
    });

    $("button#more_comment").on("click", function () {
        comment.removeClass('hidden');
        $(this).addClass('hidden');
    });

    body.on('click', "button#prev", function () {
        let scrollContainer = $('#inductor');
        scrollContainer.animate({scrollLeft: '+=170'}, 300);

    });

    body.on('click', "button#next", function () {
        const scrollContainer = $('#inductor');
        scrollContainer.animate({scrollLeft: '-=170'}, 300);
    });

    function sub_unit_controller() {
        let QuickView = findQuickView();
        let Selector = $("#sub_controller");
        let main_unit = $("#order_main_unit_name").text();
        let sub_unit_name = Selector.find("option:selected").text();
        let quantity_final = $('#quantity_final');
        let main_unit_display = $('#main_unit_display');
        let price_final_symbol, price_final;

        if (QuickView) {
            unit_selected = QuickView.find("#unit_selected");
            unit_quantity = QuickView.find("#unit_quantity");
            quantity = QuickView.find("#quantity");
            main_unit = QuickView.find("#order_main_unit_name").text();
            sub_unit_name = Selector.find("option:selected").text();
            price_final_symbol = QuickView.find(".price_final > span > bdi > .woocommerce-Price-currencySymbol").text();
            price_final = QuickView.find(".price_final > span > bdi").text();
        } else {
            price_final_symbol = $(".price_final > span > bdi > .woocommerce-Price-currencySymbol").text();
            price_final = $(".price_final > span > bdi").text();
        }

        if (Selector.val() === "0" || sub_unit_name === "") {
            return;
        }

        price_final = price_final.replace(/\.00/, '');
        price_final = price_final.replace(/[^0-9]/g, '');

        quantity.attr("data-pakage-quantity", Selector.val());
        let quantity_final_val = parseInt(quantity.val()) * parseInt(Selector.val());
        quantity_final.text(quantity_final_val);
        main_unit_display.text(main_unit);
        unit_selected.val(sub_unit_name);
        unit_quantity.val(quantity_final_val);

        let pakage_quantity = $("#quantity_final").text()
        let New_quantity_val = parseInt(quantity.val()) * parseInt(pakage_quantity);
        let number = price_final * New_quantity_val;
        let formattedNumber = number.toLocaleString('en-US', {minimumFractionDigits: 2}) + price_final_symbol;
        $("div#total_price").html(formattedNumber);
    }

    body.on('click tap', '#order_main_unit', function () {
        let QuickView = findQuickView();
        let val = $(this).val();
        let main_unit = $("#order_main_unit_name").text();
        let quantity_final = $('#quantity_final');
        let main_unit_display = $('#main_unit_display');
        let price_final_symbol, price_final;

        if (QuickView) {
            price_final_symbol = QuickView.find(".price_final > span > bdi > .woocommerce-Price-currencySymbol").text();
            price_final = QuickView.find(".price_final > span > bdi").text();
        } else {
            price_final_symbol = $(".price_final > span > bdi > .woocommerce-Price-currencySymbol").text();
            price_final = $(".price_final > span > bdi").text();
        }

        quantity.attr("data-pakage-quantity", val);
        quantity.val(val);

        let quantity_final_val = parseInt(quantity.val());
        quantity_final.text(quantity_final_val);
        main_unit_display.text(main_unit);
        unit_selected.val(main_unit);
        unit_quantity.val(quantity_final_val);

        calculateTotalPrice(price_final, price_final_symbol, quantity_final_val);
    });

    function calculateTotalPrice(price_final, price_final_symbol, quantity_final_val) {
        price_final = price_final.replace(/\.00/, '');
        price_final = price_final.replace(/[^0-9]/g, '');

        let number = price_final * quantity_final_val;
        let formattedNumber = number.toLocaleString('en-US', {minimumFractionDigits: 2}) + price_final_symbol;
        $("div#total_price").html(formattedNumber);
    }


    body.on('change', '#sub_controller', function () {
        sub_unit_controller();
    });

    body.on('click tap', '#sub_unit', function () {
        sub_unit_controller();
    });
});