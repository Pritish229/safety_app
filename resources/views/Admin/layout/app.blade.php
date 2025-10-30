<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Organiztion Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>@yield('title', 'AdminLTE 3 | Starter')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">
    <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.min.css')}}">
    <!-- CodeMirror -->
     <link rel="stylesheet" href="{{asset('plugins/codemirror/codemirror.css') }}">
     <link rel="stylesheet" href="{{asset('plugins/codemirror/theme/monokai.css')}} ">
     <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    @yield('styles')
</head>

<body class="sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed control-sidebar-slide-open ">
    <div class="wrapper">
        @include('Admin.layout.sidebar')
        @include('Admin.layout.navbar')
        <div class="content-wrapper">
        @yield('content')
        </div>
        @include('Admin.layout.footer')
    </div>
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="{{asset('plugins/select2/js/select2.full.min.js') }}"></script>
    
    @yield('scripts')
</body>


<style>
    
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
	color: #fff;
	background-color: #060607;
}
    .note-group-select-from-files {
  display: none;
    }
  .content-wrapper {
	background-color: #dde1ea;
}
.responsive-text{
  white-space: nowrap;       /* Prevent text from wrapping to the next line */
  overflow: hidden;         /* Hide overflowing content */
  text-overflow: ellipsis;  /* Display an ellipsis for overflowed text */
  max-width: 150px;
}

/* .main-sidebar{
    overflow-y: auto
} */
.select2-container .select2-selection--single {
    height: 38px; /* Adjust the height as needed */
    color:#495057;
    border: 1px solid #ced4da;
}
.form-control:focus{
      box-shadow: none;
    }

    .select2-container--bootstrap4.select2-container--focus .select2-selection {
        box-shadow: none;
    }

.tost-success{
    color:rgb(217, 233, 217);
    
}


/* width */
::-webkit-scrollbar {
  width: 10px;
  height: 12px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1; 
}
 
/* Handle */
::-webkit-scrollbar-thumb {
  background: #888; 
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555; 
}

</style>
</html>
