<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!--
        <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <style>
            .dropdown {
                
            }            
            .dropdown-menu {               
                background-color: #f9f9f9;
                box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            }
            .dropdown-menu input {                
                width: 250px;
            }
        </style> 

        <style>
            select {
                width: 250px;
            }

            input {
                width: 250px;
            }
        </style>

        <style>
            tfoot {
                position: relative;
            }

            tfoot td { 
                border: none;
            }

            td.absolute {
                position: absolute;
                top: 1px;
                right: 5px;
            }
        </style>

        <style>
            .pagination > li > a {
                color: #606060;
                background-color: white;
            }

            .pagination > .active > a,  
            .pagination > li > a:focus,  
            .pagination > li > a:hover
            .pagination > .active > span,
            .pagination > li > span:focus,
            .pagination > li > span:hover {
                color: #606060;
                background-color: #F0F0F0;
                border-color: #F0F0F0;
                border-radius: 5px;
            }

            .page-item.active .page-link {
                z-index: 1;
                color: white;
                background-color: #B0B0B0; 
                border-color: #B0B0B0;
                border-radius: 5px; 
            }
        </style>

        <style>
            div#container {
                width: 100%;
                margin: 40px;
            }
        </style>

        <style>
            div#tableContainer {
                width: 100%;
                margin: 40px;
                border: 1px solid lightgray;
            }
        </style>

        <style>
            a.markAsReadButton {
            padding: 3px;
        }
        </style>
        @livewireStyles               
    </head>
    <body class="antialiased">
        <header>     
            <div class="d-flex-column">
                <div class="d-flex">
                    <div class="d-flex px-3">            
                        <div class="pt-1">
                            <img src="{{ URL('storage/images/logo.jpg') }}" style="width: 100px">
                        </div>
                        <div class="text-center align-self-center p-3">
                            WELCOME,<br>
                            {{ session('LoggedUserFirstName') }}
                        </div>
                    </div>
                    <div class="d-flex w-100 align-self-start justify-content-between p-3">
                        <div class="py-1">
                            Logged in as: {{ session('LoggedUserRole') }}<br>  
                        </div>
                        <div class="d-flex justify-content-end">
                            <div class="d-flex-column">
                                <span>                                
                                    <button class="btn py-0" type="button" data-bs-toggle="modal" data-bs-target="#notifications">
                                        <div class="position-relative" style="font-size: 14px !important"> 
                                            NOTIFICATIONS <i class="material-icons align-bottom">notifications</i>                   
                                            @if ($currentUser->unreadNotifications->count() > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $currentUser->unreadNotifications->count() }}
                                            </span>
                                            @endif
                                        </div>
                                    </button>
                                </span>
                                @livewire('notifications')
                            </div>
                            <div class="d-flex-column">
                                <span>
                                    <button class="btn py-0" type="button" data-bs-toggle="collapse" data-bs-target="#userActions" aria-expanded="false" aria-controls="collapseExample">
                                        <div style="font-size: 14px !important"> 
                                            USER ACTIONS <i class="material-icons align-bottom">person</i>
                                        </div>
                                    </button>
                                </span>  
                                <div class="collapse text-end border" id="userActions" style="font-size: 14px !important">
                                    <a class="text-decoration-none text-secondary" href="{{ route('logout') }}"> Signout</a><br>
                                    <a class="text-decoration-none text-secondary" href="{{ route('userProfile') }}"> Manage user profile</a>
                                </div>                                    
                            </div>               
                        </div>
                    </div>
                </div>
                <div class="float-start pt-3" style="min-height: 100vh">
                    <ul class="nav nav-tabs flex-column border-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link text-secondary" data-toggle="tab" href="{{ route('dashboard') }}" role="tab" aria-controls="home" aria-selected="true"><i class="material-icons align-bottom">dashboard</i> Dashboard Home</a>
                        </li>
                        @if (session('LoggedUserRole') == 'Admin')           
                        <li class="nav-item">
                            <a class="nav-link text-secondary" data-toggle="tab" href="{{ route('roleAssignment') }}" role="tab" aria-controls="roles" aria-selected="true"><i class="material-icons align-bottom">person_add</i> Manage Role Assignment</a>
                        </li>
                        @endif
                        @if (session('LoggedUserRole') == 'Admin' || session('LoggedUserRole') == 'Project manager') 
                        <li class='nav-item'>
                            <a class='nav-link text-secondary' href="{{ route('projectAssignment') }}"><i class='material-icons align-bottom'>people</i> Manage Project Users</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link text-secondary" href="{{ route('projects') }}"><i class="material-icons align-bottom">list</i> My Projects</a>
                        </li>
                        <li class="nav-item"> 
                            <a class="nav-link text-secondary" href="{{ route('tickets') }}"><i class="material-icons align-bottom">article</i> My Tickets</a>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link text-secondary" href="{{ route('userProfile') }}"><i class="material-icons align-bottom">person</i> User Profile</a> 
                        </li>
                    </ul>       
                </div>                      
            </div>
        </header>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>    
    @yield('content')
    @livewireScripts                
    </body>
</html>