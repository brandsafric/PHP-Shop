@extends('layouts.admin')

@section('title')Edit Order @endsection

@section('styles')
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Order</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form method="post" class="form-horizontal" action="/admin/orders/{{ $admin_orders->id }}">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="firstname" class="col-sm-2 control-label">First Name</label>

                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Firstname" value="{{ old('firstname', $admin_orders->firstname) }}">
                                    </div>
                                    <label for="lastname" class="col-sm-2 control-label">Last Name</label>

                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Lastname" value="{{ old('lastname', $admin_orders->lastname) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="city" class="col-sm-2 control-label">City</label>

                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="city" id="city" placeholder="City" value="{{ old('city', $admin_orders->city) }}">
                                    </div>
                                    <label for="address" class="col-sm-2 control-label">Address</label>

                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="address" id="address" placeholder="Address" value="{{ old('address', $admin_orders->address) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="zip" class="col-sm-2 control-label">Zip</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="zip" id="address" placeholder="Zip" value="{{ old('zip', $admin_orders->zip) }}">
                                    </div>

                                    <label for="phone" class="col-sm-2 control-label">Phone</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" value="{{ old('phone', $admin_orders->phone) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="email" class="col-sm-2 control-label">Email</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email', $admin_orders->email) }}">
                                    </div>

                                    <label for="status" class="col-sm-2 control-label">Status</label>
                                    <div class="col-sm-4">
                                        <select name="status" id="status" class="form-control">
                                            @foreach(get_enum_values('orders', 'status') as $status)
                                                <option value="{{ $status }}"@if($status==$admin_orders->status) selected @endif>{{ $status }}</option>
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

        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-10">
                <div class="box box-primary">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <th>Product</th>
                                <th>Variation Name</th>
                                <th>Variation</th>
                                <th>Price</th>
                                <th width="90">Qty</th>
                                <th>Total</th>
                                <th>Save</th>
                            </tr>
                            @foreach($product_orders as $product_order)
                                <!-- form start -->
                                <form method="post" class="form-horizontal" action="/admin/productorders/{{ $product_order->id }}">
                                    <tr>
                                        <td>
                                            <div class="row" style="display: block">
                                                <div class="col-md-2 col-lg-1" style="min-width: 30px;">
                                                    <img src="{{ \App\Models\Thumbnail::getThumbnail($product_order->picture_id) }}" alt="" style="width: 100%; min-width: 30px;">
                                                </div>
                                                <div class="col-md-10 col-lg-11">
                                                    <span style="vertical-align: top;">{{ $product_order->title }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>@if($product_order->variation)<label for="variation-name" class="col-sm-1 control-label">{{ $product_order->variation_name }}</label>@endif </td>
                                        <td>@if($product_order->variation)
                                                <select name="variation-name" id="variation-name" class="form-control">
                                                    @foreach(explode('|', $product_order->variation_values) as $variation)
                                                        <option value="{{ $variation }}"@if($variation==$product_order->variation) selected @endif>{{ $variation }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </td>
                                        <td><span>{{ printPrice($product_order->price) }}</span></td>
                                        <td><input type="number" class="form-control text-center" name="qty" placeholder="Quantity" value="{{ old('qty', $product_order->qty) }}"></td>
                                        <td><span>{{ printPrice($product_order->price * $product_order->qty) }}</span></td>
                                        <td><button class="btn btn-success btn-sm btn-block"><i class="fa fa-pencil" aria-hidden="true"></i> Save</button></td>
                                    </tr>
                                    <input type="hidden" name="_method" value="PUT">
                                    {!! csrf() !!}
                                </form>
                            @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
@endsection