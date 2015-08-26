@extends('master')

@section('content')
<div id="wrapper" class="ng-cloak" ng-cloak>
  @include('partials.nav')
  <div id="page-wrapper">
    <div  ncy-breadcrumb></div>
    <div class="row">
      <div class="col-lg-12">
      </div>
    </div>
    {{-- end of breadcrumb --}}
    <div class="row">
      <div class="col-sm-12">
        <div ui-view></div>
      </div>
    </div>
    <!-- /.row -->
  </div>
  <!-- /#page-wrapper -->
</div>
@stop