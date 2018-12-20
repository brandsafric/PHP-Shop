@extends('layouts.master')

@section('title')Signin @endsection

@section('content')
    <form class="form-signin" method="post">
        <h3 class="form-signin-heading">Enter your new password</h3>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="password" id="pass1" name="pass1" class="form-control" placeholder="Password" required autofocus>
        <input type="password" id="pass2" name="pass2" class="form-control" placeholder="Password Confirmation" required autofocus>
        {!! csrf() !!}
        <button class="btn btn-lg btn-primary btn-block" type="submit">Send</button>
    </form>
@endsection
