@extends('master')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please Sign In</h3>
                </div>
                <div class="panel-body">
                    {!! Form::open([
                        'route' => 'postLogin',
                        'role'  => 'form',
                        'method' => 'POST',
                    ]) !!}
                        @if (count($errors) > 0)
                            <div class="alert alert-warning">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                {!! $errors->first() !!}
                            </div>
                        @endif
                        <fieldset>
                            <div class="form-group">
                                {!! Form::text('email', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'E-mail'
                                ]) !!}
                            </div>
                            <div class="form-group">
                               {!! Form::password('password',[
                                   'class' => 'form-control',
                                   'placeholder' => 'Password'
                               ]) !!}
                            </div>
                            <div class="checkbox">
                                <label>
                                    {{-- <input name="remember" type="checkbox" value="Remember Me">Remember Me --}}
                                    {!! Form::checkbox('remember', 1, null,[
                                        'value' => 'Remember Me'
                                    ]) !!} Remember Me
                                </label>
                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            {!! Form::submit('Login', ['class' => 'btn btn-lg btn-success btn-block']) !!}
                        </fieldset>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop