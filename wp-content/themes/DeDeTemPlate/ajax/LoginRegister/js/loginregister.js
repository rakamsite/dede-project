jQuery(document).ready(function ($) {
        // deleteCookie('sendSmSPassword');
        let LoginRegisterPage = $("#LoginRegisterModal");
        let phone_number_simulator = $("input.Phone_number");
        let password_Register = $('input#password_Register');
        let loginregisterLoading = $("div#loginregisterLoading");
        const notification_container = $("div#notification_container");
        const notification_msg = $("div#notification_msg");
        const notification_Login_register = $("div#notification_Login_register");
        const InputsGet = $("input.get_message");
        const body = $("body");
        const LoginRegisterPageOption = {
            placement: 'center-center',
            backdrop: 'dynamic',
            backdropClasses: 'bg-[#383838] bg-opacity-50 fixed inset-0 md:z-30',
            closable: true,
            onShow: () => {
                $('[modal-backdrop]').on('click tap', function () {
                    LoginRegisterModal.hide();
                });
            },
        }
        const RegisterLoginTabs = [
            {
                id: 'RegisterLoginMain',
                triggerEl: $('button.LoginRegisterMainButton')[0],
                targetEl: $('div#LoginRegisterMain')[0]
            },
            {
                id: 'LoginWithPassword',
                triggerEl: $('button#Login_with_passwordButton')[0],
                targetEl: $('div#Login_with_password')[0]
            },
            {
                id: 'LoginWithSms',
                triggerEl: $('button.LoginWithSmSButton')[0],
                targetEl: $('div#LoginWithSmS')[0]
            },
            {
                id: 'RegisterMain',
                triggerEl: $('button.RegisterMainButton')[0],
                targetEl: $('div#RegisterMain')[0]
            },
            {
                id: 'GetSMSpassword',
                triggerEl: $('button#GetSMSpasswordButton')[0],
                targetEl: $('div#GetSMSpassword')[0]
            },
            {
                id: 'AfterSuccessRegister',
                triggerEl: $('button#AfterSuccessRegisterButton')[0],
                targetEl: $('div#AfterSuccessRegister')[0]
            },
            {
                id: 'ForgetPassword',
                triggerEl: $('button#ForgetPasswordButton')[0],
                targetEl: $('div#ForgetPassword')[0]
            },
            {
                id: 'ForgetPasswordGetSMS',
                triggerEl: $('button#ForgetPasswordVerifyButton')[0],
                targetEl: $('div#ForgetPasswordVerify')[0]
            },
            {
                id: 'AfterSuccessVerifyForgetPassSms',
                triggerEl: $('button#AfterSuccessVerifyForgetPassSmsButton')[0],
                targetEl: $('div#AfterSuccessVerifyForgetPassSms')[0]
            },

        ];
        const RegisterLoginOptions = {
            defaultTabId: 'RegisterLoginMain',
            activeClasses: 'custom-class',
            inactiveClasses: 'custom-class',
            onShow: (tabs) => {
                let LoginWithSms = $('button.LoginWithSmSButton')
                let loginUsername = $("input#phone_number").val();
                let phone_register = $('input#phone_number_register').val();
                let password_register = $('input#password_Register').val();
                let tabName = tabs._activeTab.id;
                phone_number_simulator.on('keydown', function (event) {
                    if (event.key === 'Enter') {
                        RegisterLoginTab.show('LoginWithSms');
                    }
                });
                LoginWithSms.on('click tap', function () {
                    RegisterLoginTab.show('LoginWithSms');
                });
                $("input.get_message").on("input", function () {
                    const $this = $(this);
                    const value = $this.val();
                    if (value.length >= 1) {
                        const $nextInput = $this.next(".get_message");
                        if ($nextInput.length) {
                            $nextInput.removeAttr('disabled')
                            $nextInput.focus();
                        }
                    }
                });
                $('input#Forget_phone_number, input#phone_number , input#phone_number_register').on('input', function () {
                    let phone_number_val = $(this).val();
                    let allowedLength = phone_number_val.startsWith('0') ? 11 : 10;
                    if (phone_number_val.length === allowedLength) {
                        $('div.register_button_holder > button').removeClass("bg-[#4B5259]").addClass('bg-[#2F2483] hover:bg-[#E3000F]').removeAttr('disabled');
                    } else {
                        $('div.register_button_holder > button').addClass("bg-[#4B5259]").removeClass('bg-[#2F2483] hover:bg-[#E3000F]').attr('disabled', true);
                    }
                });

                $('div.register_button_holder > button').addClass("bg-[#4B5259]").removeClass('bg-[#2F2483] hover:bg-[#E3000F]').attr('disabled');
                if (tabName === "RegisterLoginMain") {
                    
                    $("button#Login_with_passwordButton").on('click tap', function () {
                        loginUsername = $("input#phone_number").val();
                        if (loginUsername !== '') {
                            loginregisterLoading.removeClass('hidden');
                            const data = {
                                action: "Login_register_ajax",
                                LoginWithPassWord: true,
                                loginUsername
                            }
                            LoginRegisterAjax(data)
                                .then(function () {
                                    RegisterLoginTab.show('LoginWithPassword');
                                }).catch(function () {
                                RegisterLoginTab.show('RegisterLoginMain')
                            });
                        }
                    });
                }
                if (tabName === "RegisterMain") {
                    if (Cookies.get('sendSmSPassword')) {
                        RegisterLoginTab.show('RegisterMain');
                    }
                }
                if (tabName === "GetSMSpassword") {
                    $("button.editPhoneRegistering").on("click tap", function () {
                        deleteCookie('sendSmSPassword');
                        RegisterLoginTab.show('RegisterMain',)

                    });
                    $("button.resendSMSPasswordRegisterMain").on('click tap', function () {
                        loginregisterLoading.removeClass('hidden');
                        const data = {
                            action: "Login_register_ajax",
                            register_sms: true,
                            phone_register,
                            password_register
                        }
                        LoginRegisterAjax(data)
                            .then(function () {
                                startCountdown('sendSmSPassword').then(function () {
                                    deleteCookie('sendSmSPassword');
                                });
                            }).catch(function () {
                            RegisterLoginTab.show('RegisterLoginMain')
                        });
                    });
                    if (phone_register !== '' && password_register !== '') {
                        loginregisterLoading.removeClass('hidden');
                        const data = {
                            action: "Login_register_ajax",
                            register_sms: true,
                            phone_register,
                            password_register
                        }
                        LoginRegisterAjax(data)
                            .then(function () {
                                startCountdown('sendSmSPassword').then(function () {
                                    deleteCookie('sendSmSPassword');
                                });
                            }).catch(function () {
                            RegisterLoginTab.show('RegisterLoginMain')
                        });
                    } else {
                        loginregisterLoading.addClass('hidden');
                        $("button.resendSMSPasswordRegisterMain").addClass('hidden')
                        startCountdown('sendSmSPassword').then(function () {
                            $("button.resendSMSPasswordRegisterMain").removeClass('hidden');
                            deleteCookie('sendSmSPassword');
                        });
                        $('#resend_sms_button').addClass('hidden')
                    }
                    $("input#get_message_4").on('input', function () {
                        let get_message_1 = $('input#get_message_1').val();
                        let get_message_2 = $('input#get_message_2').val();
                        let get_message_3 = $('input#get_message_3').val();
                        let get_message_4 = $('input#get_message_4').val();
                        let SmSPassword = get_message_1 + get_message_2 + get_message_3 + get_message_4;
                        $('input, textarea').blur();
                        const data = {
                            action: "Login_register_ajax",
                            verifySmsPass: true,
                            SmSPassword,
                        }
                        LoginRegisterAjax(data).then(function () {
                            $("button#AfterSuccessRegisterButton").removeClass('bg-[#4B5259]').addClass('bg-[#2F2483] hover:bg-[#E3000F]').removeAttr('disabled');
                        }).catch(function () {
                            setTimeout(function () {
                                $('input#get_message_1').val('').focus();
                                $('input#get_message_2').val('');
                                $('input#get_message_3').val('');
                                $('input#get_message_4').val('');
                            }, 2000);
                        });
                    });
                }
                if (tabName === "AfterSuccessRegister") {
                    $("div#notification_Login_register").addClass('hidden')
                    $('button.select_type').on("click tap", function () {
                        $(this).children(".inputSelect").prop('checked', true)
                        $("button#select_button").removeClass('bg-[#4B5259]').addClass('bg-[#2F2483] hover:bg-[#E3000F]').removeAttr('disabled');
                    });

                    $('button#select_button').on('click tap', function () {
                        let select_type = $('input[name=inputSelect]:checked').val()
                        const date = {
                            action: "Login_register_ajax",
                            updateUserRol: true,
                            select_type
                        }
                        LoginRegisterAjax(date).then(function () {
                            let panel_url = $("button#select_button").attr('data-panel-ui');
                            window.location.replace(panel_url);
                            deleteCookie('sendSmSPassword');
                        });
                    });
                }
                if (tabName === "LoginWithPassword") {
                    deleteCookie('SendForgetSms');
                    let Password = $("input#Password");
                    function login_with_password (){
                        loginregisterLoading.removeClass('hidden');
                        const data = {
                            action: "Login_register_ajax",
                            LoginWithPassWordPassword: true,
                            Password: Password.val()
                        }
                        LoginRegisterAjax(data)
                            .then(function () {
                                location.reload();
                            });

                    }
                    Password.on("keydown" , function (event){
                        if (event.key === 'Enter') {
                            login_with_password();
                        }
                    })
                    $('button#LoginWithPassButton').on('click tap', function () {
                        login_with_password();
                    });
                }
                if (tabName === "LoginWithSms") {
                    loginregisterLoading.removeClass('hidden');
                    const data = {
                        action: "Login_register_ajax",
                        LoginWithSms: true,
                        loginUsername,
                    };
                    LoginRegisterAjax(data).then(function () {
                        let AfterLogin = $("button#AfterSuccessLogin");
                        let ResendSmsLogin = $("button#resendSMSLoginPassword");
                        ResendSmsLogin.addClass('text-[#E9E9E9]').removeClass('text-[#0058BF]').attr('disabled', true);
                        $("button.RegisterMainButton").on('click tap', function () {
                            deleteCookie('LoginTempMessage');
                        });
                        ResendSmsLogin.on("click tap", function () {
                            RegisterLoginTab.show("LoginWithSms", true);
                        });
                        startCountdown("LoginTempMessage").then(function () {
                            ResendSmsLogin.removeClass('text-[#E9E9E9]').addClass('text-[#0058BF]').removeAttr('disabled');
                            deleteCookie('LoginTempMessage')
                        });
                        $("input#pass_message_4").on('input', function () {
                            let TempPassPart1 = $("input#pass_message_1").val();
                            let TempPassPart2 = $("input#pass_message_2").val();
                            let TempPassPart3 = $("input#pass_message_3").val();
                            let TempPassPart4 = $("input#pass_message_4").val();
                            let tempLoginPass = TempPassPart1 + TempPassPart2 + TempPassPart3 + TempPassPart4;
                            $('input, textarea').blur();
                            const data = {
                                action: "Login_register_ajax",
                                verifyLoginSMS: true,
                                tempLoginPass
                            };
                            LoginRegisterAjax(data).then(function () {
                                AfterLogin.removeClass("bg-[#4B5259]").addClass('bg-[#2F2483] hover:bg-[#E3000F]').removeAttr('disabled');
                                location.reload();
                            }).catch(function () {
                                setTimeout(function () {
                                    $("input#pass_message_1").val('').focus();
                                    $("input#pass_message_2").val('');
                                    $("input#pass_message_3").val('');
                                    $("input#pass_message_4").val('');
                                    AfterLogin.addClass("bg-[#4B5259]").removeClass('bg-[#2F2483] hover:bg-[#E3000F]').attr('disabled', true);
                                }, 2000);
                            });
                        });

                        $("button#SendToEmail").on('click tap', function () {
                            $("div#resendEmailButton > button ").addClass('text-[#E9E9E9]').removeClass('text-[#0058BF]').attr('disabled', true);
                            const data = {
                                action: "Login_register_ajax",
                                SendTempPassToEmail: true,
                            }
                            LoginRegisterAjax(data);
                            deleteCookie("LoginTempMessage");
                            startCountdown("LoginTempMessage").then(function () {
                                $("div#resendEmailButton > button ").removeClass('text-[#E9E9E9]').addClass('text-[#0058BF]').removeAttr('disabled');
                                deleteCookie('LoginTempMessage');
                            })
                        });
                    }).catch(function () {
                        RegisterLoginTab.show('RegisterLoginMain');
                    });
                }
                if (tabName === "ForgetPassword") {
                    $("#Forget_phone_number").on("keydown" , function (event){
                        if (event.key === 'Enter') {
                            RegisterLoginTab.show('ForgetPasswordGetSMS');
                        }
                    })
                    if (Cookies.get('SendForgetSms')) {
                        RegisterLoginTab.show('ForgetPasswordGetSMS');
                    }
                }
                if (tabName === "ForgetPasswordGetSMS") {
                    let Forget_phone_number = $('input#Forget_phone_number').val();
                    if (Forget_phone_number === ''){
                        RegisterLoginTab.show('ForgetPassword')
                    }
                    $("div.user_phone_number").text(Forget_phone_number);
                    let backToForgetPass = $("button.backToForgetPass");
                    let resendForgetPassSms = $("button.resendForgetPassSms");
                    backToForgetPass.addClass('hidden');
                    resendForgetPassSms.addClass('hidden');
                    backToForgetPass.on('click tap', function () {
                        deleteCookie("SendForgetSms");
                        RegisterLoginTab.show('ForgetPassword');
                    });
                    let AfterSuccessVerifyForgetPassSms = $("button#AfterSuccessVerifyForgetPassSmsButton");
                    loginregisterLoading.removeClass('hidden');
                    if (Forget_phone_number !== '') {
                        const data = {
                            action: "Login_register_ajax",
                            verifyForgetPassSms: true,
                            Forget_phone_number
                        }
                        LoginRegisterAjax(data).then(function () {
                            $("button#backToForgetPass").addClass('hidden');
                            startCountdown('SendForgetSms').then(function () {
                                backToForgetPass.removeClass('hidden');
                                resendForgetPassSms.removeClass('hidden');
                                deleteCookie('SendForgetSms');
                            });
                        }).catch(function () {
                            RegisterLoginTab.show('ForgetPassword');
                        });
                    } else {
                        loginregisterLoading.addClass('hidden');
                        startCountdown('SendForgetSms').then(function () {
                            deleteCookie('SendForgetSms')
                        });
                    }
                    $("input#get_forger_message_4").on("input", function () {
                        let forgetMessage_1 = $('input#get_forger_message_1').val();
                        let forgetMessage_2 = $('input#get_forger_message_2').val();
                        let forgetMessage_3 = $('input#get_forger_message_3').val();
                        let forgetMessage_4 = $('input#get_forger_message_4').val();
                        let forget_pass_code = forgetMessage_1 + forgetMessage_2 + forgetMessage_3 + forgetMessage_4;
                        $('input, textarea').blur();
                        const data = {
                            action: "Login_register_ajax",
                            verifySmsForgetPass: true,
                            forget_pass_code
                        }
                        LoginRegisterAjax(data).then(function () {
                            AfterSuccessVerifyForgetPassSms.removeClass("bg-[#4B5259]").addClass('bg-[#2F2483] hover:bg-[#E3000F]').removeAttr('disabled');
                        }).catch(function () {
                            setTimeout(function () {
                                $("input#get_forger_message_1").val('').focus();
                                $("input#get_forger_message_2").val('');
                                $("input#get_forger_message_3").val('');
                                $("input#get_forger_message_4").val('');
                            }, 2000);
                            AfterSuccessVerifyForgetPassSms.addClass("bg-[#4B5259]").removeClass('bg-[#2F2483] hover:bg-[#E3000F]').attr('disabled', true);
                        });
                    })
                }
                if (tabName === "AfterSuccessVerifyForgetPassSms") {
                    loginregisterLoading.removeClass('hidden');
                    const data = {
                        action: "Login_register_ajax",
                        checkVerifySMS: true,
                    }
                    LoginRegisterAjax(data).catch(function () {
                        RegisterLoginTab.show('ForgetPassword');
                    });
                    let password2 = $("input#ForgetPassTow");
                    let mach_pass = $("p#match_pass");
                    let ResetStatickPassword = $("button#ResetStatickPassword");
                    password2.on('input', function () {
                        let password1 = $("input#ForgetPassOne").val();
                        let pass2val = $(this).val()
                        if (password1 === pass2val) {
                            mach_pass.removeClass('text-pink-500').addClass('text-green-500').text('رمز وارد شده درست همسان میباشد.');
                            ResetStatickPassword.removeClass("bg-[#4B5259]").addClass('bg-[#2F2483] hover:bg-[#E3000F]').removeAttr('disabled');
                        } else {
                            mach_pass.addClass('text-pink-500').removeClass('text-green-500').text('رمز وارد شده همسان نمیباشد.');
                            ResetStatickPassword.addClass("bg-[#4B5259]").removeClass('bg-[#2F2483] hover:bg-[#E3000F]').attr('disabled', true);
                        }
                    });
                    $("span.showPassword").on("click", function () {
                        let inputs = $("input.ForgetPass");
                        let inputType = inputs.attr('type');
                        if (inputType === "password") {
                            inputs.attr("type", "text");
                        } else {
                            inputs.attr("type", "password");
                        }
                    });
                    ResetStatickPassword.on("click tap", function () {
                        let NewPass = $("input#ForgetPassTow").val();
                        loginregisterLoading.removeClass('hidden');
                        const data = {
                            action: "Login_register_ajax",
                            ChangePass: true,
                            NewPass
                        }
                        LoginRegisterAjax(data).then(function () {
                            loginregisterLoading.addClass('hidden');
                            RegisterLoginTab.show('RegisterLoginMain');
                        }).catch(function () {
                            loginregisterLoading.addClass('hidden');
                        });
                    });
                }
            }
        };
        const RegisterLoginTab = new Tabs(RegisterLoginTabs, RegisterLoginOptions);
        const LoginRegisterModal = new Modal(LoginRegisterPage[0], LoginRegisterPageOption);
        let select_type_holder = $("div.select_type_holder > button");
        $("button.LoginRegisterMainButton").on('click tap', function () {
            RegisterLoginTab.show('RegisterLoginMain');
        });
        $("button.RegisterMainButton").on('click tap', function () {
            RegisterLoginTab.show('RegisterMain');
        });
        password_Register.on('input', function () {
            const minLength = 5;
            const maxLength = 20;
            const pattern = /^[a-zA-Z0-9]+$/;
            const password = $(this).val();
            const errorMsg = $("p#RegisterErrorMsg");
            if (password.length < minLength || password.length > maxLength) {
                errorMsg.text("طول رمز عبور باید بین 5 تا 20 کاراکتر باشد.");
                $("button#GetSMSpasswordButton").addClass("bg-[#4B5259]").removeClass('bg-[#2F2483] hover:bg-[#E3000F]').attr('disabled');
            } else if (!pattern.test(password)) {
                errorMsg.text("رمز عبور فقط می‌تواند حروف و اعداد انگلیسی باشد.");
                $("button#GetSMSpasswordButton").addClass("bg-[#4B5259]").removeClass('bg-[#2F2483] hover:bg-[#E3000F]').attr('disabled');
            } else {
                errorMsg.text("");
                $("button#GetSMSpasswordButton").removeClass("bg-[#4B5259]").addClass('bg-[#2F2483] hover:bg-[#E3000F]').removeAttr('disabled');
            }
        });
        $(".get_message").on("keyup", function () {
            const currentInput = $(this);
            const nextInput = currentInput.next(".get_message");

            if (currentInput.val().length === currentInput.attr("maxlength")) {
                if (nextInput.length > 0) {
                    nextInput.focus();
                } else {
                    currentInput.blur();
                }
            }
        });
        phone_number_simulator.on('blur', function () {
            $('span.user_phone_number').text($(this).val())
        });
        select_type_holder.on('click tap', function () {
            $('a#accept_link').removeClass("bg-[#4B5259]").addClass('bg-[#2F2483] hover:bg-[#E3000F]');
        });

        $('button[data-modal-target=LoginRegisterModal]').on('click tap', function () {
            LoginRegisterModal.hide();
        });
        $("button.login_register_page").on('click tap', function () {
            LoginRegisterModal.show();
            RegisterLoginTab.show('RegisterLoginMain')
        });

        function createCookie(cookieName) {
            const expirationDate1 = new Date();
            expirationDate1.setTime(expirationDate1.getTime() + (120 * 1000));
            Cookies.set(cookieName, expirationDate1, {expires: expirationDate1});
        }

        function getRemainingTimeInSeconds(cookieName) {
            let cookieValue = Cookies.get(cookieName);
            if (cookieValue) {
                let expirationDate = new Date(Date.parse(cookieValue));
                let currentDate = new Date();
                let remainingSeconds = Math.floor((expirationDate - currentDate) / 1000);
                return remainingSeconds > 0 ? remainingSeconds : 0;
            }
            return false;
        }

        function deleteCookie(cookieName) {
            Cookies.remove(cookieName);
        }

        function startCountdown(cookieName) {
            let interval;
            let existingCookie = Cookies.get(cookieName);
            if (!existingCookie) {
                createCookie(cookieName);
            }
            return new Promise(function (resolve, reject) {
                interval = setInterval(function () {
                    let remainingSeconds = getRemainingTimeInSeconds(cookieName);
                    if (remainingSeconds > 0) {
                        $("div.timer_holder > span").text(remainingSeconds);
                        $("button.resendSMSPasswordRegisterMain").addClass('hidden')
                        $("div.timer_holder").removeClass('hidden');
                    } else {
                        $("div.timer_holder").addClass('hidden');
                        $("button.resendSMSPasswordRegisterMain").removeClass('hidden')
                        clearInterval(interval);
                        resolve("complete");
                    }
                }, 1000);
            })
        }

        $('.resendForgetPassSms').on('click tap', function () {
            let Forget_phone_number = $('input#Forget_phone_number').val();
            let backToForgetPass = $("button.backToForgetPass");
            let resendForgetPassSms = $("button.resendForgetPassSms");
            if (Forget_phone_number !== '') {
                loginregisterLoading.removeClass('hidden');
                resendForgetPassSms.addClass('hidden')
                const data = {
                    action: "Login_register_ajax",
                    verifyForgetPassSms: true,
                    Forget_phone_number
                }
                LoginRegisterAjax(data).then(function () {
                    loginregisterLoading.addClass('hidden')
                    startCountdown('SendForgetSms').then(function () {
                        backToForgetPass.removeClass('hidden');
                        resendForgetPassSms.removeClass('hidden');
                        deleteCookie('SendForgetSms');
                    });
                }).catch(function () {
                    RegisterLoginTab.show('ForgetPassword');
                });
            } else {
                loginregisterLoading.addClass('hidden');
                startCountdown('SendForgetSms').then(function () {
                    deleteCookie('SendForgetSms')
                });
            }
        });
        body.on('click tap', '.close-notif', function () {
            notification_Login_register.addClass('hidden');
        })
        InputsGet.each(function () {
            $(this).on("input change", function () {
                let Value = $(this).val();
                if (Value.length > 1) {
                    Value = Value.slice(0, -1);
                    $(this).val(Value);
                }
            });
        });

        function LoginRegisterAjax(data) {
            return new Promise(function (resolve, reject) {
                $.ajax({
                    url: ajax_admin.ajax_url,
                    type: 'post',
                    data: data,
                    success: function (res) {
                        if (res.success === true) {
                            notification_Login_register.removeClass('hidden');
                            notification_container.addClass('bg-[#008826]').removeClass('bg-[#E3000F]');
                            notification_msg.html(res.data + '<button class="absolute z-50 top-1/2 transform -translate-y-1/2 left-0 text-2xl close-notif">x</button>');
                            loginregisterLoading.addClass('hidden');
                            resolve(true);
                        } else if (res.success === false) {
                            notification_Login_register.removeClass('hidden');
                            notification_container.removeClass('bg-[#008826]').addClass('bg-[#E3000F]');
                            notification_msg.html(res.data + '<button class="absolute z-50 top-1/2 transform -translate-y-1/2 left-0 text-2xl close-notif">x</button>');
                            loginregisterLoading.addClass('hidden');
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
    }
)
;