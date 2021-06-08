<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Counter Calculator') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Custom Css -->

    <link rel="stylesheet" href="../../css/custom.css">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <script src="https://use.fontawesome.com/f2f635ae00.js"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @yield('style')
        body {
            margin: 0;
            font-family: "Lato", sans-serif;
        }

        .top-nav {
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
            top: 80px;
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

        .nav-link-a {
            color: white !important;
        }

        .nav-link-a:hover {
            font-weight: bold;
            color: white !important;
            font-size: 1rem;
            background-color: #e3342f;
        }

        .nav-link-a:active {
            font-weight: bold;
            color: white !important;
            font-size: 1rem;
            background-color: #e3342f;
        }

        .nav-link-a:focus {
            font-weight: bold;
            color: white !important;
            font-size: 1rem;
            background-color: #e3342f;
        }

        .link-active {
            border-left: 2px silver;
            font-weight: bold;
            color: white !important;
            font-size: 1rem;
            background-color: #e3342f;
        }

        @media screen and (max-width: 700px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .sidebar a {
                float: left;
            }

            div.content {
                margin-left: 0;
            }
        }

        @media screen and (max-width: 400px) {
            .sidebar a {
                text-align: center;
                float: none;
            }
        }
        /*.example1 {*/
            /*height: 50px;*/
            /*overflow: hidden;*/
            /*position: relative;*/
        /*}*/
        /*.example1 h3 {*/
            /*font-size: 3em;*/
            /*color: blue;*/
            /*position: absolute;*/
            /*width: 100%;*/
            /*height: 100%;*/
            /*margin: 0;*/
            /*line-height: 50px;*/
            /*text-align: center;*/
            /*!* Starting position *!*/
            /*-moz-transform:translateX(100%);*/
            /*-webkit-transform:translateX(100%);*/
            /*transform:translateX(100%);*/
            /*!* Apply animation to this element *!*/
            /*-moz-animation: example1 15s linear infinite;*/
            /*-webkit-animation: example1 15s linear infinite;*/
            /*animation: example1 15s linear infinite;*/
        /*}*/
        /*!* Move it (define the animation) *!*/
        /*@-moz-keyframes example1 {*/
            /*0%   { -moz-transform: translateX(100%); }*/
            /*100% { -moz-transform: translateX(-100%); }*/
        /*}*/
        /*@-webkit-keyframes example1 {*/
            /*0%   { -webkit-transform: translateX(100%); }*/
            /*100% { -webkit-transform: translateX(-100%); }*/
        /*}*/
        /*@keyframes example1 {*/
            /*0%   {*/
                /*-moz-transform: translateX(100%); !* Firefox bug fix *!*/
                /*-webkit-transform: translateX(100%); !* Firefox bug fix *!*/
                /*transform: translateX(100%);*/
            /*}*/
            /*100% {*/
                /*-moz-transform: translateX(-100%); !* Firefox bug fix *!*/
                /*-webkit-transform: translateX(-100%); !* Firefox bug fix *!*/
                /*transform: translateX(-100%);*/
            /*}}*/
    </style>
</head>
<body>
<div id="app" style="background: #F5F5F5">
    @php
        $user = Auth::user();
    @endphp
    <div class="">
        <nav class="navbar top-nav navbar-expand-md navbar-light  shadow-sm " style="background: #F5F5F5">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img style="max-width: 75px;" src="{{ URL::to('/image/artboard_crop.png')}}">

                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))

                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ $user->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
    document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>


        <div class="container">
            @if($user ? $user->user_role === 'isAdmin' : FALSE)
                <div class="sidebar bg-dark">
                    <nav class="navbar navbar-expand-lg px-0 ">
                        <div class="collapse navbar-collapse w-100" id="navbarText">
                            <ul id="sidebar-count"
                                class="navbar-nav mr-auto d-flex justify-content-center flex-column w-100">
                                <li class="nav-item border-bottom">
                                    <a id="sidebar-count-detail"
                                       class="  nav-link pl-4 nav-link-a {{ request()->segment(1) == 'customer-details' ? 'link-active' : '' }}"
                                       style="min-height: 70px" href="{{ url('/customer-details') }}">CUSTOMER
                                        DETAILS</a>
                                </li>
                                <li class="nav-item ">
                                    <a id="sidebar-count-new"
                                       class="nav-link pl-4 nav-link-a {{ request()->segment(1) == 'new-customer' ? 'link-active' : '' }}"
                                       style="min-height: 70px" href="{{ url('/new-customer')}}">NEW CUSTOMER</a>
                                </li>
                            </ul>

                        </div>
                    </nav>
                </div>
            @endif

            <div class="content {{($user ? $user->user_role !== 'isAdmin' : '') ? 'col-6' : 'offset-2 col-9'}}">
                @yield('content')
            </div>
        </div>

    </div>
</div>
@yield('script')
</body>
</html>
