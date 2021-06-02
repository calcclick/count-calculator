<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $data = [];

    protected $errors = [];

    protected $message = null;
    protected $statusCode = null;
    protected $isDebugging = false;

    private $debugInfo = [];

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors, $code = null)
    {
        $this->errors = $errors;
        $this->statusCode = $code;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setData($data, $code = null)
    {
        $this->data = $data;

        $this->statusCode = $code;

    }
    protected function getUser(){

        $user = JWTAuth::parseToken()->toUser();
        return $user;
    }
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    protected function response()
    {
        $resp = [
            'success' => false,
            'errors' => [],
            'data' => null,
        ];


        if (count($this->errors) === 0) {
            $resp['success'] = true;
            $resp['message'] = $this->message;
            $resp['data'] = $this->data;

        } else {
            $resp['success'] = false;

            if (is_array($this->errors)) {
                foreach ($this->errors as $error) {
                    if (is_array($error)) {
                        foreach ($error as $err) {
                            $resp['errors'][] = $err;
                        }
                    } else {
                        $resp['errors'][] = $error;
                    }
                }
            } else {
                $resp['errors'] = [$this->errors];
            }

        }

//        $resp['debugbar'] = app('debugbar')->getData();
        return response()->json($resp, $this->statusCode ? $this->statusCode : 200);
    }

    protected function checkUserLogin()
    {
        try {
            $user = JWTAuth::parseToken()->toUser();
            return $user;
        } catch (\Exception $e) {
            $this->setErrors(['Invalid credentials'], 401);
            return false;
        }
    }
     function sendEmail($name=null,$email=null,$otp=null){

        $to_name = $name;
        $to_email = $email;
        $data = array('name'=>"Team Count Calculator", 'body' => $otp);

        Mail::send('emails.mail', $data, function($message) use ($to_name, $to_email) {

            $message->to($to_email, $to_name)

                ->subject('OTP');

            $message->from(env('MAIL_USERNAME','ranaumair455@gmail.com'),'OTP');

        });
    }
    protected function getRequestedQuery(Request $request){
//        $qry['ambassador_id'] = $request->get('ambassador') ? $request->get('ambassador') : '';
        $qry['date'] = $request->get('date') ? $request->get('date') : '';
//Used atm
        $qry['customer_name'] = $request->get('customer_name') ? : '';
        $qry['email'] = $request->get('email') ? $request->get('email') : '';
        $qry['from'] = $request->get('from') ? : '';
        $qry['to'] = $request->get('to') ? : '';
        $qry['month'] = $request->get('month') ? $request->get('month') : '';

        return $qry;
    }

    protected function getFilters($qry){
        if($qry['city_id']){
            $cities = City::all();
        }
        else{
            $cities = City::all();
        }

        if($qry['store_id']){
            $stores = Store::all();
        }else{
            $stores = Store::all();
        }

        if($qry['brand_id']){
            $brands = Brand::all();
        }else{
            $brands = Brand::all();
        }

        if($qry['product_id']){
            $products = Product::all();
        }else{
            $products = Product::all();
        }

        $filters = [
            'cities' => $cities,
            'stores' => $stores,
            'brands' => $brands,
            'products' => $products,
        ];

        return $filters;
    }
    function sendPush($message=null,$title=null,$sound="default",$type,$id=null){

        $complete_message = [
            'message' => $message,
            'title'   => $title,
            'body'    => $message,
            'sound'   => 'default',
//						     'custom' => [],
            'type'    => $type
        ];
        if($type=='notice'){
            $tokens = AmbassadorMobileInfo::pluck('device_token')->toArray();
        }
        else{
            $tokens=AmbassadorMobileInfo::where('ambassador_id',$id)->first();
            $tokens=[$tokens->device_token];
        }

//            return $tokens;
        $push = new PushNotification('fcm');
        $push->setMessage($complete_message)

            ->setApiKey('AAAA9VOEOuo:APA91bGo6u3AsRf_3sjvwLjzQV0q_WD8-T09gDQQz3KvkNS77DUeIJvTCwxE7usI2XwxWfjTBk10Ce3E1nDG7aBM2JpTY86wZ48NWkIOe1I2sD-bE9tYecfLLmEph8qKS0aaOdLDQTYD')
            ->setDevicesToken($tokens)
            ->send();

    }

}
