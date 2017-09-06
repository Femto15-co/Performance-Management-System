<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PMS') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
    <link rel="stylesheet" href="{{asset('css/main.css')}}">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/css/bootstrap-datetimepicker.css">
    <!-- Add DataTable on Demand -->
    @if(isset($includeDataTable))
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.dataTables.min.css">
        <link rel="stylesheet" href="{{asset('css/dataTables.bootstrap.min.css')}}">
    @endif
    <style>
        body {
            font-family: 'Lato';
        }
        .fa-btn {
            margin-right: 6px;
        }
    </style>
    @section('extra-css')
        {{-- expr --}}
    @show
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
	'csrfToken' => csrf_token(),
]); ?>
    </script>
</head>
<body>
<div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a href="{{ url('/') }}">
                    <img src="{{asset('img/femto15.png')}}">
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                @if (Auth::check())
                    <li><a href="{{ route('report.index') }}">{{trans('reports.reports')}}</a></li>
                    @if (Auth::user()->hasRole('employee'))
                        <li><a href="{{ route('defect.index',[Auth::id()]) }}">{{trans('defects.title')}}</a></li>
                        <li><a href="{{ route('bonus.index',[Auth::id()]) }}">{{trans('bonuses.title')}}</a></li>
                        <li><a href="{{ route('statistics.view') }}">{{trans('statistics.title')}}</a></li>
                    @elseif (Auth::user()->hasRole('admin'))
                        <li><a href="{{ route('user.index') }}">{{trans('users.employees')}}</a></li>
                        <li><a href="{{ route('rule.index') }}">{{trans('rules.rules')}}</a></li>
                        <li><a href="{{ route('project.index') }}">{{trans('projects.projects')}}</a></li>
                        <li><a href="{{ route('sheet.index') }}">{{trans('sheets.sheets')}}</a></li>
                    @endif
                @endif
                </ul>
                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Admin Login</a></li>
                        {{-- <li><a href="{{ url('/register') }}">Register</a></li> --}}
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <!-- Notifications Center  -->
    <!-- this is alert for Success operations -->
    <div class="container">
        <div class="row">
            @section('session_flash')
                @if(Session::has('flash_message'))
                    <div class=" callout callout-success alert alert-success" id="success-alert">
                        <i class="close glyphicon glyphicon-remove" data-dismiss="alert" style="float: right"></i>
                        <strong>Success!</strong>
                        {{ Session::get('flash_message') }}
                    </div>
                @endif
            <!-- this is alert for Failure operations -->
                @if(Session::has('error'))
                    <div class=" callout callout-danger alert alert-danger" id="success-alert">
                        <i class="close glyphicon glyphicon-remove" data-dismiss="alert" style="float: right"></i>
                        <strong>Problem! </strong>
                        {{ Session::get('error') }}
                    </div>
                @endif
            @show
        </div>
    </div>
    @yield('content')
</div>

<!-- JavaScripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<!-- For good UX date & time picking -->
<script type="text/javascript" src="{{asset('js/moment.min.js')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/js/bootstrap-datetimepicker.min.js"></script>
<!-- Add DataTable on Demand -->
@if(isset($includeDataTable))
    <script> var dataTableRoute = '{{$dataTableRoute}}'; </script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.12/cr-1.3.2/fh-3.1.2/r-2.1.0/rr-1.1.2/se-1.2.0/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/dataTableCustom.js')}}"></script>
@endif
@yield('extra-js')
@yield('packages')
</body>
</html>
