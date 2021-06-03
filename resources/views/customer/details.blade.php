@extends('layouts.app')

@section('content')
    <div class="border">
        <div>
            <h3 class=" p-2 border-bottom" style="font-weight:bold ;background: #F5F5F5">CUSTOMER DETAIL</h3>
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
            <h3 class=" p-2 border-bottom" style="font-weight:bold ;background: #F5F5F5">Total Count</h3>
            <div class="bg-white w-100">

                <div class="col-lg-5 d-flex justify-content-around align-items-center bg-white py-5">
                    {{--@if($count)--}}
                    <div><img style="width: 80px" src="{{ URL::to('/image/up.png') }}"></div>
                    <div><h4 id="counter-up" class="font-weight-bold"> {{$count ? $count->counter_up : 0}} </h4></div>
                    <div><img style="width: 70px" src="{{ URL::to('/image/down.png') }}"></div>
                    <div><h4 id="counter-down" class="font-weight-bold">{{$count ? $count->counter_down : 0}} </h4></div>
                    {{--@else--}}
                    {{--<div class="alert-info">No Record found.</div>--}}
                    {{--@endif--}}
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

            // The rest of the code goes here!
            setInterval(function () {
                $.ajax({
                    url: '/api/counter/{{$customer->id}}'
                }).done(function (value) {
                    $("#counter-up").text(value.data.counter.counter_up);
                    $("#counter-down").text(value.data.counter.counter_down);
                })
            }, 500)
        }));
    </script>
@endsection
