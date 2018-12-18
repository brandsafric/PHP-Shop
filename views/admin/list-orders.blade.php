@extends('layouts.admin')

@section('title')Orders @endsection

@section('styles')
    <style>
        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
            padding-right: 5px !important;
            padding-left: 5px !important;
        }
        a{
            font-weight: bold;
        }
        .top-div{
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="col-md-12 top-div">
            <form action="" class="form-horizontal">
                <label for="order-id" class="col-sm-1 control-label">Order ID</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" name="order-id" autocomplete="off"@if(isset($_GET['order-id'])) value="{{ $_GET['order-id']}}"@endif>
                </div>
                <label for="status" class="col-sm-1 control-label">Status</label>
                <div class="col-sm-3">
                    <select name="status" id="status" class="form-control">
                        <option value="">Any</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @if(isset($_GET['status']) && $_GET['status']==$status) selected @endif>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary">Show</button>
            </form>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <th>Order</th>
                                <th>Delivery to</th>
                                <th>Num Products</th>
                                <th>Date</th>
                                <th>Price</th>
                                <th class="text-center">Status</th>
                                <th>View</th>
                            </tr>
                            @foreach($admin_orders as $order)
                            <tr>
                                <td><a href="/admin/orders/{{ $order->id }}">#{{ $order->id }}</a> by <a href="/admin/order/{{ $order->firstname . ' ' . $order->lastname }}">{{ $order->firstname . ' ' . $order->lastname }}</a><br>{{ $order->email }}</td>
                                <td>{{ $order->address }}<br>{{ $order->city }}</td>
                                <td>{{ $order->num_items }}</td>
                                <td>{{ $order->created_at }}</td>
                                <td>{{ printPrice($order->total_price) }}</td>
                                <td class="text-center"><span class="badge {{ \App\Models\Order::getStatusBadgeColor($order->status) }}">{{ $order->status }}</span></td>
                                <td><a href="/admin/orders/{{ $order->id }}" class="btn btn-default btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> </a></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <div class="col-md-12 text-center" style="padding-right: 0; padding-left: 0">
                    {!! $pagination !!}
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
@endsection

@section('scripts')
@endsection