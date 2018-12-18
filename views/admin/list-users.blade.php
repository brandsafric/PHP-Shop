@extends('layouts.admin')

@section('title')List Users @endsection

@section('styles')
    <style>
        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
            padding-right: 2px !important;
            padding-left: 2px !important;
        }
    </style>
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">List Users</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Confirm</th>
                            <th>Sex</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Save</th>
                            <th>Profile</th>
                            <th></th>
                        </tr>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <form action="/admin/users/{{ $user->id }}" method="post">
                                <td><input type="text" class="form-control input-sm" name="email" value="{{ $user->email }}"></td>
                                <td><input type="text" class="form-control input-sm" name="username" value="{{ $user->username }}"></td>
                                <td><input type="password" class="form-control input-sm" name="password"></td>
                                <td><input type="password" class="form-control input-sm" name="password-confirm"></td>

                                <td><select name="sex" class="form-control input-sm">
                                        @foreach($sex as $s)
                                            <option value="{{ $s }}" @if($s==$user->sex) selected @endif>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><select name="role" class="form-control input-sm">
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}" @if($role==$user->role) selected @endif>{{ $role }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><select name="status" class="form-control input-sm">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" @if($status==$user->status) selected @endif>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                {!! csrf() !!}
                                <td><button class="btn btn-success btn-sm"><i class="fa fa-edit" aria-hidden="true"></i> Save</button></td>
                                <input type="hidden" name="_method" value="PUT">
                                {!! csrf() !!}
                            </form>
                            <td><a href="/admin/users/{{ $user->id }}/edit" class="btn btn-default btn-sm"><i class="fa fa-user" aria-hidden="true"></i> View Profile</a></td>
                            <td>
                                <form action="/admin/users/{{ $user->id }}" method="post">
                                    <button class="btn btn-danger btn-sm"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                    <input type="hidden" name="_method" value="DELETE">
                                    {!! csrf() !!}
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
@endsection

@section('scripts')
@endsection