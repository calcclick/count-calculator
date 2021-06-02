@extends('layouts.app')

@section('content')
        <div class="d-flex flex-lg-row justify-content-between py-5">
            <div>
                <form class="form-inline my-2 my-lg-0" action="{{ route('list') }}" method="GET">
                    <input class="form-control border-0" name="user" type="search" placeholder="Name" aria-label="Search">
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
        <form action="{{ route('list') }}" method="GET">
            @csrf
        </form>
        <table class="table table-bordered">
        <thead class="thead-light">
        <tr>
            <th scope="col">Sr #</th>
            <th scope="col">Name</th>
            <th scope="col">Email/Number</th>
            <th scope="col">Details</th>
        </tr>
        </thead>
        <tbody>
        @php($count=1)
        @if(count($users))
        @foreach($users as $user)
        <tr>
            <th scope="row">{{$count}}</th>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            @php($count++)
            <td>
                <a class="btn btn-danger btn-block text-white py-2 px-3 " href="{{ route('details', $user->id) }}">View</a>
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
