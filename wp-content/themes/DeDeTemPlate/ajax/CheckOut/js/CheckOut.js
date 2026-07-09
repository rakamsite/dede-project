jQuery(document).ready(function ($) {
    const Loading = $("div#LoadingMyAccount");
    const NotificationEl = $("div#myAccountNotification");
    const use_wallet_amount = $("input[name=use-wallet-amount]");
    $("div#Information").removeClass('hidden');
    $("button#submitInfotmation").text('ویرایش اطلااعات');
    $('button.select_type').on("click tap", function () {
        $(this).children(".inputSelect").prop('checked', true)
        $("button#select_button").removeClass('bg-[#4B5259]').addClass('bg-[#2F2483] hover:bg-[#E3000F]').removeAttr('disabled');
    });
    $('button#change_user_type_button').on('click tap', function () {
        let select_type = $('input[name=inputSelect]:checked').val()
        const date = {
            action: "user_information_manager",
            updateUserRol: true,
            select_type
        }
        CheckOut(date).then(function (){
            location.reload();
        });
    });
    $("input#BirthDay").persianDatepicker({
        observer: false,
        format: 'YYYY/MM/DD',
        responsive: true,
        initialValue: false,
        viewMode: "day,month,year"
    });
    $('body').on("click tap", "button.CloseNotification", function () {
        $(this).parent().addClass('hidden opacity-0')
    })
    $('form.UserInformation input[type="text"], form.UserInformation select').on("blur change", function () {
        if ($(this).is('select')) { // Handle select fields separately
            if ($(this).val() !== '') {
                $(this).addClass("bg-green-50 border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
            } else {
                $(this).addClass("bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass("bg-green-50 border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
            }
        } else { // Handle text input fields
            if ($(this).val() !== '') {
                $(this).addClass("bg-green-50 border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
            } else {
                $(this).addClass("bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass("bg-green-50 border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
            }
        }
    });
    $("input#nationCode").on("blur", function () {
        let code = this.value;
        if (validateNationalId(code)) {
            $(this).addClass("bg-green-50 border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
        } else {
            $(this).addClass("bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass("bg-green-50 border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
            $(this).val('')
        }
    });
    function validateNationalId(nationalId) {
        if (nationalId.length !== 10) {
            return false;
        }
        let checksum = nationalId.slice(-1);
        let digits = nationalId.slice(0, -1);
        let sum = 0;
        for (let i = 0; i < digits.length; i++) {
            sum += digits[i] * (i + 1);
        }
        let mod = sum % 11;
        if (mod === 0) {
            mod = 11;
        }
        if (mod.toString() !== checksum) {
            return false;
        }
        return true;
    }
    $('input[type="email"]').on("blur", function () {
        let email = $(this).val();
        let regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (regex.test(email)) {
            $(this).addClass("bg-green-50 border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
        } else {
            $(this).addClass("bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass("bg-green-50 border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
            $(this).val('')
        }
    });
    $("input#NationalId").on("blur", function () {
        let code = this.value;
        if (ShenaseMeli(code)) {
            $(this).addClass("bg-green-50 border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
        } else {
            $(this).addClass("bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass("bg-green-50 border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
            $(this).val('')
        }
    });
    function ShenaseMeli(code) {
        let L = code.length;

        if (L < 11 || parseInt(code, 10) == 0) return false;

        if (parseInt(code.substr(3, 6), 10) == 0) return false;
        let c = parseInt(code.substr(10, 1), 10);
        let d = parseInt(code.substr(9, 1), 10) + 2;
        let z = [29, 27, 23, 19, 17];
        let s = 0;
        for (let i = 0; i < 10; i++)
            s += (d + parseInt(code.substr(i, 1), 10)) * z[i % 5];
        s = s % 11;
        if (s == 10) s = 0;
        return (c == s);
    }
    //get cities fromState
    $("select.state-select").on("change", function () {
        let selectedOption = $(this).find(":selected");
        let StateName = selectedOption.data('state-id');
        Loading.removeClass("hidden");
        const data = {
            action: "Get_cities_From_state",
            StateName
        };
        const cites = $(this).next('.cities-select');
        CheckOut(data).then(function (res) {
            cites.empty().append(res)
        });
    });
    $("form.UserInformation").on("submit", function (e) {
        e.preventDefault();
        let data = $(this).serialize();
        CheckOut(data).then(function (){
            location.reload();
        });
    });
    function CheckOut(data) {
        return new Promise(function (resolve, reject) {
            Loading.removeClass('hidden')

            $.ajax({
                url: ajax_admin.ajax_url,
                type: 'post',
                data: data,
                success: function (res) {
                    let notif_element;
                    if (res.success === true) {
                        Loading.addClass('hidden')
                        notif_element = $(`<div class="flex items-center w-96 p-4 mb-4 text-gray-500 bg-white rounded-lg border-2 border-gray-500/75" role="alert">  <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg"> <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"> <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/> </svg> <span class="sr-only">Check icon</span> </div> <div class="mr-3 text-sm font-normal w-full text-right">${res.data['msg']}</div> <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 CloseNotification" aria-label="Close"> <span class="sr-only">Close</span> <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"> <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/> </svg> </button> </div>`);
                        NotificationEl.append(notif_element);
                        resolve(res.data[1]);
                    } else if (res.success === false) {
                        Loading.addClass('hidden')
                        notif_element = $(`<div class="flex items-center w-96 p-4 mb-4 text-gray-500 bg-white rounded-lg border-2 border-gray-500/75" role="alert"> <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-orange-500 bg-orange-100 rounded-lg dark:bg-orange-700 dark:text-orange-200"> <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"> <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/> </svg> <span class="sr-only">Warning icon</span> </div> <div class="mr-3 text-sm font-normal w-full text-right">${res.data}</div> <button type="button" class="CloseNotification ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#toast-success" aria-label="Close"> <span class="sr-only">Close</span> <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"> <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/> </svg> </button> </div>`);
                        NotificationEl.append(notif_element);
                        reject(false);
                    }
                    setTimeout(()=> {
                        if (notif_element.length !==0){
                            notif_element.addClass('hidden')
                        }
                    },2000)
                },
                error: function (err) {
                    console.log(err);
                    reject(err);
                }
            });
        });
    }
    $(document).on('click', '[drawer-backdrop]', function() {
        $(this).removeClass('bg-opacity-50 dark:bg-opacity-80');
        $(this).addClass('bg-white');
    });
    $('#CopyFromUp').change(function () {
        if (this.checked) {
            $('#StateCurrentlyLive option:selected').each(function () {
                $('#StateSendOrder').html($(this).clone());
            });
            $('#cityCurrentlyLive option:selected').each(function () {
                $('#citySendOrder').html($(this).clone());
            });
            $('#PostcodeSendOrder').val($('#PostcodeCurrentlyLive').val());
            $('#StaticPhoneNumberSendOrder').val($('#StaticPhoneNumberCurrentlyLive').val());
            $('#AddressSendOrder').val($('#AddressCurrentlyLive').val());
        } else {
            $('#StateSendOrder').val('');
            $('#citySendOrder').val('');
            $('#PostcodeSendOrder').val('');
            $('#StaticPhoneNumberSendOrder').val('');
            $('#AddressSendOrder').val('');
        }
    });
    use_wallet_amount.on('change' , function (){
        let checked;
        checked = !!$(this).prop('checked');
        $.ajax({
            url:ajax_admin.ajax_url,
            type:"POST",
            data:{
                action:"discount_wallet_calculation",
                checked
            },
            success:function(response){
                console.log(response)
                $(document.body).trigger('init_checkout');
                window.location.reload();
            }
        })
    });
});