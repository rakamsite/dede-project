<?php
add_action( 'wp_ajax_Login_register_ajax', 'Login_register_ajax_callback' );
add_action( 'wp_ajax_nopriv_Login_register_ajax', 'Login_register_ajax_callback' );
function Login_register_ajax_callback(): void
{
	if ( $_SERVER['REQUEST_METHOD'] === "POST" ) {
		$send_sms    = new PW\PWSMS\Helper();
		$expiry_time = time() + ( 2 * 60 );
        $service_sms_enabled = cmb2_get_option("sms_settings","enable_sms_service");
        function addLeadingZeroIfNeeded( $number ) {
			if ( strlen( $number ) > 0 && !str_starts_with($number, '0')) {
				$number = '0' . $number;
			}
			return $number;
		}

		function generate_four_digit_number(): int
        {
            return mt_rand( 1000, 9999 );
		}
        function hash_password_argon2($password): string
        {
            return password_hash($password, PASSWORD_ARGON2I);
        }
		if ( isset($_POST['register_sms']) ) {
			$phone_number  = intval( $_POST['phone_register'] );
			$phone_number2 = addLeadingZeroIfNeeded( $phone_number );
			$temp_pass       = generate_four_digit_number();
			$password        = $_POST['password_register'];
            if (!$service_sms_enabled){
                $message_template = cmb2_get_option('sms_settings' , 'register_sms_message_template');
                $message_place = [
                    '#' => $temp_pass,
                ];
                $message = str_replace(array_keys($message_place), array_values($message_place), $message_template);
            }else{
                $message_template_code = cmb2_get_option('sms_settings' , 'register_sms_template_code');
                $message =str_replace(["*","#"],[$message_template_code , $temp_pass], "@*@#");
            }
			$data['message'] = $message;
			$data['mobile']  = $phone_number;
			if ( is_numeric( $_POST['phone_register'] ) ) {
				setcookie( "register_User_username", $phone_number2, $expiry_time );
				setcookie( "register_User_password", $password, $expiry_time );
				if ( ! username_exists( $phone_number2 ) ) {
					if ( $send_sms->send_sms( $data ) === true ) {
						setcookie( 'TempPassSend',hash_password_argon2($temp_pass) , $expiry_time );
						wp_send_json_success( "پیامکی حاوی رمز پیامکی برای شما ارسال شد." );
					}else{
                        wp_send_json_error('پیامک ارسال نشد');
                    }
				} else {
					wp_send_json_error( "شما قبلا با این شماره ثبت نام کرده اید ." );
				}
			} else {
				wp_send_json_error( "فرمت شماره تماس اشتباه است" );
			}
		}
		if ( isset($_POST['verifySmsPass']) ) {
			$Temp_sms = $_COOKIE['TempPassSend'];
			if ( password_verify($_POST['SmSPassword'] , $Temp_sms) ) {
				$user_login = $_COOKIE['register_User_username'];
				// تبدیل 0 اول به 98
				$nicename = preg_replace('/^0/', '98', $user_login);
		
				$user_id = wp_insert_user( [
					'user_pass'  => $_COOKIE['register_User_password'],
					'user_login' => $user_login,
					'role'       => 'personal',
					'nickname' => $nickname
				] );
				if ( is_wp_error( $user_id ) ) {
					wp_send_json_error( $user_id->get_error_message() );
				} else {
					setcookie( "User_ID_dede", $user_id, $expiry_time );
					wp_send_json_success( "لطفا در مرحله بعد نوع حساب کاربری خود را انتخاب کنید." );
				}
				// این خط اضافه است و هیچ وقت اجرا نمی‌شود
				// wp_send_json_success( 'true' );
			} else {
				wp_send_json_error( "رمز وارد شده اشتباه میباشد." );
			}
		}	
		if ( isset($_POST['updateUserRol']) ) {
            $user_type =  $_POST["select_type"];
            $acceptedUserType = ["personal","company","store"];
            if (!in_array($user_type, $acceptedUserType)) {
                wp_send_json_error( "مشکل در انتخاب نوع حساب کاربری ." );
            }
			$user_id_updated = wp_update_user( [
				"ID"   => $_COOKIE['User_ID_dede'],
				"role" => $user_type
			] );
			if ( is_wp_error( $user_id_updated ) ) {
				wp_send_json_error( "مشکل در انتخاب نوع حساب کاربری ." );
			} else {
                add_user_meta($user_id_updated , 'customer_type',$user_type );
                add_user_meta($user_id_updated , 'custom_phone_number',$user_type );
				$login = wp_signon( [
					'user_login'    => $_COOKIE['register_User_username'],
					'user_password' => $_COOKIE['register_User_password'],
					'remember'      => true
				] );
				if ( is_wp_error( $login ) ) {
					wp_send_json_error( $login->get_error_message() );
				}
				wp_send_json_success("با موفقیت وارد شدید :)");
			}
		}
		if ( isset($_POST['LoginWithPassWord']) ) {
			$userNameCheck = addLeadingZeroIfNeeded( $_POST['loginUsername'] );
			if ( ! username_exists( $userNameCheck ) ) {
				wp_send_json_error( 'لطفا برای ورود به سایت ابتدا ثبت نام کنید.' );
			} else {
				setcookie( "LoginUsername", $userNameCheck ,  $expiry_time  , '/');
				wp_send_json_success( 'رمز ثابت خود را وارد کنید.' );
			}
		}
		if ( isset($_POST['LoginWithPassWordPassword']) ) {
			$login = wp_signon( [
				'user_login'    => $_COOKIE['LoginUsername'],
				'user_password' => $_POST['Password'],
				'remember'      => true
			] );
			if ( is_wp_error( $login ) ) {
				wp_send_json_error('رمز عبوری که برای این حساب کاربری وارد شده درست نیست.');
			} else {
				wp_send_json_success( 'با موفقیت وارد شدید .' );
			}
		}
		if ( isset($_POST['LoginWithSms']) ) {
			$userNameCheck = addLeadingZeroIfNeeded( $_POST['loginUsername'] );
			if ( empty( $userNameCheck ) ) {
				wp_send_json_error( 'شماره تماس خود را وارد کنید.' );
			}
			if ( ! username_exists( $userNameCheck ) ) {
				wp_send_json_error( 'لطفا برای ورود به سایت ابتدا ثبت نام کنید.' );
			} else {
				$temp_pass = generate_four_digit_number();
                if (!$service_sms_enabled){
                    $message_template = cmb2_get_option('sms_settings' , 'login_sms_message_template');
                    $message_place = [
                        '#' => $temp_pass,
                    ];
                    $message = str_replace(array_keys($message_place), array_values($message_place), $message_template);
                }else{
                    $message_template_code = cmb2_get_option('sms_settings' , 'login_sms_template_code');
                    $message =str_replace(["*","#"],[$message_template_code , $temp_pass], "@*@#");
                }
				$data['message'] = $message;
				$data['mobile']  = $_POST['loginUsername'];
                $sms = $send_sms->send_sms( $data );
				if ($sms  === true ) {
					setcookie( 'LoginWithSmsLoginUsername', $userNameCheck , $expiry_time );
					setcookie( 'LoginWithSmsTempPassword', hash_password_argon2($temp_pass), $expiry_time );
					wp_send_json_success( 'پیامکی حاوی رمز عبور موقت برای شما ارسال شد .' );
				}else{
                    wp_send_json_error( $sms);
                }
			}
		}
		if ( isset($_POST['verifyLoginSMS']) ) {
			$temp_pass           = $_POST['tempLoginPass'];
			$temp_pass_generated = $_COOKIE['LoginWithSmsTempPassword'];
			if ( password_verify($temp_pass, $temp_pass_generated) ) {
				$username = $_COOKIE['LoginWithSmsLoginUsername'];
				$user     = get_user_by( 'login', $username );
				if ( ! is_wp_error( $user ) ) {
					wp_clear_auth_cookie();
					wp_set_current_user( $user->ID );
					wp_set_auth_cookie( $user->ID );
					wp_send_json_success( 'با موفقیت وارد شدید :)' );
					exit();
				} else {
					wp_send_json_error( 'حساب کاربری شما دچار مشکل شده است . لطفا با مدیریت تماس بگیرید.' );
				}
			} else {
				wp_send_json_error( 'کد وارد شده صحیح نمیباشد .' );
			}
		}
		if ( isset($_POST['SendTempPassToEmail']) ) {
			$temp_pass = $_COOKIE['LoginWithSmsTempPassword'];
			$user      = get_user_by( 'login', $_COOKIE['LoginWithSmsLoginUsername'] );
			$to        = $user->user_email;
			$subject   = 'رمز عبور موفق پیامکی ';
			$message   = "طبق درخواست شما برای ورود به سایت \n کد ورود شما : $temp_pass  \n [DeDe.ir]";
			$send_mail = wp_mail( $to, $subject, $message );
			if ( is_wp_error( $send_mail ) ) {
				wp_send_json_error( $send_mail->get_error_message() );
			} else {
				wp_send_json_success( 'کد با موفقیت ارسال شد.' );
			}
		}
		if ( isset($_POST['verifyForgetPassSms']) ) {
			$phone_number    = addLeadingZeroIfNeeded( $_POST['Forget_phone_number'] );
			$temp_pass       = generate_four_digit_number();
            if (!$service_sms_enabled){
                $message_template = cmb2_get_option('sms_settings' , 'login_sms_message_template');
                $message_place = [
                    '#' => $temp_pass,
                ];
                $message = str_replace(array_keys($message_place), array_values($message_place), $message_template);
            }else{
                $message_template_code = cmb2_get_option('sms_settings' , 'forget_sms_message_template');
                $message =str_replace(["*","#"],[$message_template_code , $temp_pass], "@*@#");
            }
			$data['message'] = $message . "\n کد جهت بازیابی رمز عبور" . "\n[DeDe.ir]";
			$data['mobile']  = $_POST['Forget_phone_number'];
			if ( ! username_exists( $phone_number ) ) {
				wp_send_json_error( 'کاربری با این شماره وجود ندارد.' );
			} else {
				if ( $send_sms->send_sms( $data ) === true ) {
					setcookie( 'ForgetPassUsername', addLeadingZeroIfNeeded( $phone_number ), $expiry_time );
					setcookie( 'ForgetPassUsernameTempPassword', hash_password_argon2($temp_pass), $expiry_time );
					wp_send_json_success( 'پیامکی حاوی رمز عبور موقت برای شما ارسال شد .' );
				} else {
                    wp_send_json_error('پیامک ارسال نشد');
				}
			}
		}
		if (isset($_POST['verifySmsForgetPass'])){
			$forgetCode = $_POST['forget_pass_code'];
			$temp_pass = $_COOKIE['ForgetPassUsernameTempPassword'];
			if (password_verify($forgetCode , $temp_pass)){
				setcookie('verifiedSMSForgetPass' ,true, $expiry_time);
				wp_send_json_success('رمز جدید را وارد کنید.');
			}else{
				wp_send_json_error('رمز وارد شده صحیح نمیباشد .');
			}
		}
		if (isset($_POST['checkVerifySMS'])){
			if ($_COOKIE['verifiedSMSForgetPass']){
				wp_send_json_success('رمز جدید را وارد کنید.');
			}else{
				wp_send_json_error('لطفا صفحه رو رفرش کنید و مجدد اقدام به بازیابی رمز ثابت خود بفرمایید .');
			}
		}
		if (isset($_POST['ChangePass'])){
			$new_password = $_POST['NewPass'];
			$username= $_COOKIE['ForgetPassUsername'];
			$user = get_user_by('login', $username);
			if (username_exists($username)) {
				wp_set_password($new_password, $user->ID);
				wp_send_json_success('با موفقیت رمز عبور ثابت شما بروز شد.');
			}
			wp_send_json_error("مشکلی برای بروزرسانی رمز عبور شما وجود دارد.");
		}
		if (isset($_POST['PanelChangOrSelectPassword'])){
			$phone_number = addLeadingZeroIfNeeded($_POST['username']);
			$password = strip_tags($_POST['password']);
			$temp_pass         = generate_four_digit_number();
			$data['message'] = $temp_pass . "\nکد ثبت یا تغییر رمز ثابت شما در سایت سایت" . "\n[DeDe.ir]";
			$data['mobile']  = $phone_number;
			if ( $send_sms->send_sms( $data ) === true ) {
				setcookie("SubmitAndChangeStaticPasswordTempPass" , hash_password_argon2($temp_pass) , $expiry_time);
				setcookie("SubmitAndChangeStaticPassword" , $password , $expiry_time);
				setcookie("SubmitAndChangeStaticPasswordUsername" , $phone_number , $expiry_time);
				wp_send_json_success(['msg' => "پیامکی حاوی رمز عبور موفقت برای ثبت یا تغییر رمز عبور ثابت برای شما ارسال شد ."]);
			}else{
				wp_send_json_error("موفق به ارسال پیامک برای تغییر رمز نشدید .");
			}
		}
		if (isset($_POST['PanelChangOrSelectPasswordVerifySMS'])){
			$temp_pass = $_POST['SmSPassword'];
			$created_temp_password = $_COOKIE['SubmitAndChangeStaticPasswordTempPass'];
			$password = $_COOKIE['SubmitAndChangeStaticPassword'];
			if (password_verify($temp_pass , $created_temp_password)){
				wp_set_password($password ,get_current_user_id());
				wp_send_json_success(['msg' => "رمز جدید شما ثبت شد."]);
			}else{
				wp_send_json_error("رمز پیامکی وارد شده صحیح نمیباشد.");
			}
		}
	}
}

