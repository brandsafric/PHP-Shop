@extends('layouts.master')

@section('title')Error @endsection
@section('styles')
    <style>
        #notfound{
            text-align: center;
        }
        h1{
            font-size: 202px;
            color: #ff9555;
            font-weight: lighter;
        }
        .notfound a {
            font-family: 'Kanit', sans-serif;
            color: #ff6f68;
            font-weight: 200;
            text-decoration: none;
            border-bottom: 1px dashed #ff6f68;
            border-radius: 2px;
        }
    </style>
@endsection

@section('content')
    <div class="col-md-6 col-md-offset-3">
    <div id="notfound">
        <div class="notfound">
            <div class="notfound-404">
                <h1>404</h1>
            </div>
            <h2>Oops! Nothing was found</h2>
            <p>The page you are looking for might have been removed had its name changed or is temporarily unavailable. <a href="{{ server() }}">Return to homepage</a></p>
            <div class="notfound-social">
                <a href="#"><i class="fa fa-facebook"></i></a>
                <a href="#"><i class="fa fa-twitter"></i></a>
                <a href="#"><i class="fa fa-pinterest"></i></a>
                <a href="#"><i class="fa fa-google-plus"></i></a>
            </div>
        </div>
    </div>
    </div>
@endsection

