<!DOCTYPE html>
<html lang="en" ng-app="omiAdmin" ng-controller="mainController">
<head>
<meta charset="utf-8"/>
<title>Open Myanmar </title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/fav.jpg') }}"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="Open Myanmar" name="description"/>
<meta content="MyanmarLinks" name="author"/>
{{-- <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/> --}}
@include('backend.partials.jsvars')
@include('backend.partials.ie-support')
{!!
css([
    'bootstrap/dist/css/bootstrap.min',
    'fontawesome/css/font-awesome.min',
    'jquery.uniform/themes/default/css/uniform.default.min',
    'ng-tags-input/ng-tags-input.min',
    'toastr/toastr',
    'bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min',
    'pikaday/css/pikaday',
    'select2/dist/css/select2.min',

    // not yet
    'angular-ui-select/dist/select',
    ], 'bower_components/');
!!}

{!!js([
    'jquery/dist/jquery.min',
    'angular/angular.min',
    ], 'bower_components/')!!}

{!!
    css([
    'admin',
    ], 'css/');
!!}
@yield("styles")
@yield("headerjs")
</head>
<body class="page-md page-header-fixed page-quick-sidebar-over-content ">
@include('backend.partials.flash')
<!-- BEGIN HEADER -->
@include('backend.partials.navbar')
<!-- END HEADER -->
<div class="clearfix"></div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
   @include('backend.partials.left-sidebar')
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            @include('backend.partials.modal')
            <!-- /.modal -->
            <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <!-- BEGIN PAGE HEADER-->
            <h3 class="page-title">@yield("title", "Page Title")</h3>
            <div class="page-bar">
                @include('backend.partials.breadcrumb')
                @include('backend.partials.page-toolbar')
            </div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    @yield('content')
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
    <!-- BEGIN QUICK SIDEBAR -->
    @include('backend.partials.right-sidebar')
    <!-- END QUICK SIDEBAR -->
</div>
<!-- END CONTAINER -->
{!!js([
    'jquery-migrate/jquery-migrate.min',
    'jquery-ui/jquery-ui.min',
    'bootstrap/dist/js/bootstrap.min',
    'bootstrap-hover-dropdown/bootstrap-hover-dropdown.min',
    'jquery-slimscroll/jquery.slimscroll.min',
    'blockui/jquery.blockUI',
    'jquery-cookie/jquery.cookie',
    'jquery.uniform/jquery.uniform.min',
    'bootstrap-switch/dist/js/bootstrap-switch.min',
    'ng-tags-input/ng-tags-input.min',
    'toastr/toastr',
    'moment/moment',
    'ckeditor/ckeditor',
    'pikaday/pikaday',
    'pikaday/plugins/pikaday.jquery',
    'angular-ui-select/dist/select',
    'select2/dist/js/select2.full.min',
    'ng-file-upload/ng-file-upload-shim.min',
    'ng-file-upload/ng-file-upload.min',
    ], 'bower_components/')!!}

{!!js([
    'metronic',
    'layout',
    'datatable',
    'quick-sidebar',
    'admin',
    ], 'js/')!!}
@yield("footerjs")
</body>
</html>
