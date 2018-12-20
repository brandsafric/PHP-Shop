@extends('layouts.master')

@section('title')Signin @endsection

@section('content')
    <form class="form-signin" method="post" action="/forgot-password">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="Email" required autofocus>
        {!! csrf() !!}
        <button class="btn btn-lg btn-primary btn-block" type="submit">Send</button>
    </form>
@endsection
