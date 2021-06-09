<?php

namespace App\Http\Controllers;

use App\MobileInfo;
use App\TimeSetting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\CodeCoverageException;
use App\Counter;
use Carbon\Carbon;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function customerDetails()
    {
        return view('customer.customerDetails');
    }

    public function newCustomer(Request $request)
    {
        $customerQry = User::where('status', 'Pending');
        $user_name = $request->get("user", '');
        $customerQry->when($user_name, function ($sql) use ($user_name) {
            $sql->where('name', 'LIKE', '%' . $user_name . '%');
        });
        $users = $customerQry->paginate($request->get('show', 10));
        return view('customer.newCustomer', compact('users'));
    }

    public function detail(Request $request, $id)
    {
        $qry = $this->getRequestedQuery($request);

        $customer = User::find($id);

//        dd($request->all());

        if(!$customer){
            return redirect()->route('customerDetails');
        }

        $count = Counter::where('user_id', $id)
            ->whereBetween('created_at', [$qry['from']->startOfDay(), $qry['to']->endOfDay()]);


//        $count->when($qry['from'] && $qry['to'], function ($sql) use ($qry) {
//            $sql->whereBetween('created_at', [Carbon::parse($qry['from'])->startOfDay(), Carbon::parse($qry['to'])->endOfDay()]);
//        });
//        dd($qry['date']);


        $count = $count->select(DB::raw('SUM(`counter_up`) as counter_up, SUM(`counter_down`) as counter_down, user_id'))
            ->groupBy('user_id')
            ->first();

        return view('customer.details', compact('customer', 'count', 'qry'));
    }

    public function resetUserCount(Request $request){


//dump($request->toArray());

        $id = $request->get('user_id');

//        Counter::fin()
        if ($request->get('reset_type') == 'today'){
            $from = Carbon::now()->startOfDay()->toDateTimeString();
            $to = Carbon::now()->endOfDay()->toDateTimeString();
            $countQry = Counter::where('user_id', $id)
                ->whereBetween('created_at', [$from,$to]);
        }else if ($request->get('reset_type') == 'all_date'){
            $countQry = Counter::where('user_id', $id);

        }else if ($request->get('reset_type') == 'range'){

            if (empty($request->get('from_date')) || empty($request->get('to_date'))){
                 return redirect()->back()->with('error','range both field is required');
            }

            $from = Carbon::parse($request->get('from_date'))->startOfDay()->toDateTimeString();
            $to = Carbon::parse($request->get('to_date'))->endOfDay()->toDateTimeString();

            $countQry = Counter::where('user_id', $id)
                ->whereBetween('created_at', [$from,$to]);
        }
        $counts = $countQry->get();
        foreach ($counts as $count ){
            $count->counter_up = 0;
            $count->counter_down = 0;
            $count->save();
        }
       return redirect()->back()->with('success','reset Successfully !');

    }

    public function approvedCustomer(Request $request, $id)
    {

        $user = User::where('id', $id)->first();
        if (!$user) {
            return redirect()
                ->back()
                ->withErrors("Customer Not found")
                ->withInput();
        }

        try {
            $user->update([
                'status' => 'Accepted',
            ]);
            $this->sendEmail($user->name, $user->email, $user->otp);
//            $this->sendPush('Your Account IS Approved Kindly Enter Otp For Activation','Approved','default','approved',$id);
            return redirect()->back()->with("message", 'Successfully Updated Customer Details!!');

        } catch (\Exception $e) {
            Log::info('Error on: ' . __DIR__ . ': ' . __LINE__);
            Log::error($e);

            return redirect()
                ->back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    public function rejectCustomer($id)
    {

        $user = User::where('id', $id)->first();
        $mobileInfo=MobileInfo::where('user_id',$id)
            ->where('status','active')
            ->first();
        if (!$user) {
            return redirect()
                ->back()
                ->withErrors("Customer Not found")
                ->withInput();
        }

        try {
            $user->update([
                'verified_at' => 0,
                'status' => 'Rejected'
            ]);
            $this->sendPush('Your Account IS Rejected Sorry for inconvenience','Rejected','default','rejected',$id);
            if($mobileInfo){
                $mobileInfo->update([
                    'status'=>'inActive'
                ]);
            }
            return redirect()->with("message", 'Successfully Updated Customer !!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    public function customerList(Request $request)
    {
        $customerQry = User::where('status', 'Accepted')
            ->where('user_role','isCustomer');
        $user_name = $request->get("user", '');
        $customerQry->when($user_name, function ($sql) use ($user_name) {
            $sql->where('name', 'LIKE', '%' . $user_name . '%');
        });

        $users = $customerQry->paginate($request->get('show', 10));
//        dd($users);
        return view('customer.customerDetails', compact('users'));

    }

    public function unauthorised(){
        return view('not-admin');
    }
    public function saveCount(Request $request){

//        dd($request);
        $dateOfRecord= Carbon::parse($request->date);

        $userCount = Counter::where('user_id', $request->user_id)
            ->whereBetween('created_at',[$dateOfRecord->startOfDay()->toDateTimeString(),$dateOfRecord->endOfDay()->toDateTimeString()])
            ->get();
        if (!$userCount) {
            if($request->from == $request->to){
                $userCount->each->delete();
                $userCountSave=Counter::create([
                    'user_id' => $request->user_id,
                    'counter_up' => $request->counter_up,
                    'counter_down'=>$request->counter_down,
                    'created_at'=> $dateOfRecord
                ]);
                return redirect()->back()->with('success','Count Save Successfully !');}else {
                return redirect()->back()->with('error', 'Cant save the record Dates must be same !');
            }
        }

        try {
            if($request->from == $request->to){
            $userCount->each->delete();
            $userCountSave=Counter::create([
                'user_id' => $request->user_id,
                'counter_up' => $request->counter_up,
                'counter_down'=>$request->counter_down,
                'created_at'=> $dateOfRecord
            ]);
            return redirect()->back()->with('success','Count Save Successfully !');}else{
                return redirect()->back()->with('error','Cant save the record Dates must be same !');
            }
        } catch (\Exception $e) {
            Log::info('Error on: ' . __DIR__ . ': ' . __LINE__);
            Log::error($e);

            return redirect()
                ->back()
                ->withErrors($e->getMessage())
                ->withInput();
        }

    }
//    public function timeSetting(Request $request,$id){
//        $validator = Validator::make($request->all(), [
//            'time' => 'required',
//        ]);
//
//        if ($validator->fails()) {
//            $error = $validator->errors()->first();
//            $this->setErrors([
//                $error
//            ], 400);
//
//            return $this->response();
//        }
//        $time=TimeSetting::find(id);
//        if(!$time){
//            $timeSettings = TimeSetting::create([
//                'id' => $id,
//                'time_setting' => $request->time
//            ]);
//            return redirect()
//                ->back();
//        }else{
//            $time->update([
//                'time_setting'=>$request->time
//            ]);
//            $time->save();
//            return redirect()
//                ->back();
//        }
//
//    }
}
