@extends('layouts.app')

@section('content')
    <div>
        <table class="table">
            <thead class="thead-light">
            <tr>
                <th scope="col">Sr #</th>
                <th scope="col">Name</th>
                <th scope="col">Email/Number</th>
                <th scope="col">Details</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>
                    <button></button>
                </td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td>Larry</td>
                <td>the Bird</td>
                <td>@twitter</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
