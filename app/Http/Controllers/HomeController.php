<?php

namespace App\Http\Controllers;

use App\MobileInfo;
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
        if ($qry['from']) {
            $from = Carbon::parse($qry['from'])->startOfDay()->toDateTimeString();
        }
        if (!$qry['from']) {
            $from = Carbon::now()->startOfDay()->toDateTimeString();
        }
        if ($qry['to']) {
            $to = Carbon::parse($qry['to'])->endOfDay()->toDateTimeString();
        }

        if (!$qry['to']) {
            $to = Carbon::now()->endOfDay()->toDateTimeString();
        }

        $customer = User::find($id);

        if(!$customer){
            return redirect()->route('customerDetails');
        }

        $count = Counter::where('user_id', $id)
            ->whereBetween('created_at', [$from,$to]);

        $count->when(($qry['from'] && !$qry['to']), function ($sql) use ($qry) {
            $sql->whereBetween('created_at', [Carbon::parse($qry['from'])->startOfDay(), Carbon::today()->endOfDay()]);
        });


        $count->when(!$qry['from'] && $qry['to'], function ($sql) use ($qry) {
            $sql->whereBetween('created_at', [Carbon::parse($qry['to'])->subDay()->startOfDay(), Carbon::parse($qry['to'])->endOfDay()]);
        });


        $count->when($qry['from'] && $qry['to'], function ($sql) use ($qry) {
            $sql->whereBetween('created_at', [Carbon::parse($qry['from'])->startOfDay(), Carbon::parse($qry['to'])->endOfDay()]);
        });

        $count = $count->select(DB::raw('SUM(`counter_up`) as counter_up, SUM(`counter_down`) as counter_down, user_id'))
            ->groupBy('user_id')
            ->first();

        return view('customer.details', compact('customer', 'count', 'qry'));
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
        $customerQry = User::where('status', 'Accepted');
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
}
