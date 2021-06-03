<?php

namespace App\Http\Controllers;

use App\Counter;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\MobileInfo;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->setErrors([
                'All field are Required'
            ], 400);

            return $this->response();
        }

        try {
            $user=User::where('email',$request->email)->first();
            if($user){
                if($user->verified_at=== 1 && $user->user_role === 'isCustomer') {
                    if (!$token = JWTAuth::attempt($credentials)) {
                        $this->setErrors(['invalid_credentials']);
                        return $this->response();
                    }
                    $this->setData([
                        'user' => $user,
                        'token'=>$token
                    ], 200);
                }else{
                    $this->setErrors(['Access Denied,Please Verify Yourself Thanks!']);
                }
            }else{
                $this->setErrors(['Email is Invalid']);
            }


        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return $this->response();
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',

        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            $this->setErrors([
                $error
            ], 400);

            return $this->response();
        }
        $user=User::where('email',$request->email)->first();
        if(!$user) {
            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'verified_at' => 0,
                'status' => 'Pending',
            ]);
            $user->generateOtp();
            $token = JWTAuth::fromUser($user);
            $this->setMessage('Thank You!,Please wait for the admin approval');
        }else{
            $this->setErrors(['Email Already Exist Please Try another email']);
        }
        return $this->response();
    }
    public function updateMobileInfo(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'ud_id'=>'required',
            'device_token'=>'required',
            'device_type' => 'required',

        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            $this->setErrors([
                $error
            ], 400);

            return $this->response();
        }
        $user = User::where('email', $request->email)->first();
        if($user){
            $updateMobileInfo=MobileInfo::where('user_id',$user->id)
                ->where('status','active')
                ->first();
            if($updateMobileInfo){
                $updateMobileInfo->update([
                    'status'=>'inActive'
                ]);
                $updateMobileInfo->save();
                $mobileInfo=MobileInfo::create([
                    'user_id' => $user->id,
                    'ud_id'=> $request->ud_id ,
                    'device_token'=> $request->device_token,
                    'device_type' => $request->device_type,
                    'status' => 'active'
                ]);
                $this->setErrors(['Mobile Info Updated Successful']);
            }else{
                $mobileInfo=MobileInfo::create([
                    'user_id' => $user->id,
                    'ud_id'=> $request->ud_id ,
                    'device_token'=> $request->device_token,
                    'device_type' => $request->device_type,
                    'status' => 'active'
                ]);

                $this->setErrors(['Mobile Info Updated Successful']);

            }

        }else{
            $this->setErrors(['inValid Email']);
        }
        return $this->response();

    }
    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            $this->setErrors([
                $error
            ], 400);

            return $this->response();
        }
        $user=User::where('email',$request->email)->first();
        if($user){
            If($user->status==='Accepted') {
                $user->generateOtp();
                $this->setMessage('Thank You!,Please wait for the OTP');
                $user=User::where('email',$request->email)->first();
                $this->sendEmail($user->name,$user->email,$user->otp);
            }else{
                $this->setErrors(["Wait For Account Approval"]);
                return $this->response();
            }
        }
        else{
            $this->setErrors(['Email Does not Exist']);
            return $this->response();
        }
        $token = JWTAuth::fromUser($user);

        return $this->response();
    }
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|string|min:6',
            'otp_code'=>'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            $this->setErrors([
                $error
            ], 400);

            return $this->response();
        }
        $user=User::where('email',$request->email)->first();
        if($user){
            If($user->status=='Accepted') {
                if ($user->otp != $request->otp_code){
                    $this->setErrors(["invalid Otp"]);
                    return $this->response();
                }
                $user->update(['password' => Hash::make($request->get('password'))]);
                $this->setMessage('Thank You!,Password Updated Successfully ');
            }else{
                $this->setErrors(["Wait For Account Approval"]);
                return $this->response();
            }
        }
        else{
            $this->setErrors(['Email Does not Exist']);
            return $this->response();
        }
        $token = JWTAuth::fromUser($user);

        return $this->response();
    }
    public function counter(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'counter_up' => 'required',
            'counter_down' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            $this->setErrors([
                $error
            ], 400);

            return $this->response();
        }
        $user = Counter::create([
            'user_id' => $request->user_id,
            'counter_up' => $request->counter_up,
            'counter_down'=>$request->counter_down
        ]);
        $this->setMessage('Data Added Successfully');
        return $this->response();
    }
    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }
    public function verifiedCustomer(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'otp_code'=>'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            $this->setErrors([
                $error
            ], 400);

            return $this->response();
        }
        $user=User::where('email',$request->email)->first();
        if($user){
            If($user->status==='Accepted' && $user->otp === $request->otp_code) {
                $user->update(['verified_at' => 1,'user_role'=>'isCustomer']);
                $this->setMessage('Thank You!,Registed Successfully ');
            }else{
                $this->setErrors(["Wait For Account Approval"]);
                return $this->response();
            }
        }
        else{
            $this->setErrors(['Email Does not Exist']);
            return $this->response();
        }
        $token = JWTAuth::fromUser($user);

        return $this->response();
    }
}
