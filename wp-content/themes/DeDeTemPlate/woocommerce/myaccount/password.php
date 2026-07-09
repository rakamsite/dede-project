<div id="StaticPassword" class="text-[#525252] hidden">
    <div class="w-full flex flex-col gap-2">
        <h2 class="font-[700] text-[20px]">تغییر و انتخاب رمز ثابت جدید</h2>
        <p class="text-[18px] font-[500]">برای حساب کاربری خود رمز ثابت انتخاب کنید، تا مجددا نیازی به رمز پیامکی نداشته
            باشید.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-10">
        <form id="ResetStaticPassword" class="w-full">
            <input type="hidden" name="action" value="Login_register_ajax">
            <input type="hidden" name="PanelChangOrSelectPassword" value="true">
            <input name="username" value="<?php echo wp_get_current_user()->user_login; ?>" type="hidden">
            <div class="flex flex-col gap-5 w-full text-[#4B5259]">
                <div class="relative w-full">
                    <input type="password" id="password" name="password" required
                           class="p-2 pr-10 border-[1px] border-[#4B5259] rounded-lg w-full md:w-1/2"
                           placeholder="رمز ثابت جدید"/>
                    <svg class="absolute top-1/2 right-2 transform -translate-y-1/2 cursor-pointer show_pass" width="24"
                         height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12Z"
                              stroke="#4B5259" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12C3.6 7.903 7.336 5 12 5C16.664 5 20.4 7.903 22 12C20.4 16.097 16.664 19 12 19C7.336 19 3.6 16.097 2 12Z"
                              stroke="#4B5259" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="relative w-full">
                    <input type="password" id="ReInterPassword" name="ReInterPassword" required
                           class="p-2 pr-10 border-[1px] border-[#4B5259] rounded-lg w-full  md:w-1/2"
                           placeholder="ورود مجدد رمز ثابت جدید"/>
                    <svg class="absolute top-1/2 right-2 transform -translate-y-1/2 cursor-pointer show_pass" width="24"
                         height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12Z"
                              stroke="#4B5259" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12C3.6 7.903 7.336 5 12 5C16.664 5 20.4 7.903 22 12C20.4 16.097 16.664 19 12 19C7.336 19 3.6 16.097 2 12Z"
                              stroke="#4B5259" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <button id="submit_new_pass" class="p-4 text-white text-center w-full md:w-1/2 rounded-lg text-[18px] font-[500] bg bg-[#4B5259] hover:bg-[#2F2483]">
                    تایید رمز ثابت جدید
                </button>
            </div>
        </form>
        <div id="submit_sms_code" class="grid grid-cols-4 content-center md:space-x-10 space-x-reverse w-full md:w-3/4 mx-auto hidden" dir="ltr">
            <div class="text-[#4B5259] col-span-4 mb-5">
                <p class="w-full text-center">
                    رمز پیامکی به شماره همراه شما ارسال شد. جهت نهایی سازی رمز ثابت جدید، رمز پیامکی را در قسمت زیر وارد کنید
                </p>
            </div>
            <input type="text" maxlength="1" name="get_message_1" id="pass_message_1"
                   class="get_message place-self-center w-1/2 p-2 rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"/>
            <input type="text" maxlength="1" name="get_message_2" id="pass_message_2"
                   class="get_message place-self-center w-1/2 p-2 rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                   disabled="disabled"/>
            <input type="text" maxlength="1" name="get_message_3" id="pass_message_3"
                   class="get_message place-self-center w-1/2 p-2 rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                   disabled="disabled"/>
            <input type="text" maxlength="1" name="get_message_4" id="pass_message_4"
                   class="get_message place-self-center w-1/2 p-2 rounded-lg  border-[1px] text-center text-[24px] focus:border-none border-black"
                   disabled="disabled"/>
            <button id="submit_final_static_pass" class="col-span-4 rounded-lg p-4 bg-[#4B5259] text-white my-5 " disabled>ثبت نهایی رمز ثابت جدید</button>
        </div>

    </div>
</div>