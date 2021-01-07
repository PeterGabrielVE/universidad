
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" id="token">
    <link rel="icon" href={{  URL::asset('img/basic/favicon.ico')}} type="image/x-icon">
    <title>{{config('app.name')}}</title>
    <!-- CSS -->
    <link rel="stylesheet" href=  {{asset('css/app.css')}}>
    <link rel="stylesheet" href=  {{asset('css/all.min.css')}}>
    <link rel="stylesheet" href=  {{asset('css/sb-admin-2.min.css')}}>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
   
    </head>
    <body class="light">
       
        <div class="app" id="app">
            {{-- Main Navigation or menu --}}
            @include('layouts.navbar')
            
           
             <main class="py-4">
                @yield('content')
            </main>
            
            <div class="control-sidebar-bg shadow white fixed"></div>
        </div>
            <!--/#app -->
    </body>
    <!--
    --- Footer Part - Use Jquery anywhere at page.
    --- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
    -->
    <script src={{asset('js/app.js')}}></script>
    
    <script src={{asset('js/jquery.min.js')}}></script>
    <script src={{asset('js/bootstrap.bundle.min.js')}}></script>
    <script src={{asset('js/jquery.easing.min.js')}}></script>
    <script src={{asset('js/sb-admin-2.min.js')}}></script>


</html>
@yield('js')
