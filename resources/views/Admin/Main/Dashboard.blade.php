@extends('Admin.layout.app')

@section('title', 'Dashboard')

@section('body_class', 'hold-transition sidebar-mini')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div>
</div>



<div class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Welcome Card -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Welcome</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Welcome to the Safety App</h6>
                        <p class="card-text">
                            This is your dashboard. Use the navigation to access modules, view reports, and manage settings.
                        </p>
                        <a href="#" class="btn btn-primary">Get Started</a>
                    </div>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->
@endsection