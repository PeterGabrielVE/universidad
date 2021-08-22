
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        body{
        margin: 0;
        padding: 0;
        font-family: sans-serif;
        background: linear-gradient(90deg, hsla(337, 92%, 31%) 0%, hsla(0, 20%, 86%) 100%);
        background-size: cover;



         background-position: center 0;
         background-repeat: no-repeat;
         background-attachment: fixed;
          -webkit-background-size: cover;
          -o-background-size: cover;
          -moz-background-size: cover;
          -ms-background-size: cover;

    }
    .login-box{
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%,-50%);
        width: 400px;
        padding:40px;
        background: #F3E4E7;
        box-sizing: border-box;
        box-shadow: 0 15px  25px #900C3F;
        border-radius: 10px;
    }
    .login-box h2{
        margin: 0 0 30px;
        padding: 0;
        text-align: center;
        color: #fff;
    }
    .login-box .login-field{
        position: relative;
    }
    .login-box .login-field  input{
        width: 100%;
        padding: 10px 0;
        font-size: 16px;
        color: #000000;
        margin-bottom: 30px;
        border: none;
        border-bottom: 1px solid #fff;
        outline: none;
        background: transparent;
    }
    .login-box .login-field  label{
        position: absolute;
        top: 0;
        left: 0;
        letter-spacing: 1px;
        padding: 10px 0;
        font-size: 16px;
        color: #000000;
        pointer-events: none;
        transition: .5s;
    }
    .login-box .login-field input:focus ~ label,
    .login-box .login-field input:valid ~ label{
        top: -23px;
        left: 0;
        color:#000000;
        font-size: 12px;
    }
    .login-box button{
        background: transparent;
        border: none;
        outline: none;
        color: #000000;
        background: #900C3F;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
    }
    </style>

</head>

<body id="page-top">
    <div class="app" id="app">
    <main>
    <div class="login-box">
    <div class="text-center mb-4">
        <img src="img/logo.png" alt="" width="100px">
    </div>
    <form  method="POST" action="{{ route('login') }}">
        @csrf
        <div class="login-field">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <label>Correo</label>
        </div>
        <div class="login-field">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <label >Contraseña</label>
        </div>
        <div class="text-center mb-4">
            <button type="submit">Ingresar</button>
        </div>

    </form>
</div>
</main>

<div class="control-sidebar-bg shadow white fixed"></div>
</div>
<!-- Footer -->
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
</html>
@yield('js')
