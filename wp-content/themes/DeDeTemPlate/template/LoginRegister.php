<div id="LoginRegisterModal"
     class="fixed top-1/2 left-1/2 transform -translate-y-1/2 -translate-x-1/2 z-50 hidden p-4 overflow-x-hidden overflow-y-auto w-full md:w-[550px] h-screen md:h-[500px] bg-gray-50 text-black flex flex-col-reverse rounded-lg">
    <button class="absolute top-5 left-5 z-40" data-modal-target="LoginRegisterModal" type="button">
        <svg class="h-8 text-[#D9D9D9] cursor-pointer z-50" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"></path>
        </svg>
    </button>
    <div id="notification_Login_register" class="absolute md:top-4 bottom-0 md:bottom-auto w-full md:w-2/3 hidden z-50">
        <div id="notification_container"
             class="flex items-center p-10 md:p-4 text-sm md:rounded-lg text-white" role="alert">
            <div id="notification_msg" class="relative ml-3 text-sm font-medium w-full text-center"></div>
        </div>
    </div>
    <div id="loginregisterLoading"
         class="absolute bg-black/50 w-full h-full z-30 flex justify-center items-center hidden">
        <svg class="animate-spin h-24 w-24 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path
        </svg>
    </div>
    <div id="LoginRegisterMain" class="flex md:justify-center justify-start items-center w-full h-full">
        <p class="md:hidden absolute top-5 w-full text-center text-[24px] font-[700] text-[#525252] bg-gray-50 z-30">ورود یا ثبت نام</p>
        <div class="flex flex-col justify-center text-center m-auto h-full md:h-auto w-full md:w-2/3 md:gap-2 relative ">
            <svg class="mx-auto hidden md:block z-50" width="46" height="57" viewBox="0 0 46 57" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M22.6999 22.7C28.9683 22.7 34.0499 17.6184 34.0499 11.35C34.0499 5.08157 28.9683 0 22.6999 0C16.4314 0 11.3499 5.08157 11.3499 11.35C11.3499 17.6184 16.4314 22.7 22.6999 22.7Z"
                      fill="#4B5259"/>
                <path d="M45.4 43.9814C45.4 51.0326 45.4 56.7501 22.7 56.7501C0 56.7501 0 51.0326 0 43.9814C0 36.9302 10.1639 31.2126 22.7 31.2126C35.2361 31.2126 45.4 36.9302 45.4 43.9814Z"
                      fill="#4B5259"/>
            </svg>
            <p class="text-[24px] font-[700] text-[#525252] hidden md:block">ورود یا ثبت نام</p>
            <p class="text-[18px] font-[500] text-[#525252] mt-1">برای ادامه شماره موبایل خود را وارد کنید.</p>
            <div class="relative mt-3 md:mt-0 mb-8 md:mb-0">
                <input type="number" inputmode="numeric" pattern="[0-9]*" name="phone_number" id="phone_number"
                       class="rounded-lg px-1 py-2 pl-12 border-[1px] border-black w-full invalid:border-red-900 invalid:border-2 placeholder:text-right text-left Phone_number "
                       placeholder="شماره موبایل"/>
                <span class="absolute left-2 top-1/2 transform -translate-y-1/2 pr-2 border-r-[1px] border-[#4B5259] text-[#979797]">
                        98+
                    </span>
            </div>
            <p class="text-[14px] font-[700] text-[#525252] mt-3 hidden md:block">
                ثبت نام در DeDe.ir به معنی پذیرش <a href="https://dede.ir/terms" class="text-[#0058BF]">قوانین و مقررات</a> این سایت
                است.
            </p>
            <div class="w-full flex flex-col gap-8 md:gap-1 mt-3 register_button_holder md:static">
                <button type="button" aria-expanded="false"
                        class="p-4 text-white bg-[#4B5259] rounded-lg LoginWithSmSButton" disabled="disabled">ورود
                    با رمز پیامکی
                </button>
                <button type="button" aria-expanded="false" id="Login_with_passwordButton"
                        class="p-4 text-white bg-[#4B5259] rounded-lg" disabled="disabled">
                    ورود با رمز ثابت
                </button>
                <p class="text-[18px] font-[500] text-[#525252] md:mt-3">حساب کاربری ندارید؟
                    <button class="text-[#0058BF] RegisterMainButton">ثبت نام کنید</button>
                </p>
            </div>
        </div>
    </div>
    <div id="LoginWithSmS"  class="flex md:justify-center justify-start items-center w-full h-full">
        <div class="flex flex-col justify-center text-center m-auto md:w-2/3">
            <p class="text-[24px] font-[700] text-[#525252] mt-1">رمز پیامکی را وارد کنید</p>
            <div class="flex justify-center">
                <p class="text-[18px] font-[500] text-[#525252] mt-2">رمز پیامکی 4 رقمی به شماره <span
                            class="user_phone_number"></span> ارسال شد.</p>
            </div>

            <button class="text-[#0058BF] mt-10 mb-3 LoginRegisterMainButton" type="button" aria-expanded="false">
                اصلاح شماره موبایل
            </button>
            <div class="grid grid-cols-4 gap-5 w-[260px] h-[50px] mx-auto mt-3" dir="ltr">
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" id="pass_message_1"
                       class="get_message rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"/>
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" id="pass_message_2"
                       class="get_message rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                       disabled="disabled"/>
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" id="pass_message_3"
                       class="get_message  rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                       disabled="disabled"/>
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" id="pass_message_4"
                       class="get_message rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                disabled="disabled"/>
            </div>
            <div class="flex text-[#525252] text-[14px] mt-5 mx-auto">
                <div class="timer_holder">
                    <span class="inline-block ml-1"></span>
                    <p class="inline-block">ثانیه تا درخواست مجدد رمز پیامکی</p>
                </div>
            </div>
            <div id="resendEmailButton"
                 class="flex justify-center items-center gap-3 divide-x divide-x-reverse py-8 text-[14px] font-[700] ">
                <button id="resendSMSLoginPassword" class="text-[#E9E9E9]" type="button"
                        aria-expanded="false" disabled="disabled">
                    ارسال مجدد رمز پیامکی
                </button>

                <button id="SendToEmail" class="text-[#0058BF] border-[#525252] px-3 hidden" type="button"
                        aria-expanded="false">
                    ارسال رمز به ایمیل
                </button>

            </div>
            <button id="AfterSuccessLogin" class="bg-[#4B5259] p-4 rounded-lg w-full text-white mt-5"
                    disabled="disabled">
                تایید و ادامه
            </button>
        </div>

    </div>
    <div id="ForgetPassword" class="flex md:justify-center justify-start items-center w-full h-full">
        <div class="flex flex-col justify-center text-center m-auto w-full md:w-2/3">
            <p class="text-[24px] font-[700] text-[#525252] mt-1">فراموشی رمز ثابت</p>
            <p class="text-[18px] font-[500] text-[#525252] mt-1">برای بازیابی رمز ثابت، شماره موبایل خود را وارد
                کنید.</p>
            <div class="relative mt-10">
                <input type="number" inputmode="numeric" pattern="[0-9]*" name="Forget_phone_number" id="Forget_phone_number"
                       class="rounded-lg px-1 py-2 pl-12 border-[1px] border-black w-full invalid:border-red-900 invalid:border-2 placeholder:text-right text-left Phone_number"
                       placeholder="شماره موبایل"/>
<!--                <span class="absolute left-2 top-1/2 transform -translate-y-1/2 pr-2 border-r-[1px] border-[#4B5259] text-[#979797]">-->
<!--                        98+-->
<!--                    </span>-->
            </div>
            <div class="register_button_holder">
                <button id="ForgetPasswordVerifyButton" class="bg-[#4B5259] p-4 rounded-lg w-full text-white mt-10"
                        disabled="disabled">
                    تایید و ادامه
                </button>
            </div>
            <button id="SendToEmail" class="text-[#0058BF] border-[#525252] px-3 mx-auto mt-5 LoginWithSmSButton"
                    type="button"
                    aria-expanded="false">
                ورود با رمز پیامکی
            </button>

        </div>
    </div>
    <div id="RegisterMain"  class="flex md:justify-center justify-start items-center w-full h-full">
        <div class="flex flex-col justify-center text-center m-auto md:w-2/3">
            <svg class="mx-auto" width="46" height="57" viewBox="0 0 46 57" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M22.6999 22.7C28.9683 22.7 34.0499 17.6184 34.0499 11.35C34.0499 5.08157 28.9683 0 22.6999 0C16.4314 0 11.3499 5.08157 11.3499 11.35C11.3499 17.6184 16.4314 22.7 22.6999 22.7Z"
                      fill="#4B5259"/>
                <path d="M45.4 43.9814C45.4 51.0326 45.4 56.7501 22.7 56.7501C0 56.7501 0 51.0326 0 43.9814C0 36.9302 10.1639 31.2126 22.7 31.2126C35.2361 31.2126 45.4 36.9302 45.4 43.9814Z"
                      fill="#4B5259"/>
            </svg>
            <p class="text-[24px] font-[700] text-[#525252] mt-1">ثبت نام کاربر جدید</p>
            <div class="relative mt-3">
                <input type="number" inputmode="numeric" pattern="[0-9]*" name="phone_number_register" id="phone_number_register"
                       class="rounded-lg px-1 py-2 pl-12 border-[1px] border-black w-full invalid:border-red-900 invalid:border-2 placeholder:text-right text-left Phone_number"
                       placeholder="شماره موبایل"/>
                <span class="absolute left-2 top-1/2 transform -translate-y-1/2 pr-2 border-r-[1px] border-[#4B5259] text-[#979797]">
                        98+
                    </span>
            </div>
            <input type="text" name="password_Register" id="password_Register"
                   class="rounded-lg px-1 py-2 border-[1px] border-black w-full invalid:border-red-900 invalid:border-2 mt-2 "
                   placeholder="رمز ثابت جدید"/>
            <p class="mt-2 text-sm text-red-600 " id="RegisterErrorMsg"></p>
            <p class="text-[14px] font-[700] text-[#525252] mt-3">
                ثبت نام در DeDe.ir به معنی پذیرش <a href="https://dede.ir/terms" class="text-[#0058BF]">قوانین و مقررات</a> این سایت
                است.
            </p>
            <button id="GetSMSpasswordButton" class="bg-[#4B5259] p-4 rounded-lg w-full text-white mt-10"
                    disabled="disabled">
                تایید و ادامه
            </button>
            <p class="text-[18px] font-[500] text-[#525252] md:mt-3">حساب کاربری دارید؟
                <button class="text-[#0058BF] LoginRegisterMainButton">وارد شوید</button>
            </p>

        </div>
    </div>
    <div id="Login_with_password" class="flex md:justify-center justify-start items-center w-full h-full">
        <div class="flex flex-col justify-center text-center md:w-2/3 mx-auto">
            <p class="text-[24px] font-[700] text-[#525252] mt-1">رمز ثابت را وارد کنید</p>
            <p class="text-[18px] font-[500] text-[#525252] mt-1">رمزی که قبلا برای حساب خود انتخاب کرده اید را وارد
                کنید.</p>
            <button class="text-[#0058BF] mt-4 mb-3 LoginRegisterMainButton" type="button" aria-expanded="false">
                اصلاح شماره موبایل
            </button>
            <div class="relative mt-3 px-5">
                <input type="password" name="Password" id="Password"
                       class="rounded-lg px-1 py-2 border-[1px] border-black w-full focus:text-center"
                       placeholder="رمز ثابت"/>
            </div>
            <div class="register_button_holder mt-10 px-5">
                <button id="LoginWithPassButton" class="bg-[#4B5259] p-4 rounded-lg w-full text-white"
                        disabled="disabled">
                    تایید و ادامه
                </button>
            </div>
            <div class="mt-1 flex justify-between w-full px-5">
                <button class="text-[#0058BF] mt-4 mb-3" type="button" aria-expanded="false"
                        id="ForgetPasswordButton">
                    فراموشی رمز ثابت
                </button>
                <button class="text-[#0058BF] mt-4 mb-3 LoginWithSmSButton" type="button" aria-expanded="false"
                        id="LoginWithSMS">
                    ورود با رمز پیامکی
                </button>
            </div>
            <p class="text-[18px] font-[500] text-[#525252] mt-3">حساب کاربری ندارید؟
                <button class="text-[#0058BF] RegisterMainButton">ثبت نام کنید</button>
            </p>
        </div>
    </div>
    <div id="GetSMSpassword"  class="flex md:justify-center justify-start items-center w-full h-full" >
        <div class="flex flex-col justify-center text-center m-auto md:w-2/3">
            <p class="text-[24px] font-[700] text-[#525252] mt-1">رمز پیامکی را وارد کنید</p>
            <div class="flex justify-center">
                <p class="text-[18px] font-[500] text-[#525252] mt-2">رمز پیامکی 4 رقمی به شماره <span
                            class="user_phone_number"></span> ارسال شد.</p>
            </div>

            <button class="text-[#0058BF] mt-10 mb-3 editPhoneRegistering" type="button" aria-expanded="false">
                اصلاح شماره موبایل
            </button>
            <div class="grid grid-cols-4 gap-5 w-[260px] h-[50px] mx-auto mt-5" dir="ltr">
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" name="get_message_1" id="get_message_1"
                       class="get_message rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"/>
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" name="get_message_2" id="get_message_2"
                       class="get_message rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                       disabled="disabled"/>
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" name="get_message_3" id="get_message_3"
                       class="get_message rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                       disabled="disabled"/>
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" name="get_message_4" id="get_message_4"
                       class="get_message rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                       disabled="disabled"/>
            </div>
            <div id="resend_sms_timer" class="flex text-[#525252] text-[14px] mt-5 mx-auto">
                <div class="timer_holder">
                    <span class="inline-block ml-1"></span>
                    <p class="inline-block">ثانیه تا درخواست مجدد رمز پیامکی</p>
                </div>
            </div>
            <button class="text-[#0058BF] mt-2 resendSMSPasswordRegisterMain text-[14px] font-[700]" type="button"
                    aria-expanded="false">
                ارسال مجدد رمز پیامکی
            </button>
            <button id="AfterSuccessRegisterButton" class="bg-[#4B5259] p-4 rounded-lg w-full text-white mt-10"
                    disabled="disabled">
                تایید و ادامه
            </button>
            <p class="text-[18px] font-[500] text-[#525252] md:mt-3">حساب کاربری دارید؟
                <button class="text-[#0058BF] LoginRegisterMainButton">وارد شوید</button>
            </p>

        </div>
    </div>
    <div id="AfterSuccessRegister"  class="flex flex-col md:justify-center justify-start items-center w-full h-full overflow-y-auto">
        <div class="flex flex-col justify-center text-center md:w-2/3">
            <div class="">
                <p class="text-[24px] font-[700] text-[#525252] mt-1">حساب کاربری شما ایجاد گردید</p>
                <p class="text-[18px] font-[500] text-[#525252] mt-2">لطفا نوع حساب کاربری خود جهت خرید و دریافت فاکتور
                    را مشخص نمایید</p>
            </div>
            <div class=" grid grid-cols-1 md:grid-cols-3 items-center gap-2 mt-10 select_type_holder ">
                <button
                        class="bg-[#4B5259] md:w-full mx-auto rounded-lg flex flex-col justify-center items-center px-5 py-2 text-white hover:bg-[#E3000F] focus:bg-[#E3000F] cursor-pointer w-[150px] select_type">
                    <svg width="64" height="79" viewBox="0 0 64 79" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M61.847 56.9666C58.5315 52.5057 53.7978 49.3107 48.4267 47.9085L48.2593 47.8723L44.3897 54.4175C44.3871 55.2891 44.0405 56.1241 43.4256 56.7404C42.8106 57.3566 41.9773 57.704 41.1077 57.7066C39.3026 57.7066 37.8256 56.2265 36.1026 51.8718V51.7666C36.1026 50.6762 35.6703 49.6304 34.901 48.8594C34.1316 48.0884 33.0881 47.6552 32 47.6552C30.9119 47.6552 29.8684 48.0884 29.099 48.8594C28.3297 49.6304 27.8974 50.6762 27.8974 51.7666V51.8751V51.8685C26.0595 56.2265 24.5694 57.7033 22.7676 57.7033C21.8979 57.7007 21.0646 57.3534 20.4497 56.7371C19.8348 56.1208 19.4881 55.2858 19.4855 54.4143L15.7374 47.8559C10.3294 49.2343 5.55241 52.4204 2.19569 56.8876L2.14974 56.9501C0.819199 59.1309 0.0783042 61.6213 0 64.1762V64.1992C0.0164103 64.6926 0 65.2682 0 65.8438V72.4219C0 74.1665 0.691573 75.8397 1.92258 77.0733C3.15359 78.307 4.82319 79 6.5641 79H57.4359C59.1768 79 60.8464 78.307 62.0774 77.0733C63.3084 75.8397 64 74.1665 64 72.4219V65.8438C64 65.2715 63.9836 64.6926 64 64.1992C63.9173 61.6152 63.1639 59.0974 61.8142 56.8942L61.8503 56.96L61.847 56.9666ZM14.7659 17.4814C14.7659 27.0854 20.7393 41.1955 31.9967 41.1955C43.0572 41.1955 49.2275 27.0854 49.2275 17.4814V17.2676C49.2275 15 48.7818 12.7546 47.9159 10.6596C47.0499 8.56456 45.7807 6.661 44.1807 5.05756C42.5807 3.45411 40.6812 2.18219 38.5906 1.31442C36.5001 0.446639 34.2595 0 31.9967 0C29.7339 0 27.4933 0.446639 25.4028 1.31442C23.3123 2.18219 21.4128 3.45411 19.8127 5.05756C18.2127 6.661 16.9435 8.56456 16.0776 10.6596C15.2116 12.7546 14.7659 15 14.7659 17.2676V17.4912V17.4814Z"
                              fill="white"/>
                    </svg>
                    <p class="mt-1">شخص</p>
                    <input name="inputSelect" class="inputSelect hidden" type="radio" value="personal">
                </button>
                <button class="bg-[#4B5259] md:w-full mx-auto rounded-lg flex flex-col justify-center items-center px-5 py-2 text-white hover:bg-[#E3000F] focus:bg-[#E3000F] cursor-pointer w-[150px] select_type">
                    <svg width="77" height="79" viewBox="0 0 77 79" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M69.5568 73.9921V73.1053C69.5568 53.856 69.5568 34.5547 69.5568 15.3055C69.5568 14.836 69.3996 14.5751 69.0327 14.3143C62.7427 9.61939 56.4527 4.97662 50.2151 0.281696C49.7958 -0.0312995 49.4813 -0.0834654 49.0095 0.125198C36.2199 5.70695 23.4826 11.2887 10.693 16.8183C10.064 17.0791 9.90674 17.3921 9.90674 18.0703C9.90674 36.4326 9.90674 54.8472 9.90674 73.2096V74.0442H0V79H68.2988V78.9478H77V73.9921H69.5568ZM39.3125 51.8737C33.8087 53.4387 28.2526 55.0037 22.7488 56.5687C22.644 56.5687 22.5391 56.6208 22.4343 56.6208C22.4343 56.4643 22.3819 56.36 22.3819 56.2035C22.3295 53.4387 22.3295 50.6218 22.2771 47.857C22.2771 47.544 22.2771 47.3353 22.6964 47.1788C28.2526 45.4573 33.8087 43.6837 39.3649 41.9622C39.4697 41.9101 39.5745 41.9101 39.7318 41.8579C39.7318 42.0666 39.7842 42.2231 39.7842 42.3796C39.7842 45.3008 39.7842 48.2743 39.7842 51.1956C39.7318 51.6129 39.6794 51.7694 39.3125 51.8737ZM39.7318 61.6809V73.9921H22.3819C22.3819 71.5924 22.3819 69.245 22.3819 66.8975C22.3819 66.741 22.644 66.5324 22.8536 66.4802C25.8414 65.5934 28.8291 64.7587 31.7645 63.9241C34.3853 63.1937 37.0061 62.4113 39.7318 61.6809ZM22.3295 36.9021V27.2514C28.0429 24.7475 33.8087 22.1914 39.7318 19.5831C39.7318 22.8174 39.7318 25.9995 39.7318 29.1294C39.7318 29.3381 39.4697 29.5989 39.26 29.6511C35.6957 31.2161 32.1838 32.7289 28.6195 34.2417C26.7849 35.0242 24.9503 35.8067 23.1157 36.5891C22.8536 36.6935 22.6964 36.7978 22.3295 36.9021Z"
                              fill="white"/>
                    </svg>
                    <p class="mt-1">شرکت</p>
                    <input name="inputSelect" class="inputSelect hidden" type="radio" value="company">
                </button>
                <button class="bg-[#4B5259] md:w-full mx-auto rounded-lg flex flex-col justify-center items-center px-5 py-2 text-white hover:bg-[#E3000F] focus:bg-[#E3000F] cursor-pointer w-[150px] select_type">
                    <svg width="84" height="79" viewBox="0 0 84 79" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M41.851 78.9667C30.9637 78.9667 20.0765 78.9008 9.15594 78.9996C6.02628 79.0325 4.06191 77.1226 4.0952 73.9613C4.19509 61.6124 4.1285 49.2307 4.1285 36.8818C4.1285 36.355 3.89544 35.7293 3.59579 35.3341C1.36507 32.4033 0.0665887 29.1762 0 25.5209C0 24.7635 0.099883 23.9732 0.299649 23.2158C2.26402 16.6298 4.29497 10.0437 6.29263 3.4906C7.02511 1.05377 8.39017 0 10.9538 0C31.6296 0 52.3054 0 72.9812 0C75.5449 0 76.9099 1.05377 77.6424 3.4906C79.6401 10.0766 81.6377 16.6298 83.6354 23.2158C84.3013 25.4551 84.0016 27.6943 83.1693 29.8018C82.4368 31.7118 81.2715 33.4571 80.3059 35.3012C80.0396 35.7951 79.7732 36.355 79.7732 36.8818C79.74 49.2965 79.7399 61.7441 79.7399 74.1588C79.7399 77.3201 78.1085 78.9667 74.9123 78.9667C63.9251 78.9667 52.9047 78.9667 41.851 78.9667ZM12.6186 70.6024C32.1956 70.6024 51.7394 70.6024 71.2832 70.6024C71.2832 60.7562 71.2832 50.943 71.2832 41.0969C64.7242 42.447 59.164 40.7347 54.5361 35.9598C51.1068 39.615 46.9117 41.525 41.9509 41.5579C36.9567 41.5579 32.7616 39.5821 29.3656 35.9927C24.7044 40.8005 19.111 42.447 12.6186 41.1298C12.6186 50.976 12.6186 60.7562 12.6186 70.6024Z"
                              fill="white"/>
                    </svg>
                    <p class="mt-1">فروشگاه</p>
                    <input name="inputSelect" class="inputSelect hidden" type="radio" value="store">
                </button>
            </div>
            <div class="w-full mx-auto mt-14">
                <button id="select_button" data-panel-ui="<?php echo home_url( '/my-account' ) ?>"
                        class="bg-[#4B5259] p-4 rounded-lg w-full text-white inline-block text-center">تایید و
                    ادامه
                </button>
            </div>
        </div>
    </div>
    <div id="ForgetPasswordVerify"  class="flex md:justify-center justify-start items-center w-full h-full">
        <div class="flex flex-col justify-center text-center m-auto md:w-2/3">
            <p class="text-[24px] font-[700] text-[#525252] mt-1">رمز پیامکی را وارد کنید</p>
            <div class="flex justify-center">
                <p class="text-[18px] font-[500] text-[#525252] mt-2">رمز پیامکی 4 رقمی به شماره <span
                            class="user_phone_number"></span> ارسال شد.</p>
            </div>
            <button class="text-[#0058BF] mt-10 mb-3 backToForgetPass" type="button" aria-expanded="false">
                اصلاح شماره موبایل
            </button>
            <div class="grid grid-cols-4 gap-5  w-[260px] h-[50px] mx-auto mt-5" dir="ltr">
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" name="get_forger_message_1" id="get_forger_message_1"
                       class="get_message rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"/>
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" name="get_forger_message_2" id="get_forger_message_2"
                       class="get_message rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                       disabled="disabled"/>
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" name="get_forger_message_3" id="get_forger_message_3"
                       class="get_message rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                       disabled="disabled"/>
                <input type="number" inputmode="numeric" pattern="[0-9]*" maxlength="1" max="1" name="get_forger_message_4" id="get_forger_message_4"
                       class="get_message  rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                       disabled="disabled"/>
            </div>
            <div id="resend_sms_timer" class="flex text-[#525252] text-[14px] mt-5 mx-auto">
                <div class="timer_holder">
                    <span class="inline-block ml-1"></span>
                    <p class="inline-block">ثانیه تا درخواست مجدد رمز پیامکی</p>
                </div>
            </div>
            <button class="text-[#0058BF] mt-5 mb-3 resendForgetPassSms text-[14px] font-[700] hidden"
                    type="button"
                    aria-expanded="false">
                ارسال مجدد رمز پیامکی
            </button>
            <button id="AfterSuccessVerifyForgetPassSmsButton"
                    class="bg-[#4B5259] p-4 rounded-lg w-full text-white mt-5"
                    disabled="disabled">
                تایید و ادامه
            </button>
        </div>
    </div>
    <div id="AfterSuccessVerifyForgetPassSms"  class="flex md:justify-center justify-start items-center w-full h-full">
        <div class="flex flex-col justify-center text-center m-auto md:w-2/3">
            <p class="text-[24px] font-[700] text-[#525252] mt-1">رمز ثابت جدید انتخاب کنید</p>
            <p class="text-[18px] font-[500] text-[#525252] mt-2">انتخاب رمز ثابت جهت امکان ورود با رمز ثابت (به جای رمز پیامکی)</p>
            <div class="relative mt-5 w-fit mx-auto">
                <input type="password" name="ForgetPassOne" id="ForgetPassOne" placeholder="رمز ثابت جدید"
                       class=" ForgetPass rounded-lg  border-[1px] text-start text-[15px] font-[500] py-2 px-8  focus:border-none border-black"/>
                <span class="absolute right-1 top-2 cursor-pointer showPassword"><svg width="24" height="24"
                                                                                      viewBox="0 0 24 24" fill="none"
                                                                                      xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12Z"
                          stroke="#4B5259" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12C3.6 7.903 7.336 5 12 5C16.664 5 20.4 7.903 22 12C20.4 16.097 16.664 19 12 19C7.336 19 3.6 16.097 2 12Z"
                          stroke="#4B5259" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <div class="relative mt-5 w-fit mx-auto">
                <input type="password" name="ForgetPassTow" id="ForgetPassTow" placeholder="ورود مجدد رمز ثابت جدید"
                       class=" ForgetPass rounded-lg  border-[1px] text-start text-[15px] font-[500] py-2 px-8 focus:border-none border-black "/>
                <span class="absolute right-1 top-2 cursor-pointer showPassword"><svg width="24" height="24"
                                                                                      viewBox="0 0 24 24" fill="none"
                                                                                      xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12Z"
                          stroke="#4B5259" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12C3.6 7.903 7.336 5 12 5C16.664 5 20.4 7.903 22 12C20.4 16.097 16.664 19 12 19C7.336 19 3.6 16.097 2 12Z"
                          stroke="#4B5259" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>

                <p class="mt-2" id="match_pass"></p>
                <button id="ResetStatickPassword"
                        class="bg-[#4B5259] p-4 rounded-lg w-full text-white mt-10"
                        disabled="disabled">
                    تایید و ادامه
                </button>

            </div>
        </div>
    </div>
</div>