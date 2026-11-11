@extends('layouts.app')

{{-- Marco común de las pantallas internas: sidebar, barra superior, título y errores. --}}
@section('content')
<div id="wrapper">
    @include('layouts.sidebar')

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            @include('layouts.navbar')

            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <h1 class="h3 mb-2 text-gray-800">@yield('title')</h1>
                    <div>@yield('actions')</div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('panel')
            </div>
        </div>
    </div>
</div>
@endsection
