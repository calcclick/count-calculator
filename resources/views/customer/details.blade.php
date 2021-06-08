@extends('layouts.app')
@section('content')
    <div class="border">

        @if(Session::has('error'))
            <div class="alert alert-danger">
                {{ Session::get('error')}}
            </div>
        @endif

            @if(Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success')}}
                </div>
            @endif
        <div class="d-flex justify-content-between border-bottom " style="background: #F5F5F5">
            <h3 class=" p-2 " style="font-weight:bold ">CUSTOMER DETAIL</h3>
            {{--<div class="input-group h-100 col-3 my-2">--}}
                {{--<div class="input-group-prepend">--}}
                    {{--<button class="input-group-text text-primary" onclick="submitFrom()">search</button>--}}
                {{--</div>--}}
                {{--<input id="time" type="number" class="form-control bg-light " name="from">--}}
            {{--</div>--}}
            {{--<div id='scroling-text' class="example1 col-4">--}}
                {{--<h3>This is the scrolling text </h3>--}}
            {{--</div>--}}
        </div>
        <div class="border-bottom mb-2 bg-white">
            <div class="px-2 py-4  justify-content-center align-items-center row">
                <div class="col-6">
                    @if($customer)
                        <h4>
                            {{$customer->name}}
                        </h4>

                    @endif
                        <?php

                        $month = date('m');
                        $day = date('d');
                        $year = date('Y');

                        $today = $year . '-' . $month . '-' . $day;
                        ?>
                </div>
                <div class="col-6">

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button class="input-group-text text-primary">From</button>
                        </div>
                        <input value="{{$qry['from'] !== '' ? $qry['from'] : $today}}" id="from-date" type="date"
                               class="form-control bg-light " name="from">
                        {{--<input value="{{$qry['to'] !== '' ? $qry['to'] : ''}}" id="to-date" type="date" class="bg-light form-control " name="to"/>--}}

                    </div>
                </div>
            </div>
            <div class="px-2 py-4  justify-content-center align-items-center row">
                <div class="col-6">
                    @if($customer)
                        <h4>
                            {{$customer->email}}
                        </h4>

                    @endif
                </div>
                <div class="col-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button class="input-group-text text-primary">To</button>
                        </div>
                        <input value="{{$qry['to'] !== '' ? $qry['to'] : $today}}" id="to-date" type="date"
                               class="form-control bg-light " name="to">
                    </div>
                </div>
            </div>

            <div class="px-2 py-4  justify-content-end align-items-end row">

                <div class="col-12">
                    <input type="button" class="search btn btn-primary float-right" value="Search"/>
                </div>
            </div>

        </div>
        <div>
            <h3 class=" p-2 border-bottom" style="font-weight:bold ;background: #F5F5F5">Total Count <button class="btn btn-danger float-right" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">Reset</button></h3>
            <div class="bg-white w-100">

                <div class="col-lg-7 d-flex justify-content-around align-items-center bg-white py-5">
                    {{--@if($count)--}}
                    {{--<form class="form-inline my-2 my-lg-0" id="saveRecord"  action="{{ route('saveCounter') }}" method="post">--}}
                        {{--@csrf--}}
                        {{--<input type="hidden" name="user_id" value="{{$customer->id}}">--}}
                    <div><img style="width: 105px" src="{{ URL::to('/image/pluscount.png') }}"></div>
                    <div><h4  id="counter-up"  style="width:100px " class="h2 font-weight-bold">{{$count ? $count->counter_up : 0}}</h4></div>
                    <div><img style="width: 100px" src="{{ URL::to('/image/down.png') }}"></div>
                    <div><h4  id="counter-down"  style="width:100px " class=" h2  font-weight-bold">{{$count ? $count->counter_down : 0}}</h4></div>
                    <div class="border rounded mb-2"><img style="width: 45px" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" class="btn" id="edit-record" src="{{ URL::to('/image/pencil.png') }}"></div>
                    {{--@else--}}
                    {{--<div class="alert-info">No Record found.</div>--}}
                    {{--@endif--}}
                    {{--<div>--}}
                        {{--<button class="btn btn-danger mb-2 ml-2" id="save-counter" type="submit" >save</button>--}}
                    {{--</div>--}}
                    {{--</form>--}}
                </div>
                <!-- Modal -->
                {{--<div class="modal fade " id="save{{$customer->id}}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">--}}
                    {{--<div class="modal-dialog">--}}
                        {{--<div class="modal-content">--}}
                            {{--<div class="modal-header">--}}
                                {{--<h5 class="modal-title font-weight-bold" id="staticBackdropLabel">Edit Record</h5>--}}
                                {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                                    {{--<span aria-hidden="true">&times;</span>--}}
                                {{--</button>--}}
                            {{--</div>--}}

                            {{--@if($count)--}}
                            {{--<form class="form-inline modal-body my-2 my-lg-0" id="saveRecord"  action="{{ route('saveCounter') }}" method="post">--}}
                            {{--@csrf--}}
                                {{--<div class="d-flex justify-content-center col-12">--}}
                                    {{--<form action="{{ route('details') }}" method="GET">--}}
                                    {{--<div class="input-group col-7">--}}
                                        {{--<div class="input-group-prepend">--}}
                                            {{--<button class="input-group-text text-primary">Date</button>--}}
                                        {{--</div>--}}
                                        {{--<input value="{{$today}}" id="save-date" type="date"--}}
                                               {{--class="form-control bg-light " name="date">--}}
                                    {{--</div>--}}
                                    {{--</form>--}}
                                {{--</div>--}}
                            {{--<input type="hidden" name="user_id" value="{{$customer->id}}">--}}
                            {{--<div><img style="width: 105px" src="{{ URL::to('/image/pluscount.png') }}"></div>--}}
                            {{--<div><input type="text" id="counter-up-m"  value="{{$count->counter_up ?? 0}}" style="width:100px " name="counter_up" class="h2 font-weight-bold"/></div>--}}
                            {{--<div><img style="width: 100px" src="{{ URL::to('/image/down.png') }}"></div>--}}
                            {{--<div><input type="text" id="counter-down-m"  value="{{$count->counter_down ?? 0}}" style="width:100px " name="counter_down" class=" h2  font-weight-bold"/></div>--}}
                            {{--<div class="border rounded mb-2"><img style="width: 45px" data-toggle="modal" data-target="#save{{$customer->id}}" class="btn " src="{{ URL::to('/image/pencil.png') }}"></div>--}}
                            {{--@else--}}
                            {{--<div class="alert-info">No Record found.</div>--}}
                            {{--@endif--}}
                            {{--<div>--}}
                            {{--<button class="btn btn-danger mb-2 ml-2" id="save-counter" type="submit" >save</button>--}}
                            {{--</div>--}}

                            {{--<div class="modal-footer justify-content-center w-100">--}}
                                {{--<a class="btn btn-secondary m-2 px-4"  data-dismiss="modal" >close</a>--}}
                                {{--<button class="btn btn-danger px-5" id="save-counter" type="submit" >save</button>--}}

                            {{--</div>--}}
                            {{--</form>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <!-- Allow Model -->
                <div class="collapse col-6 " id="collapseExample">
                    <form class="form-inline card my-2 mb-2 my-lg-0 d-flex flex-lg-row" id="saveRecord"  action="{{ route('saveCounter') }}" method="post">
                        @csrf
                        <h4 class="w-100 mb-2 card-header">Edit Details:</h4>
                        <div class="d-flex justify-content-center col-12 pb-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button class="input-group-text text-primary">Date</button>
                                </div>
                                <input value="{{$today}}" id="save-date" type="date"
                                       class="form-control bg-light " name="date">
                            </div>
                        </div>
                        <input type="hidden" name="user_id" value="{{$customer->id}}">
                        <div><img style="width: 105px" src="{{ URL::to('/image/pluscount.png') }}"></div>
                        <div><input type="text" id="counter-up-m"  style="width:100px " name="counter_up" class="h2 font-weight-bold"/></div>
                        <div><img style="width: 100px" src="{{ URL::to('/image/down.png') }}"></div>
                        <div><input type="text" id="counter-down-m"   style="width:100px " name="counter_down" class=" h2  font-weight-bold"/></div>

                        <div class="modal-footer justify-content-center w-100 pt-3 mt-2">
                            <button class="btn btn-danger px-5" id="save-counter" type="submit" >save</button>

                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reset Data for <span style="color: maroon">{{$customer->name}}</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('reset.count')}}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{$customer->id}}">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reset_type" id="flexRadioDefault1" value="today" checked>
                            <label class="form-check-label" for="today">
                                Today
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reset_type" id="flexRadioDefault2" value="all_date">
                            <label class="form-check-label" for="all_date">
                                All Date
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reset_type" id="flexRadioDefault2" value="range">
                            <label class="form-check-label" for="range">
                                Range &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input class="" type="date" name="from_date">
                                <input class="" type="date" name="to_date">
                            </label>
                        </div>
                        <br/>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Reset</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    <script>
        function submitFrom() {
            document.getElementById("fromDate").submit();
        }

        function submitTo() {
            document.getElementById("toDate").submit();
        }

    </script>


@endsection

@section('script')
    <script>


        (function (yourcode) {

            yourcode(window.jQuery, window, document);

        }(function ($, window, document) {
            var body = $('body');
            var fromDate = $('#from-date');
            var toDate = $('#to-date');


            $(function () {
                body.on('click', '.search', function (e) {
                    var errorCount = 0;

                    e.preventDefault();
                    e.stopImmediatePropagation();

                    if (fromDate.val() === '') {
                        fromDate.addClass('is-invalid');
                        errorCount++;
                    } else {
                        fromDate.removeClass('is-invalid');
                    }

                    if (toDate.val() === '') {
                        toDate.addClass('is-invalid');
                        errorCount++;
                    } else {
                        toDate.removeClass('is-invalid');
                    }

                    if (errorCount === 0) {
                        var base = "{{route('details', $customer->id)}}";
                        var href = base + '?from=' + fromDate.val() + '&to=' + toDate.val();
                        document.location.href = href;
                    }

                    return false;
                })

            });

            $("#edit-record").on('click', function(){
                counterData();
            });

            $("#save-date").on('change', function(){
                counterData();
            })
            // The rest of the code goes here!
            setInterval(function () {
                $.ajax({
                    url: '/api/counter/{{$customer->id}}'
                }).done(function (value) {
                    $("#counter-up").text(value.data.counter.counter_up);
                    $("#counter-down").text(value.data.counter.counter_down);
                })
            }, 500);

            function counterData() {
                var counterDate = $("#save-date").val();
                console.log(counterDate);
                $.ajax({
                    url: '/api/counter/{{$customer->id}}?date='+counterDate
                }).done(function (value) {
                    console.log(value);
                    $("#counter-up-m").val(value.data.counter == null ? 0 : value.data.counter.counter_up  );
                    $("#counter-down-m").val(value.data.counter == null ? 0 : value.data.counter.counter_down);
                })
            }

        }));
    </script>
@endsection
