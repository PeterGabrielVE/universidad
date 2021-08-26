
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
     @toastr_css
    <!-- CSS -->
    <link rel="stylesheet" href=  {{asset('css/app.css')}}>
    <link rel="stylesheet" href=  {{asset('css/all.min.css')}}>
    <link rel="stylesheet" href=  {{asset('css/sb-admin-2.min.css')}}>
    <link rel="stylesheet" href=  {{asset('css/styles.css')}}>
    <link rel="stylesheet" href=  {{asset('bootstrap-fileinput/css/fileinput.css')}}>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    </head>
    <body id="page-top">

        <div class="app" id="app">
            {{-- Main Navigation or menu --}}
             <main>
                @include('sweetalert::alert')
                @yield('content')
            </main>

            <div class="control-sidebar-bg shadow white fixed"></div>
        </div>
        <!-- Footer -->
        @include('layouts.footer')
        <!-- End of Footer -->
          <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

    </body>
    <!--
    --- Footer Part - Use Jquery anywhere at page.
    --- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
    -->


    <script src={{asset('js/jquery.min.js')}}></script>
    <script src={{asset('js/bootstrap.bundle.min.js')}}></script>
    <script src={{asset('js/jquery.easing.min.js')}}></script>
    <script src={{asset('js/sb-admin-2.min.js')}}></script>
    <script src={{asset('js/functions.js')}}></script>
    <!-- Page level plugins -->
    <script src={{asset('js/jquery.dataTables.min.js')}}></script>
    <script src={{asset('js/dataTables.bootstrap4.min.js')}}></script>

    <!-- Page level custom scripts -->
    <script src={{asset('js/datatables-demo.js')}}></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</html>
@yield('js')
