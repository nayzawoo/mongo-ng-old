<!DOCTYPE html>
<html lang="en" ng-app="MongoApp" ng-controller="MainController">
<head>
{{-- <base href="/"> --}}
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">

<title>MongoMyAdmin</title>
{!!
css([
    'bower_components/bootstrap/dist/css/bootstrap.min',
    // 'css/slate',
    'css/material',
    'bower_components/angular-ui-router-anim-in-out/css/anim-in-out',
    'bower_components/bootstrap-material-design/dist/css/ripples.min',
    //'bower_components/metisMenu/dist/metisMenu.min',
    'bower_components/font-awesome/css/font-awesome.min',
    'css/sb-admin-2',
    'bower_components/sweetalert/dist/sweetalert',
    // 'bower_components/sweetalert/themes/twitter/twitter',
    // AddOns
    'bower_components/codemirror/lib/codemirror',
    'bower_components/codemirror/addon/fold/foldgutter',
    'bower_components/codemirror/addon/scroll/simplescrollbars',
    'bower_components/codemirror/addon/lint/lint',
    'bower_components/codemirror/addon/display/fullscreen',
    'bower_components/codemirror/theme/material',
    'bower_components/angular-loading-bar/build/loading-bar.min',
    'css/prism',
    'css/style',
    'css/breadcrumb',
    ]);
!!}
@yield("css")

</head>
<body>
@yield('content')
{!!js([
    'bower_components/jquery/dist/jquery.min',
    'bower_components/bootstrap/dist/js/bootstrap.min',
    'bower_components/bootstrap-material-design/dist/js/ripples.min',
    'bower_components/bootstrap-material-design/dist/js/material.min',
    'bower_components/sweetalert/dist/sweetalert.min',
    'bower_components/angular/angular.min',
    'bower_components/angular-animate/angular-animate.min',
    'bower_components/angular-ui-router/release/angular-ui-router.min',
    'bower_components/angular-ui-router-anim-in-out/anim-in-out',
    'bower_components/angular-breadcrumb/dist/angular-breadcrumb.min',
    'bower_components/angular-bootstrap/ui-bootstrap-tpls.min',
    'bower_components/angular-bootstrap/ui-bootstrap.min',
    'bower_components/angular-sanitize/angular-sanitize',
    'bower_components/codemirror/lib/codemirror',
    'bower_components/underscore/underscore-min',
    // Codemirror AddOns
    'js/jsonlint',
    'bower_components/codemirror/addon/fold/foldcode',
    'bower_components/codemirror/addon/scroll/simplescrollbars',
    'bower_components/codemirror/addon/display/autorefresh',
    'bower_components/codemirror/addon/display/fullscreen',
    'bower_components/codemirror/addon/fold/foldgutter',
    'bower_components/codemirror/addon/fold/brace-fold',
    'bower_components/codemirror/addon/lint/lint',
    'bower_components/codemirror/addon/lint/json-lint',
    'bower_components/codemirror/keymap/sublime',
    'bower_components/angular-loading-bar/build/loading-bar.min',
    'bower_components/codemirror/mode/javascript/javascript',
    'bower_components/angular-ui-codemirror/ui-codemirror',
    'js/prism',
    'js/sb-admin-2',
    'js/helpers/filesize.min',
    'js/App',
    'js/esprima',
    'js/GenghisJSON',
    'js/helpers/helper',
    'js/filters/filter',
    'js/controllers/MainController',
    'js/controllers/HomeController',
    'js/controllers/SidebarController',
    'js/controllers/DatabaseBrowserController',
    'js/controllers/CollectionBrowserController',
    'js/controllers/DocumentEditorController',
    'js/services/ApiService',
    'js/directives/directives',
    ])!!}
@yield("script")
</body>
</html>
