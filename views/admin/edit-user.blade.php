@extends('layouts.admin')

@section('title')Edit User @endsection

@section('styles')
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit User</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form class="form-horizontal" method="post" action="/admin/users/{{ $user->id }}">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="firstname" class="col-sm-2 control-label">First Name</label>

                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Firstname" value="{{ old('', $user->firstname) }}">
                                </div>
                                <label for="lastname" class="col-sm-2 control-label">Last Name</label>

                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Lastname" value="{{ old('lastname', $user->lastname) }}">
                                </div>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group">
                                <label for="email" class="col-sm-2 control-label">Email</label>

                                <div class="col-sm-4">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email', $user->email) }}">
                                </div>
                                <label for="username" class="col-sm-2 control-label">Username</label>

                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="{{ old('username', $user->username) }}">
                                </div>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group">
                                <label for="address" class="col-sm-2 control-label">Address</label>

                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="address" id="address" placeholder="Address" value="{{ old('address', $user->address) }}">
                                </div>
                                <label for="city" class="col-sm-2 control-label">City</label>

                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="city" id="city" placeholder="City" value="{{ old('city', $user->city) }}">
                                </div>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group">
                                <label for="zip" class="col-sm-2 control-label">Zip</label>

                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="zip" id="zip" placeholder="Zip" value="{{ old('zip', $user->zip) }}">
                                </div>
                                <label for="phone" class="col-sm-2 control-label">Phone</label>

                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" value="{{ old('phone', $user->phone) }}">
                                </div>
                            </div>
                        </div>


                        <div class="box-body">
                            <div class="form-group">
                                <label for="password" class="col-sm-2 control-label">Password</label>

                                <div class="col-sm-2">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" value="{{ old('password') }}">
                                </div>

                                <div class="col-sm-2">
                                    <input type="password" class="form-control" name="password-confirm" placeholder="Confirm" value="{{ old('password') }}">
                                </div>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group">
                                <label for="sex" class="col-sm-2 control-label">Sex</label>

                                <div class="col-sm-2">
                                    <select name="sex" id="sex" class="form-control">
                                        @foreach($sex as $s)
                                            <option value="{{ $s }}" @if($sex==$user->sex) selected @endif >{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <label for="role" class="col-sm-2 control-label">Role</label>

                                <div class="col-sm-2">
                                    <select name="role" id="role" class="form-control">
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}" @if($role==$user->role) selected @endif >{{ $role }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <label for="status" class="col-sm-2 control-label">Sex</label>

                                <div class="col-sm-2">
                                    <select name="status" id="status" class="form-control">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" @if($status==$user->status) selected @endif >{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="submit" class="btn btn-default pull-right">Save</button>
                        </div>
                        <input type="hidden" name="_method" value="PUT">
                        {!! csrf() !!}
                        <!-- /.box-footer -->
                    </form>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
@endsection

@section('scripts')
@endsection