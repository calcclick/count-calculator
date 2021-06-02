@extends('layouts.app')

@section('content')
    <div class="d-flex flex-lg-row justify-content-between py-5">
        <div>
            <form class="form-inline my-2 my-lg-0" action="{{ route('newCustomer') }}" method="GET">
                <input class="form-control border-0" type="search" name='user' placeholder="Name" aria-label="Search">
                <button class="btn border my-2 my-sm-0 " type="submit">Search</button>
            </form>
        </div>
        <div class="pr-2">
            <form class="form-inline my-2 my-lg-0" id="showForm">
                <div class="form-group row btn btn-dark">
                    <label for="number" class="p-0 col-form-label text-md-right">{{ __('Show') }}</label>

                    <div class="col-md-6 p-0">
                        <select class="form-control border-0 bg-dark text-white" id="show" name="show" onclick="submitForm()">
                            <option class="text-white" {{ request('show') == 10 ? 'selected' : '' }} value="10">10</option>
                            <option class="text-white" {{ request('show') == 20 ? 'selected' : '' }} value="20">25</option>
                            <option class="text-white" {{ request('show') == 50 ? 'selected' : '' }} value="50">50</option>
                            <option class="text-white" {{ request('show') == 100 ? 'selected' : '' }} value="100">100</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div>
        <table class="table table-bordered">
            <thead class="thead-light">
            <tr>
                <th scope="col">Sr #</th>
                <th scope="col">Name</th>
                <th scope="col">Email/Number</th>
                <th class="d-flex justify-content-center " scope="col">Permission</th>
            </tr>
            </thead>
            <tbody>
            @php($count=1)
            @if(count($users))
                @foreach($users as $user)
            <tr>
                <th scope="row">{{$count}}</th>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                @php($count++)
                <td class="d-flex justify-content-center "  colspan="1">
                    {{--<button class="btn btn-dark m-2">--}}
                        {{--<a class="nav-link text-white py-0 px-3 " style="font-size: 0.8rem" href="{{ route('reject', $user->id) }}">Deny</a>--}}
                    {{--</button>--}}
                    {{--<button class="btn btn-danger m-2">--}}
                        {{--<a class="nav-link text-white py-0 px-3 " style="font-size: 0.8rem" href="{{ route('approved', $user->id) }}">Allow</a>--}}
                    {{--</button>--}}
                    <!-- Deny Model -->
                        <!-- Button trigger modal -->
                        <button  class="btn btn-dark m-2 px-5" style="font-size: 0.8rem" data-toggle="modal" data-target="#deny{{$user->id}}">
                            Deny
                        </button>

                        <!-- Modal -->
                        <div class="modal fade " id="deny{{$user->id}}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title font-weight-bold" id="staticBackdropLabel">Deny "{{$user->name}}" request?</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body font-weight-bold">
                                        Are you sure you want to deny
                                    </div>
                                    <div class="modal-footer justify-content-center">
                                        <a class="btn btn-secondary m-2 px-5"  data-dismiss="modal" >close</a>
                                        <a  class="btn btn-danger m-2 px-5" href="{{ route('reject', $user->id) }}">ok</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                <!-- Allow Model -->
                    <!-- Button trigger modal -->
                    <button  class="btn btn-danger m-2 px-5" style="font-size: 0.8rem" data-toggle="modal" data-target="#allow{{$user->id}}">
                        Allow
                    </button>

                    <!-- Modal -->
                    <div class="modal fade align-items-center" id="allow{{$user->id}}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title font-weight-bold" id="staticBackdropLabel">{{$user->name}}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body font-weight-bold">
                                    Are you sure you want to add new customer?
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <a class="btn btn-secondary m-2 px-5"  data-dismiss="modal" >close</a>
                                    <a  class="btn btn-danger m-2 px-5" href="{{ route('approved', $user->id) }}">Allow</a>

                                </div>
                            </div>
                        </div>
                    </div>

                     </td>
            </tr>
            @endforeach
            @endif
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            <h5>{{ $users->appends(['show' => request('show')])->links() }}</h5>
        </div>

    </div>
    <script>
        function submitForm(){
            document.getElementById("showForm").submit();
        }
    </script>
@endsection
