<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Counter Calculator') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Custom Css -->

    <link rel="stylesheet" href="../../css/custom.css">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: "Lato", sans-serif;
        }
        .top-nav{
            background: #F5F5F5;
            position: fixed;
            width: 100%;
            z-index: 3;
        }
        .sidebar {
            margin: 0;
            padding: 0;
            width: 200px;
            position: fixed;
            height: 100%;
            overflow: auto;
            top:80px;
        }

        .sidebar a {
            display: block;
            color: black;
            padding: 16px;
            text-decoration: none;
        }

        .sidebar a.active {
            background-color: #04AA6D;
            color: white;
        }

        .sidebar a:hover:not(.active) {
            background-color: #555;
            color: white;
        }

        div.content {
            margin-left: 200px;
            padding: 1px 16px;
            top: 80px;
            position: absolute;
        }
        .nav-link-a{
            color: white !important;
        }
        .nav-link-a:hover{
            font-weight: bold;
            color: white !important;
            font-size: 1rem;
        }
        .nav-link-a:active{
            font-weight: bold;
            color: white !important;
            font-size: 1rem;
        }
        .nav-link-a:focus{
            font-weight: bold;
            color: white !important;
            font-size: 1rem;
        }
        @media screen and (max-width: 700px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .sidebar a {float: left;}
            div.content {margin-left: 0;}
        }

        @media screen and (max-width: 400px) {
            .sidebar a {
                text-align: center;
                float: none;
            }
        }
    </style>
</head>
<body>
<div id="app" style="background: #F5F5F5">
    <div class="">
<div class="container" style="background: #F5F5F5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 " style="background: #F5F5F5">
                <div class="card-header d-flex justify-content-center bg-transparent border-bottom-0">
                    <img src="{{ URL::to('/image/artboard_crop.png') }}">
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row d-flex flex-column align-items-center">
                            <label for="email" class="col-md-5 col-form-label text-md-left">{{ __('E-Mail/Number') }}</label>

                            <div class="col-md-5">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row flex-column align-items-center">
                            <label for="password" class="col-md-5 col-form-label text-md-left">{{ __('Password') }}</label>

                            <div class="col-md-5">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{--<div class="form-group row">--}}
                            {{--<div class="col-md-6 offset-md-4">--}}
                                {{--<div class="form-check">--}}
                                    {{--<input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>--}}

                                    {{--<label class="form-check-label" for="remember">--}}
                                        {{--{{ __('Remember Me') }}--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group row mb-0 d-flex flex-column align-items-center">
                            <div class="col-md-5">
                                <button type="submit" class="btn btn-dark w-100">
                                    {{ __('Login') }}
                                </button>

                                {{--@if (Route::has('password.request'))--}}
                                    {{--<a class="btn btn-link" href="{{ route('password.request') }}">--}}
                                        {{--{{ __('Forgot Your Password?') }}--}}
                                    {{--</a>--}}
                                {{--@endif--}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
</body>
</html>

