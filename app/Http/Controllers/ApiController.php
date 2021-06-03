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
   public function getDataCount(Request $request,$id){
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
       $this->setData([
          'counter'=>$count
       ]);
       return $this->response();
   }
}
