
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" id="token">
    <link rel="icon" href={{  URL::sucure_asset('img/basic/favicon.ico')}} type="image/x-icon">
    <title>{{config('app.name')}}</title>
     @toastr_css
    <!-- CSS -->
    <link rel="stylesheet" href=  {{sucure_asset('css/app.css')}}>
    <link rel="stylesheet" href=  {{sucure_asset('css/all.min.css')}}>
    <link rel="stylesheet" href=  {{sucure_asset('css/sb-admin-2.min.css')}}>
    <link rel="stylesheet" href=  {{sucure_asset('css/styles.css')}}>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    </head>
    <body id="page-top">

        <div class="app" id="app">
            {{-- Main Navigation or menu --}}

             <main>
                @yield('content')
            </main>

            <div class="control-sidebar-bg shadow white fixed"></div>
        </div>

    </body>
    <!--
    --- Footer Part - Use Jquery anywhere at page.
    --- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
    -->


    <script src={{sucure_asset('js/jquery.min.js')}}></script>
    <script src={{sucure_asset('js/bootstrap.bundle.min.js')}}></script>
    <script src={{sucure_asset('js/jquery.easing.min.js')}}></script>
    <script src={{sucure_asset('js/sb-admin-2.min.js')}}></script>
    <script src={{sucure_asset('js/functions.js')}}></script>
    <!-- Page level plugins -->
    <script src={{sucure_asset('js/jquery.dataTables.min.js')}}></script>
    <script src={{sucure_asset('js/dataTables.bootstrap4.min.js')}}></script>

    <!-- Page level custom scripts -->
    <script src={{sucure_asset('js/datatables-demo.js')}}></script>
</html>
@yield('js')
