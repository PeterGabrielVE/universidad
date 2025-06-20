@extends('layouts.app')

@section('content')
<div id="wrapper">
    <!-- Sidebar -->
    @include('layouts.sidebar')
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column w-100">
        <!-- Main Content -->
        <div id="content">
            <!-- Topbar -->
            @include('layouts.navbar')
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="px-4 py-4 w-full container-fluid">
                <div id="app" class="w-full max-w-none">
                    <users-index></users-index>
                </div>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->
@endsection
