<?php

namespace App\Http\Controllers;
use App\User;
use App\UserSocialite;
use App\UserToken;
use App\UserType;
use Carbon\Carbon;
//use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    protected $token;
    protected $permission;
    protected $permissionMessage;
    protected $orderRelations = [];
    protected $detailRelation = [];

    public function __construct()
    {
        $this->token = new TokenController();
        $this->orderRelations = [
            'user',
            'orderType',
            'orderBookmeDetail',
            'orderPaymentDetail',
            'orderCustomerDetail',
            'orderDetail',
            'orderDetail.departSectorName',
            'orderDetail.arivalSectorName'
        ];
        $this->detailRelation = ['orderType', 'orderDetail.departSectorName', 'orderDetail.arivalSectorName'];

        $this->permission = "OFF-SERVICE";
        $this->permissionMessage = 'Service is temporarily stopped due to Maintenance';
    }

    public function createUser(Request $request)
    {
        if (!$this->validateReq($request->all(), User::REGISTER_RULES)) {
            return $this->response();
        }
        try {
            DB::beginTransaction();
            $user = User::where('phone', $this->convertNumber($request->get('phone')))->first();


            if (empty($user)) {
                $user = new User();
            }
            $phone = $this->convertNumber($request->get('phone'));
            $user->name = !empty($request->user_name) ? $request->user_name : $phone;
            Log::info('Phone Number after CONVERSION');
            Log::info($phone);
            $user->email = empty($request->get('email')) ? $this->convertNumber($request->get('phone')) . '@mytm.com.pk' : $request->get('email');
            $user->user_type = $request->get('user_type_id');

//	        $password = $this->generateRandomString(10);
//	        $user->password = Hash::make($password);
            $otpPasword = $this->generateVerificationNumber(4);
            $testPhoneNums = explode(',', env('TEST_PHONE_NUMBERS', ''));
            Log::channel('testing')->info($testPhoneNums);
            Log::channel('testing')->info('req number: ' . $phone);

            if (in_array($phone, $testPhoneNums)
//	        	$request->get('phone') == '00923465758568'
//	            || $request->get('phone') == '00923365758668'
//	            || $request->get('phone') == '00923234567890'
//	            || $request->get('phone') == '00923123456789'
            ) {
                $otpPasword = 1234;
                Log::channel('testing')->info('test number: ' . $phone);
            } else {
                Log::channel('testing')->info('not a test number: ' . $phone);
            }

            $user->password = Hash::make($otpPasword);
            $user->confirmed_otp = $request->get('user_type_id') == 2 ? '1234' : $otpPasword;
            $user->phone = $this->convertNumber($request->get('phone'));
            $user->save();
            /*---------------Expire previous token--------------------*/
            UserToken::where('device_type', $request->get('device_type'))
                ->where('user_id', $user->id)
                ->update(array('status' => 'INACTIVE'));
            /*---------------Generate token--------------------*/

            $this->token->gernerate($user, $request);


            /*---------------Social User--------------------*/
            $social = $this->createSocialiteUser($user);


            $this->setData([
//                'user' => $user,
                'verfication' => $user->confirmed_otp,
//                'userpassword' => $password
            ]);

            if (!$request->has('corporate') && $request->get('user_type_id') != 3) {
                if ($social) {
                    Log::info("send Sms");
                    Log::info($user->phone);
//	             	dd($user->phone, "repeat: verification number for mytm: " . $user->confirmed_otp);
                    $sendsmsrespons = $this->sendSMS($user->phone, "verification number for mytm: " . $user->confirmed_otp);
                    Log::info("send Sms response");
                    Log::info($sendsmsrespons);
                    $this->setMessage('verification sms sent with number ' . $user->confirmed_otp);
                }
            }


            DB::commit();

            return $this->response();
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            $this->setErrors([$e->getMessage()]);

            return $this->response();
        }


    }

    protected function createSocialiteUser($user)
    {
        $flag = false;

        try {
            DB::connection('mysql_socialite');
            DB::beginTransaction();
            $timeline = TimelineSocialite::where('username', $user->phone)->first();
            if (empty($timeline)) {
                $timeline = new TimelineSocialite();
                $timeline->name = $user->name;
            }

            $timeline->username = $user->phone;
            $timeline->about = 'Hey there! I am using MyTM .';
            $timeline->type = 'user';
            $timeline->hide_cover = 0;
            $timeline->save();


            $socialuser = UserSocialite::where('timeline_id', $timeline->id)->first();
            if (empty($socialuser)) {
                $socialuser = new UserSocialite();
            }
            $socialuser->email = $user->email;
            $socialuser->password = $user->password;
            $socialuser->timeline_id = $timeline->id;
            $socialuser->verification_code = str_random(18);
            $socialuser->verified = 1;
            $socialuser->email_verified = 1;
            $socialuser->remember_token = str_random(10);
            $socialuser->balance = 0.0;
            $socialuser->birthday = '1970-01-01';
            $socialuser->gender = 'Male';
            $socialuser->active = 1;
            $socialuser->save();


            $user_settings = [
                'user_id' => $socialuser->id,
                'confirm_follow' => 'no',
                'follow_privacy' => 'everyone',
                'comment_privacy' => 'everyone',
                'timeline_post_privacy' => 'everyone',
                'message_privacy' => 'everyone',
                'post_privacy' => 'everyone',
            ];
            $userSettings = DB::connection('mysql_socialite')->table('user_settings')->insert($user_settings);
            DB::commit();

            return $flag = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setErrors([$e->getMessage()]);

            return $flag;
        }


    }

    public function verificationCode(Request $request)
    {
        $this->setRequestData($request->toArray());
        if (!$this->validateReq($request->all(), User::CODE_VERIFICATION_RULES)) {
            return $this->response();
        }
        $phone = $this->convertNumber($request->get('phone'));

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            $this->setErrors(['Invalid phone number']);
            return $this->response();
        }


        if ($request->get('verification_code') != $user->confirmed_otp) {
            $this->setErrors(['Incorrect code']);
            return $this->response();
        }

        //Update verified code as flexisip password
        $flexisipAccount = Account::where('login', $phone)->first();
        if (!$flexisipAccount) {
            $flexisipAccount = new Account();
            $flexisipAccount->login = $phone;
        }

        $flexisipAccount->password = $user->confirmed_otp;
        $flexisipAccount->save();

        $userToken = UserToken::where('device_type', $request->get('device_type'))
            ->where('user_id', $user->id)->where('status', 'ACTIVE')->first();

        $userToken->device_token = $request->get('device_token');
        $userToken->save();
        $user->activate = 1;
        $user->save();
        $pd = $this->collectPoints($userToken->token);

        if ($request->user_type_id == 3) {
            return
                ['user' => $user,
                    'token' => $userToken->token
                ];

        } else {
            $this->setData([
                'user' => $user,
                'token' => $userToken->token
            ]);
        }


        return $this->response();

    }

    public function resendVerificationCode(Request $request)
    {

        if (!$this->validateReq($request->all(), User::RESEND_CODE_VERIFICATION_RULES)) {
            return $this->response();
        }

        $user = User::where('phone', $this->convertNumber($request->get('phone')))->first();
        Log::info("Phone Number:");
        Log::info($this->convertNumber($request->get('phone')));
        Log::info("user after resend code");
        Log::info($user);
        //$user->confirmed_otp = $this->generateVerificationNumber(4);
        //$user->save();
        if ($user) {
            $this->sendSMS($user->phone, "repeat: verification number for mytm: " . $user->confirmed_otp);
            $this->setMessage('verification sms sent with number ' . $user->confirmed_otp);

            $this->setData([
//            'user' => $user,
                'verfication' => $user->confirmed_otp
            ]);
        } else {
            $this->setErrors(['User Not Found']);
        }

        return $this->response();

    }

    public function loginold(Request $request)
    {
        if (!$this->validateReq($request->all(), User::LOGIN_RULES)) {
            return $this->response();
        }
        /*-------------------------validate token--------------------*/
        if (!$this->token->validateToken($request)) {
            return $this->response();
        }

        return $this->response();
    }

    public function login(Request $request)
    {
        if (!$this->validateReq($request->all(), User::LOGIN_RULES)) {
            return $this->response();
        }
        $phone = $this->convertNumber($request->get('phone'));
        $email = $phone . '@mytm.com.pk';
        $user = User::where('email', $email)->first();
        if ($user) {

            if (Hash::check($request->password, $user->password)) {

                $privoiceToken = UserToken::where('user_id', $user->id)->get();
                foreach ($privoiceToken as $in_active) {
                    $in_active->status = 'INACTIVE';
                    $in_active->save();

                }
                $token = $this->token->gernerate($user, $request);
                $this->setMessage('successfull Authenticated');
                $this->setData(['token' => $token]);

                return $this->response();
            } else {
                $this->setErrors(['Phone Or Password are incorrect']);

                return $this->response();
            }
        } else {
            $this->setErrors(['User Does Not Exist with This Creds']);

            return $this->response();
        }


        /*-------------------------validate token--------------------*/

//		return $token;
    }

    public function tokenValidation(Request $request)
    {

        if (!$this->validateReq($request->all(), User::TOKEN_VALIDATION)) {
            return $this->response();
        }

        $userToken = UserToken::where('token', $request->token)->first();

        if ($userToken) {
            if ($userToken->status == 'INACTIVE') {

                $respons = ['success' => false, 'errors' => ['Token is Invalid'], 'data' => null, 'status' => 498];

                return response()->json($respons);

            } elseif ($userToken->status == 'EXPIRE') {
                $user = User::find($userToken->user_id);
                $request->request->add(['udid' => $userToken->udid]);
                $token = new TokenController();
                $refreshToken = $token->gernerate($user, $request);

//
                $respons = [
                    'success' => false,
                    'errors' => ['Token is Expired'],
                    'data' => null,
                    'status' => 419,
                    'token' => $refreshToken
                ];

                return response()->json($respons);
            } else {

                $refreshToken = UserToken::with('user')->where('user_id', $userToken->user_id)
                    ->where('status', 'ACTIVE')
                    ->first();

                $respons = [
                    'success' => true,
                    'errors' => null,
                    'data' => [],
                    'status' => 200,
                    'token' => $refreshToken
                ];

                return response()->json($respons);
            }

        } else {

            $respons = ['success' => false, 'errors' => ['Token is Invalid'], 'data' => null, 'status' => 498];

            return response()->json($respons);
        }


    }


    public function sendPushNotification(Request $request)
    {
        $order = Order::where('id', $request->order_id)->first();
        $this->sendPush($order);
    }

    public function sendPushNotificationPg(Request $request)
    {


        $token = $this->token->validateToken($request);

        if ($token['success'] == true) {
            $deivceTokenObj = UserToken::where('token', $request->header('token'))->where('status', 'ACTIVE')->first();

            if ($deivceTokenObj) {

                $deviceToken = $deivceTokenObj->device_token;
//		    dd($divceToekn);
                $type = $deivceTokenObj->device_type;
                switch ($type) {
                    case 'ANDROID' :
                        $push = new PushNotification('fcm');
                        $complete_message = [
                            'message' => 'YOUR APP REQUEST HAS BEEN GENERATED SUCCESSFULLY',
                            'title' => 'APP REQUEST',
                            'body' => 'APP REQUEST GENERATED SUCCESSFULL',
//				    'sound' => $this->sound,
//						     'custom' => [],
                            'type' => 'app_request'
                        ];


                        break;
                    case 'IOS' :
//
                        $push = new PushNotification('apn');
//
                        $push->setConfig([
                            'certificate' => __DIR__ . '/../../../config/iosCertificates/' . $this::PRODUCTION_PEM
                        ]);

                        $complete_message = [
                            'aps' => [
                                'alert' => [
                                    'title' => 'APP REQUEST',
                                    'body' => 'YOUR APP REQUEST HAS BEEN GENERATED SUCCESSFULLY'
                                ],
//					    'sound' => $this->sound,
//							     'custom' => [],
                                'type' => 'app_request'
                            ]
                        ];
                        break;
                    default :
                        $push = new PushNotification('fcm');
                        $complete_message = [
                            'message' => 'YOUR APP REQUEST HAS BEEN GENERATED SUCCESSFULLY',
                            'title' => 'APP REQUEST',
                            'body' => 'APP REQUEST GENERATED SUCCESSFULL',
//				    'sound' => $this->sound,
//						     'custom' => [],
                            'type' => 'app_request'
                        ];
                }

                $res = $push->setMessage($complete_message)
                    ->setDevicesToken([$deviceToken])
                    ->send()
                    ->getFeedback();
                $this->setMessage(['push sent Successfully']);
                $this->setData(['device_token ' => $deviceToken]);

                return $this->response();


            }
        } else {
            return $token;
        }


    }

    public function generateOTP(Request $request)
    {

        $phone = $this->convertNumber($request->get('phone'));
        $verificationCode = $this->generateVerificationNumber(4);
        $this->sendSMS($phone, "Verification Code: verification number for corporate: " . $verificationCode);
        $this->setMessage('verification sms sent');
        $this->setData(['verification_code' => $verificationCode]);

        return $this->response();
    }

    //	-------------BOOKING LIST----


    /*
     *  Api: Get mobile token and return web active token or create new token for web
     */


    public function refreshToken(Request $request)
    {

        if (!$this->validateReq($request->all(), User::REFRESH_TOKEN_VALIDATION)) {
            return $this->response();
        }


        try {

            $userToken = UserToken::where('token', $request->get('token'))->first();

            if ($userToken) {
                if ($userToken->status === 'INACTIVE' || $userToken->status === 'EXPIRE') {

                    $getActiveToken = UserToken::where('user_id', $userToken->user_id)
                        ->where('device_type', $request->device_type)
                        ->where('status', 'ACTIVE')
                        ->first();

                    if ($getActiveToken) {
                        $token = $getActiveToken->token;
                    } else {
                        $user = User::find($userToken->user_id);
                        $request->request->add(['udid' => $userToken->udid]);
                        $tokenController = new TokenController();
                        $token = $tokenController->gernerate($user, $request);

                    }

                } elseif ($userToken->status == 'ACTIVE') {
                    $token = $request->token;
                }

                return [
                    'success' => true,
//                        'errors' => ['Token is Invalid'],
                    'data' => null,
                    'status' => 498,
                    'token' => $token
                ];
            } else {
                return ['success' => false, 'errors' => ['Token not Found h'], 'data' => null, 'status' => 498];
            }


        } catch (\Exception $e) {
            $this->setErrors([$e->getMessage()]);
            Log::error($e->getMessage() . ' => in ' . $e->getFile() . ': ' . $e->getLine());
            return $this->response();
        }


    }

    public function getUserFromTokenWeb(Request $request)
    {

        if (!$request->has('token')) {
            $this->setErrors(['token is required']);

            return $this->response();
        }

        $userToken = UserToken::where('token', $request->token)->first();
        if ($userToken) {
            if ($userToken->status == 'INACTIVE') {
                $this->setErrors(['Token is invild']);
                return $this->response();
            }
            $this->setData(['userId' => $userToken->user_id]);
            return $this->response();
        } else {
            $this->setErrors(['token is miss Match']);
            return $this->response();
        }
    }


    public function tokenByPhoneExtended(Request $request)
    {

        if (!$request->phone) {
            $this->setErrors(['phone number is Required']);

            return $this->response();
        }

        $user = User::where('phone', $this->convertNumber($request->get('phone')))->first();
        if ($user) {
            $userToken = UserToken::where('user_id', $user->id)
                ->where('status', 'ACTIVE')->first();
            $this->setMessage('success');
            $this->setData(['userToken' => $userToken]);

            return $this->response();

        } else {
            $this->setErrors(['not found']);

            return $this->response();
        }

    }


    public function sendMail(Request $request)
    {

        if (!$request->has('email')) {
            $this->setErrors(['email Required']);
            return $this->response();
        }
        $data = [
            'message' => "Test Message",
            'address' => 'info@mytm.com',
            'subject' => 'MYTM!',
            'name' => 'MYtm',
        ];
        Mail::to($request->email)->send(new SendMail($data));
        $this->setMessage('Email sent Succesfully');
        return $this->response();
    }


    public function createCorporateUser(Request $request)
    {

        if (!$this->validateReq($request->all(), User::REGISTER_RULES)) {
            return $this->response();
        }
        try {
            DB::beginTransaction();
            $user = User::where('phone', $this->convertNumber($request->get('phone')))->first();


            if (empty($user)) {
                $user = new User();
            }

            $user->name = $phone = $this->convertNumber($request->get('phone'));
            $user->email = empty($request->get('email')) ? $this->convertNumber($request->get('phone')) . '@mytm.com.pk' : $request->get('email');
            $user->user_type = $request->get('user_type_id');

//	        $password = $this->generateRandomString(10);
//	        $user->password = Hash::make($password);
            $otpPasword = $this->generateVerificationNumber(4);
            $testPhoneNums = explode(',', env('TEST_PHONE_NUMBERS', ''));
            Log::channel('testing')->info($testPhoneNums);
            Log::channel('testing')->info('req number: ' . $phone);

            $user->password = Hash::make($otpPasword);
            $user->confirmed_otp = $request->get('user_type_id') == 2 ? '1234' : $otpPasword;
            $user->phone = $this->convertNumber($request->get('phone'));
            $user->save();
            /*---------------Expire previous token--------------------*/
            UserToken::where('device_type', $request->get('device_type'))
                ->where('user_id', $user->id)
                ->update(array('status' => 'INACTIVE'));
            /*---------------Generate token--------------------*/

            $this->token->gernerate($user, $request);
//			$request->request->add(['variable' => 'value']);
            $request->request->add([
                'phone' => $phone,
                'device_token' => base64_encode($phone),
                'verification_code' => $user->confirmed_otp,
                'device_type' => 'WEB',
            ]);


            $respose = $this->verificationCode($request);

            $this->setData($respose);
            DB::commit();

            return $this->response();
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            $this->setErrors([$e->getMessage()]);

            return $this->response();
        }


    }
}
