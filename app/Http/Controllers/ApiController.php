<?php

namespace App\Http\Controllers;

use App\User;
use App\Counter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class ApiController extends Controller
{
    public function getDataCount(Request $request, $id)
    {
        $qry = $this->getRequestedQuery($request);
        $customer = User::find($id);

        if (!$customer) {
            return redirect()->route('customerDetails');
        }

        $count = Counter::where('user_id', $id);

        if(!$request->get('date')) {
            $count->whereBetween('created_at', [$qry['from']->startOfDay(), $qry['to']->endOfDay()]);
        }else{
            $count->whereBetween('created_at', [$qry['date']->startOfDay()->toDateTimeString(), $qry['date']->endOfDay()->toDateTimeString()]);
        }


        $count = $count->select(DB::raw('SUM(`counter_up`) as counter_up, SUM(`counter_down`) as counter_down, user_id'))
            ->groupBy('user_id')
            ->first();
        $this->setData([
            'counter' => $count
        ]);
        return $this->response();
    }
}
