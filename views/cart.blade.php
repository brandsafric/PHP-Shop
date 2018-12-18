@extends('layouts.master')

@section('title')Order review @endsection

@section('content')
    <div class="col-md-12">
        <div class="order-summary clearfix">
                <div class="section-title">
                    <h3 class="title">Order Review</h3>
                </div>
                <table class="shopping-cart-table table">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th></th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Total</th>
                        <th class="text-right"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                            <tr>
                                <td class="thumb"><img src="{{ $order->path ? : '/placeholder.jpg' }}" alt=""></td>
                                <td class="details">
                                    <a href="/products/{{ $order->product_id }}">{{ $order->title }}</a>
                                    <ul>
                                        <li><span>@if($order->variation_name)<span>{{ $order->variation_name }}: {{ $order->variation }}</span>@endif</span></li>
                                    </ul>
                                </td>
                                <td class="price text-center"><strong>{{ printPrice($order->promo_price!=null ? $order->promo_price : $order->price) }}</strong>@if($order->promo_price!=null)<br><del class="font-weak"><small>{{ printPrice($order->price) }}</small></del>@endif</td>
                                <td class="qty text-center">
                                    <form action="/cart/{{ $order->id }}" method="post">
                                        <input class="input" name="qty" type="number" min="1" value="{{ $order->qty }}">
                                        <button class="btn btn-default"><i class="fa fa-floppy-o" aria-hidden="true"></i> Update</button>
                                        {!! csrf() !!}
                                        <input type="hidden" name="_method" value="PUT">
                                    </form>
                                </td>
                                <td class="total text-center"><strong class="primary-color">{{ printPrice($order->promo_price!=null ? $order->promo_price * $order->qty : $order->price * $order->qty) }}</strong></td>
                                <td class="text-right">
                                    <form action="/cart/{{ $order->id }}" method="post">
                                        <button class="main-btn icon-btn"><i class="fa fa-close"></i></button>
                                        {!! csrf() !!}
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                </td>
                            </tr>
                    @endforeach
                    </tbody>
                    <tfoot style="border-top: 1px solid #DADADA;">
                    <tr>
                        <th class="empty" colspan="3"></th>
                        <th>SUBTOTAL</th>
                        <th colspan="2" class="sub-total">$97.50</th>
                    </tr>
                    <tr>
                        <th class="empty" colspan="3"></th>
                        <th>SHIPING</th>
                        <td colspan="2">Free Shipping</td>
                    </tr>
                    <tr>
                        <th class="empty" colspan="3"></th>
                        <th>TOTAL</th>
                        <th colspan="2" class="total">{{printPrice(\App\Models\ProductOrder::getTotalPrice($orders))}}</th>
                    </tr>
                    </tfoot>
                </table>
            <div class="pull-right">
                <a href="checkout" class="primary-btn">Checkout</a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')


@endsection