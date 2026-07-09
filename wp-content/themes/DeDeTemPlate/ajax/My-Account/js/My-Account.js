jQuery(document).ready(function ($) {
        let myAccountLoacation = Cookies.get('myAccountLoc');
        const wallet_add_amount = $('.wallet_add_amount');
        const body = $("body");
        let maxImageFiles = 0;
        let maxVideoFile = 0;
        let maxImageSize = 5 * 1024 * 1024;
        let maxVideoSize = 100 * 1024 * 1024;
        let allowedExtensions = ["jpg", "jpeg", "png", "gif", "mp4"];
        let checkBasicInfoInputs = $("div#basic-information input");
        let checkBasicBilling = $("div#billing-address input");
        let checkShippingInfoInputs = $("div#shipping-address input");
        let BirthDayInput = $("input#BirthDay");
        let staticPhoneNumber = $(".staticPhoneNumber");
        const accept_to_exit_main_account = $("#accept_to_exit_main_account");
    
    if (window.location.href.indexOf("my-account") === -1) {
        Cookies.remove("myAccountLoc");
    }
        function updateValidity() {
            let isFormValid = 0;
            const excludedElements = ['BirthDay', 'Telegram', 'birthdayTimeStampUnixFormat', "PostcodeCurrentlyLive", "PostcodeSendOrder"];
            
            $('form.UserInformation input, form.UserInformation select').each(function () {
                const element = $(this);
                const value = element.val();
                let isValid;
                if (element.attr('id') === 'nationCode') {
                    isValid = validateNationalId(value);
                } else {
                    isValid = value !== '' || (this.tagName === 'SELECT' && value !== '');
                    element.attr('data-valid', isValid ? 'true' : 'false');
                    if (excludedElements.includes(element.attr('id'))) {
                        isValid = true;
                    }
                    
                }
                
                if (!isValid) {
                    element.addClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
                    isFormValid++;
                } else {
                    element.addClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
                }
            });
            
            return isFormValid;
        }
        
        const MyAccountMenus = [
            {
                id: 'MyAccount',
                triggerEl: $('button#MyAccountButton')[0],
                targetEl: $('div#MyAccount')[0]
            },
            {
                id: 'Information',
                triggerEl: $('button#InformationButton')[0],
                targetEl: $('div#Information')[0]
            },
            {
                id: 'WalletInformation',
                triggerEl: $('button#WalletInformationButton')[0],
                targetEl: $('div#WalletInformation')[0]
            },
            {
                id: 'orders',
                triggerEl: $('button#ordersButton')[0],
                targetEl: $('div#orders')[0]
            },
            {
                id: 'guaranty',
                triggerEl: $('button#guarantyButton')[0],
                targetEl: $('div#guaranty')[0]
            },
            {
                id: 'StaticPassword',
                triggerEl: $('button#StaticPasswordButton')[0],
                targetEl: $('div#StaticPassword')[0]
            },
        ];
        const MyAccountOptions = {
            defaultTabId: 'MyAccount',
            activeClasses: 'bg-[#E9E9E9]',
            inactiveClasses: 'bg-white',
            onShow: (tabs) => {
                $('button.select_type').on("click tap", function () {
                    $(this).children(".inputSelect").prop('checked', true)
                    $("button#change_user_type_button").removeClass('bg-[#4B5259]').addClass('bg-[#2F2483] hover:bg-[#E3000F]').removeAttr('disabled');
                });
                let tabName = tabs._activeTab.id;
                if (tabName === 'Information') {
                    $('button#change_user_type_button').on("click", function () {
                        let select_type = $('input[name=inputSelect]:checked').val()
                        const date = {
                            action: "user_information_manager",
                            updateUserRol: true,
                            select_type
                        }
                        myAccountLoadAjax(date).then(function () {
                            location.reload();
                        });
                    });
                    
                    $("form.UserInformation").on("submit", function (e) {
                        e.preventDefault();
                        if (updateValidity() !== 0) {
                            NotificationElement('error', 'لطفا تمام اطلاعات فرم(ها) را تکمیل نمایید');
                        } else {
                            let data = $(this).serialize();
                            myAccountLoadAjax(data).then(function () {
                                location.reload();
                            });
                        }
                    });
                    BirthDayInput.persianDatepicker({
                        persianNumbers: !0,
                        observer: false,
                        format: 'YYYY/MM/DD',
                        responsive: true,
                        initialValue: false,
                        viewMode: "day,month,year",
                        altField: '#birthdayTimeStampUnixFormat',
                    });
                    $('.UserInformation input[type="text"], .UserInformation select').on("blur input change", function () {
                        if ($(this).is('select')) { // Handle select fields separately
                            if ($(this).val() !== '') {
                                $(this).addClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
                                $(this).attr('data-valid', 'true');
                            } else {
                                $(this).attr('data-valid', 'false');
                                $(this).addClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
                            }
                        } else { // Handle text input fields
                            if ($(this).val() !== '') {
                                $(this).addClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
                                $(this).attr('data-valid', 'true');
                            } else {
                                $(this).attr('data-valid', 'false');
                                $(this).addClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
                            }
                        }
                    });
                    $("input#nationCode").on("blur input change", function () {
                        let code = this.value;
                        if (validateNationalId(code)) {
                            $(this).addClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
                            $(this).attr('data-valid', 'true');
                        } else {
                            $(this).addClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
                            $(this).attr('data-valid', 'false');
                        }
                    });
                    
                    $("input.postcode").on("blur input change", function () {
                        let postcode = $(this).val();
                        let regex = /^\d{10}$/;
                        if (regex.test(postcode)) {
                            $(this).addClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
                            $(this).attr('data-valid', 'true');
                        } else {
                            $(this).attr('data-valid', 'false');
                            $(this).addClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
                        }
                    });
                    $('input[type="email"]').on("blur input change", function () {
                        let email = $(this).val();
                        let regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                        
                        if (regex.test(email)) {
                            $(this).addClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
                            $(this).attr('data-valid', 'true');
                        } else {
                            $(this).attr('data-valid', 'false');
                            $(this).addClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
                        }
                    });
                    $("input#NationalId").on("blur input change", function () {
                        let code = this.value;
                        if (ShenaseMeli(code)) {
                            $(this).addClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
                            $(this).attr('data-valid', 'true');
                        } else {
                            $(this).attr('data-valid', 'false');
                            $(this).addClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
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
                    
                    $("select.state-select").on("change", function () {
                        let selectedOption = $(this).find(":selected");
                        let StateName = selectedOption.data('state-id');
                        Loading.removeClass("hidden");
                        const data = {
                            action: "Get_cities_From_state",
                            StateName
                        };
                        const cites = $(this).next('.cities-select');
                        myAccountLoadAjax(data).then(function (res) {
                            cites.empty().append(res)
                        });
                    });
                }
                if (tabName === 'WalletInformation') {
                    $("button#addFoundToWallet").on('click', function () {
                        let amountAddToWallet = $("input#amountAddToWallet").val();
                        if (amountAddToWallet > 10000) {
                            const data = {
                                action: "Charge_Wallet",
                                amountAddToWallet
                            }
                            myAccountLoadAjax(data).then(function (url) {
                                window.open(url)
                            });
                        } else {
                            NotificationElement('error', 'مبلغ باید بیشتر از 10 هزار تومان باشد.');
                        }
                    });
                }
                if (tabName === 'guaranty') {
                    $("select#order_id").on("change", function () {
                        const order_id = this.value;
                        const data = {
                            action: "get_orders_products",
                            order_id
                        }
                        myAccountLoadAjax(data).then(function (res) {
                            console.log(res)
                            $("select#order_product_list").html(res)
                        })
                    });
                    $("button#submit_guaranty").on("click tap", function () {
                        const order_id = $('select#order_id').val();
                        const product_id = $('select#order_product_list').val();
                        const product_count = $('input#product_count').val();
                        const data =
                            {
                                action: "submit_guaranty_request",
                                order_id,
                                product_id,
                                product_count
                            }
                        myAccountLoadAjax(data).then(function () {
                            location.reload();
                        })
                    });
                }
                if (tabName === 'StaticPassword') {
                    
                    $('.show_pass').click(function () {
                        let passwordInputs = $('#password, #ReInterPassword');
                        let passwordType = passwordInputs.attr('type');
                        
                        if (passwordType === 'password') {
                            passwordInputs.attr('type', 'text');
                        } else {
                            passwordInputs.attr('type', 'password');
                        }
                    });
                    $('#ResetStaticPassword').submit(function (event) {
                        event.preventDefault();
                        let password = $('#password').val();
                        let reenteredPassword = $('#ReInterPassword').val();
                        let PasswordInputs = $('#password, #ReInterPassword');
                        if (password !== reenteredPassword) {
                            PasswordInputs.addClass('border-red-500');
                            PasswordInputs.removeClass('border-green-500');
                            NotificationElement('error', 'رمز های وارد شده یکسان نیستند.');
                            $("#submit_new_pass").removeClass('bg-[#2F2483]').addClass('bg-[#4B5259]')
                        } else {
                            PasswordInputs.addClass('border-green-500');
                            PasswordInputs.removeClass('border-red-500');
                            $("#submit_new_pass").addClass('bg-[#2F2483]').removeClass('bg-[#4B5259]');
                            $("div#submit_sms_code").removeClass("hidden invisible");
                            const data = $(this).serialize();
                            myAccountLoadAjax(data).then(function () {
                                $("input.get_message").on("keyup", function () {
                                    const currentInput = $(this);
                                    const nextInput = currentInput.next(".get_message");
                                    if (nextInput.length > 0) {
                                        nextInput.removeAttr('disabled')
                                        nextInput.focus();
                                    } else {
                                        currentInput.blur();
                                    }
                                    
                                });
                                
                                $("input#pass_message_4").on('input', function () {
                                    $("button#submit_final_static_pass").removeClass('bg-[#4B5259]').addClass('bg-[#2F2483] hover:bg-[#E3000F]').removeAttr('disabled');
                                });
                                
                                $("button#submit_final_static_pass").on("click", function () {
                                    let get_message_1 = $('input#pass_message_1').val();
                                    let get_message_2 = $('input#pass_message_2').val();
                                    let get_message_3 = $('input#pass_message_3').val();
                                    let get_message_4 = $('input#pass_message_4').val();
                                    let SmSPassword = get_message_1 + get_message_2 + get_message_3 + get_message_4;
                                    const data = {
                                        action: "Login_register_ajax",
                                        PanelChangOrSelectPasswordVerifySMS: true,
                                        SmSPassword,
                                    }
                                    myAccountLoadAjax(data).then(function () {
                                        location.reload();
                                    });
                                })
                            });
                        }
                    });
                }
            }
        }
        const MyAccount = new Tabs(MyAccountMenus, MyAccountOptions);
        const NotificationEl = $("div#myAccountNotification");
        let Loading = $("div#LoadingMyAccount");
        let imageInput = $("input#ProfilePicture");
        updateValidity();
        
        function NotificationElement(type, msg) {
            let notif_element;
            if (type === 'error') {
                notif_element = $(`<div class="flex items-center w-full md:w-96 p-4 mb-4 text-gray-500 bg-white rounded-lg border-2 border-gray-500/75"  role="alert"> <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-orange-500 bg-orange-100 rounded-lg"> <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"> <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/> </svg> <span class="sr-only">Warning icon</span> </div> <div class="mr-3 text-sm font-normal w-full text-right">${msg}</div> <button type="button" class="CloseNotification ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#toast-success" aria-label="Close"> <span class="sr-only">Close</span> <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"> <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/> </svg> </button> </div>`)
            } else if (type === 'success') {
                notif_element = $(`<div class="flex items-center w-full md:w-96 md:p-4 mb-4 text-gray-500 bg-white rounded-lg border-2 border-gray-500/75"   role="alert">  <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg"> <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"> <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/> </svg> <span class="sr-only">Check icon</span> </div> <div class="mr-3 text-sm font-normal w-full text-right">${msg}</div> <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 CloseNotification" aria-label="Close"> <span class="sr-only">Close</span> <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"> <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/> </svg> </button> </div>`);
            }
            setTimeout(() => notif_element.remove(), 2000)
            NotificationEl.append(notif_element);
        }
        
        if (myAccountLoacation && myAccountLoacation.trim() !== "") {
            MyAccount.show(myAccountLoacation, true);
            if (myAccountLoacation !== 'MyAccount') {
                $("button#mobile_page_controller > p").text("برگشت");
                $("button#mobile_page_controller").attr("data-url", "MyAccount")
            } else {
                $("button#mobile_page_controller > p").text("ویرایش اطلاعات");
                $("button#mobile_page_controller").attr("data-url", "Information")
            }
        }
        wallet_add_amount.each(function () {
            let orgData = new Date($(this).text().trim());
            let ShamsI = new persianDate(orgData).format('YYYY-MM-DD');
            $(this).text(ShamsI);
        });
        body.on("click tap", "button.CloseNotification", function () {
            $(this).parent().addClass('hidden opacity-0')
        })
        imageInput.on("change", function () {
            Loading.removeClass('hidden')
            let file = this.files[0];
            let data = new FormData();
            data.append("action", "Upload_Profile_Picture");
            data.append("ProfileImage", file);
            $.ajax({
                url: ajax_admin.ajax_url,
                type: "post",
                data: data,
                processData: false,
                contentType: false,
                success: function (res) {
                    Loading.addClass('hidden')
                    if (res.success === true) {
                        NotificationElement('success', res.data);
                        setTimeout(() => {
                            window.location.reload();
                        }, 500)
                    } else {
                        NotificationElement('error', res.data)
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });
        // $("button.submitComment").each(function (el){
        //     el.on("click tap", function () {
        //         let product_id = $(this).val();
        //         const data = {
        //             action: "Submit_Product_Comment",
        //             product_id
        //         }
        //         myAccountLoadAjax(data).then(function (res) {
        //             console.log(res)
        //         });
        //     });
        //
        
        // })
        
        function myAccountLoadAjax(data) {
            return new Promise(function (resolve, reject) {
                Loading.removeClass('hidden')
                
                $.ajax({
                    url: ajax_admin.ajax_url,
                    type: 'post',
                    data: data,
                    success: function (res) {
                        if (res.success === true) {
                            Loading.addClass('hidden')
                            NotificationElement('success', res.data['msg']);
                            resolve(res.data[1]);
                        } else if (res.success === false) {
                            Loading.addClass('hidden')
                            NotificationElement('error', res.data)
                            reject(false);
                        }
                    },
                    error: function (err) {
                        console.log(err);
                        reject(err);
                    }
                });
            });
        }
        
        $('button.myAccountButton').on("click tap", function () {
            let TabName = $(this).attr('data-url');
            Cookies.set('myAccountLoc', TabName, {sameSite: 'none', secure: true, path: '/'});
            myAccountLoacation = Cookies.get('myAccountLoc');
            if (myAccountLoacation !== 'MyAccount') {
                $("button#mobile_page_controller > p").text("برگشت");
                $("button#mobile_page_controller").attr("data-url", "MyAccount")
            } else {
                $("button#mobile_page_controller > p").text("ویرایش اطلاعات");
                $("button#mobile_page_controller").attr("data-url", "Information")
            }
            
            MyAccount.show(TabName);
        });
        $("button#ordersButton").on('click tap', function () {
            let mainUrl = $(this).attr("data-main");
            if (window.location.href !== mainUrl + '/') {
                window.location.href = mainUrl;
            }
        });
        body.on('change focus', 'input.FavStar', function () {
            let rating_stars = $("svg.rating-stars");
            let Selected = parseInt($(this).val()); // تبدیل مقدار به عدد صحیح
            rating_stars.each(function (index, el) {
                if (index < Selected) {
                    $(el).addClass('text-[#E3000F]');
                } else {
                    $(el).removeClass('text-[#E3000F]');
                }
            });
        });
        $("button.product-submit").on("click tap", function () {
            let title = $(this).attr('data-product-title');
            let product_id = $(this).val();
            $('strong.comment-title').text(title)
            $('button.submitComment').val(product_id)
        })
        let formData = new FormData();
        $("#fileInput").change(function () {
            let file = this.files[0];
            if (maxImageFiles >= 5 || maxVideoFile >= 3) {
                NotificationElement('error', 'تعداد فایل های مجاز خود را انتخاب کرده اید.')
                return;
            }
            let extension = file.name.split(".").pop().toLowerCase();
            if (allowedExtensions.indexOf(extension) === -1) {
                alert("فرمت فایل مجاز نیست: " + file.name);
                return;
            }
            
            if (extension === "mp4" && file.size > maxVideoSize) {
                alert("حجم ویدیو بیش از حد مجاز است: " + file.name);
                return;
            }
            
            if (extension !== "mp4" && file.size > maxImageSize) {
                alert("حجم تصویر بیش از حد مجاز است: " + file.name);
                return;
            }
            
            let reader = new FileReader();
            reader.onload = function (e) {
                let preview;
                if (extension === "mp4") {
                    preview = "<video controls><source src='" + e.target.result + "' type='video/mp4'></video>";
                    formData.append("videos[]", file);
                    maxVideoFile++;
                } else {
                    preview = "<img class='h-full rounded-lg' src='" + e.target.result + "'>";
                    formData.append("images[]", file);
                    maxImageFiles++;
                }
                $("#previewContainer").append(preview);
            };
            reader.readAsDataURL(file);
        });
        $("button.submitComment").click(function () {
            let commentText = $("textarea#comment-section").val();
            let ratingStars = $("input[name=FavStar]:checked").val();
            Loading.removeClass('hidden');
            formData.append('action', 'Submit_Product_Comment')
            formData.append('commentText', commentText);
            formData.append('ratingStars', ratingStars);
            formData.append('product_id', $(this).val())
            $.ajax({
                url: ajax_admin.ajax_url, // Replace with your actual target URL
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.success === true) {
                        Loading.addClass('hidden');
                        NotificationElement('success', res.data['msg']);
                        location.reload();
                    } else {
                        Loading.addClass('hidden');
                        NotificationElement('error', res.data);
                    }
                },
                error: function (xhr, status, error) {
                    alert("خطا در ارسال اطلاعات: " + error);
                }
            });
        });
        checkBasicInfoInputs.each(function () {
            if ($(this).val() === '') {
                let button = $("button#basic_info_button > .grow > div.info-checker");
                button.removeClass("text-[#008826] bg-[#CFEFD8]").addClass("text-[#E3000F] bg-[#EFCFCF]")
                button.html('<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="5" cy="5" r="5" fill="#E3000F"/><rect x="3.1438" y="2.08838" width="7" height="1.23523" transform="rotate(45 3.1438 2.08838)" fill="white"/><rect x="8.14502" y="3.01318" width="7" height="1.38051" transform="rotate(135 8.14502 3.01318)" fill="white"/></svg><p>ناقص</p>');
                return false;
            }
        });
        checkBasicBilling.each(function () {
            if ($(this).val() === '') {
                let button = $("button#billing_info_button > .grow > div.info-checker");
                button.removeClass("text-[#008826] bg-[#CFEFD8]").addClass("text-[#E3000F] bg-[#EFCFCF]")
                button.html('<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="5" cy="5" r="5" fill="#E3000F"/><rect x="3.1438" y="2.08838" width="7" height="1.23523" transform="rotate(45 3.1438 2.08838)" fill="white"/><rect x="8.14502" y="3.01318" width="7" height="1.38051" transform="rotate(135 8.14502 3.01318)" fill="white"/></svg><p>ناقص</p>');
                return false;
            }
        });
        checkShippingInfoInputs.each(function () {
            if ($(this).val() === '') {
                let button = $("button#shipping_info_button > .grow > div.info-checker");
                button.removeClass("text-[#008826] bg-[#CFEFD8]").addClass("text-[#E3000F] bg-[#EFCFCF]")
                button.html('<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="5" cy="5" r="5" fill="#E3000F"/><rect x="3.1438" y="2.08838" width="7" height="1.23523" transform="rotate(45 3.1438 2.08838)" fill="white"/><rect x="8.14502" y="3.01318" width="7" height="1.38051" transform="rotate(135 8.14502 3.01318)" fill="white"/></svg><p>ناقص</p>');
                return false;
            }
        });
        staticPhoneNumber.each(function () {
            $(this).on('blur input change', function () {
                let PhoneNumber = $(this).val();
                let regex = /^\d{4,}$/;
                if (regex.test(PhoneNumber)) {
                    $(this).addClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
                    $(this).attr('data-valid', 'true');
                } else {
                    $(this).attr('data-valid', 'false');
                    $(this).addClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
                }
            })
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
        $('input#Telegram').on('blur input change', function () {
            let phone_number_val = $(this).val();
            let allowedLength = phone_number_val.startsWith('0') ? 11 : 10;
            if (phone_number_val.length === allowedLength) {
                $(this).addClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500").removeClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500");
            } else {
                $(this).addClass("border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500").removeClass(" border-green-500 text-green-900 placeholder-green-700 focus:ring-green-500 focus:border-green-500");
            }
        })
        accept_to_exit_main_account.on('click tap', function () {
            Cookies.remove('myAccountLoc');
            window.location.href = this.value;
        })
        
        function validateNationalId(nationalId) {
            if (nationalId.length !== 10 || isNaN(nationalId)) {
                return false;
            }
            
            let first_number = nationalId[0];
            if (nationalId.split('').every(digit => digit === first_number)) {
                return false;
            }
            
            let checksum = parseInt(nationalId[9], 10);
            let digits = nationalId.slice(0, 9);
            
            let sum = 0;
            for (let i = 0; i < digits.length; i++) {
                sum += parseInt(digits[i], 10) * (10 - i);
            }
            
            let mod = sum % 11;
            if (mod < 2) {
                return mod === checksum;
            } else {
                return (11 - mod) === checksum;
            }
        }
    }
);
