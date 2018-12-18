@extends('layouts.master')

@section('title')Edit Profile @endsection

@section('content')
    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            <form id="signupform" class="form-horizontal" role="form" method="post" action="edit-profile">
            <div class="panel-heading">
                <div class="panel-title"> &nbsp Промяна на данните</div>
            </div>
            <div class="panel-body" >
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#home">General</a></li>
                    <li><a data-toggle="tab" href="#menu1">Billing Details</a></li>
                </ul>

                <div class="tab-content">
                    <div id="home" class="tab-pane fade in active">

                            <div class="form-group row">
                                <label for="email" class="col-md-4 control-label">Email</label>
                                <div class="col-md-8">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" required value="{{ old('email',user()->email) }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="username" class="col-md-4 control-label">Username</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="username" id="username" minlength="4" maxlength="15" placeholder="Username" required value="{{ old('username',user()->username) }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="pass1" class="col-md-4 control-label">Password</label>
                                <div class="col-md-8">
                                    <input type="password" class="form-control" name="pass1" id="pass1" minlength="6" placeholder="Password">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="pass2" class="col-md-4 control-label">Password Confirm</label>
                                <div class="col-md-8">
                                    <input type="password" class="form-control" name="pass2" id="pass2" minlength="6" placeholder="Password"} >
                                </div>
                            </div>
                            {!! csrf() !!}
                            <input type="hidden" name="_method" value="PUT">
                    </div>
                    <div id="menu1" class="tab-pane fade">
                        <div class="billing-details">
                            <div class="section-title">
                                <h3 class="title">Billing Details</h3>
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="firstname" placeholder="First Name" value="{{ old('firstname', user()->firstname) }}">
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="lastname" placeholder="Last Name" value="{{ old('lastname', user()->lastname) }}">
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="address" placeholder="Address" value="{{ old('address', user()->address) }}">
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="city" placeholder="City" value="{{ old('city', user()->city) }}">
                            </div>
                            <div class="form-group">
                                <input class="input" type="number" name="zip-code" placeholder="ZIP Code" value="{{ old('zip-code', user()->zip) }}">
                            </div>
                            <div class="form-group">
                                <input class="input" type="tel" name="tel" placeholder="Telephone" value="{{ old('tel', user()->phone) }}">
                            </div>
                            <div class="form-group">
                                <div class="radio">
                                    <label><input type="radio" name="sex"@if(user()->sex == 'male') checked @endif value="male">Male</label>
                                </div>
                                <div class="radio">
                                    <label><input type="radio" name="sex"@if(user()->sex == 'female') checked @endif value="female">Female</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pull-right">
                    <!-- Button -->
                    <div class="col-md-12">
                        <button id="btn-signup" type="submit" class="btn btn-primary float-right"><i class="icon-hand-right"></i>Промени </button>
                    </div>
                </div>

            </div>
            </form>
        </div>
    </div>
@endsection
