jQuery(document).ready(function ($) {
    const send_message_now = $(".send_message_now");
    send_message_now.on("click", function (event) {
        let user_id = $(this).val() ,abandoned_cart_total = $(this).data('total').toLocaleString('fa-IR') , admin_url = admin.url , loading_abandoned_cart = $(".loading_abandoned_cart");
        loading_abandoned_cart.css('display', 'flex');
        $.ajax({
            url:admin_url,
            method:"POST",
            dataType:"json",
            data:{
                action:"send_abandoned_cart_message",
                user_id,
                abandoned_cart_total,
            },
            success: function(data){
                if (data.success) {
                    alert('پیامک با موفقیت ارسال شد');
                }else {
                    alert('مشکلی در ارسال پیامک');
                }
                loading_abandoned_cart.css('display', 'none');
            },
        })
    });
    
})