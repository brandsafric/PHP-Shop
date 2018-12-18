@extends('layouts.master')

@section('title')Order review @endsection

@section('content')
    @if(!\App\Models\ProductOrder::haveUnfinishedOrders())
        <p>Your card is empty</p>
    @else
    <form id="checkout-form" class="clearfix" method="post">
        <div class="col-md-6">
            <div class="billing-details">
                <p>Already a customer ? <a href="/signin">Login</a></p>
                <div class="section-title">
                    <h3 class="title">Billing Details</h3>
                </div>
                <div class="form-group">
                    <input class="input" type="text" name="first-name" placeholder="First Name" value="{{ old('first-name', $user ? $user->firstname : '') }}">
                </div>
                <div class="form-group">
                    <input class="input" type="text" name="last-name" placeholder="Last Name" value="{{ old('last-name', $user ? $user->lastname : '') }}">
                </div>
                <div class="form-group">
                    <input class="input" type="email" name="email" placeholder="Email" value="{{ old('email', $user ? $user->email : '') }}">
                </div>
                <div class="form-group">
                    <input class="input" type="text" name="address" placeholder="Address" value="{{ old('address', $user ? $user->address : '') }}">
                </div>
                <div class="form-group">
                    <input class="input" type="text" name="city" placeholder="City" value="{{ old('city', $user ? $user->city : '') }}">
                </div>
                <div class="form-group">
                    <input class="input" type="text" name="zip-code" placeholder="ZIP Code" value="{{ old('zip-code', $user ? $user->zip : '') }}">
                </div>
                <div class="form-group">
                    <input class="input" type="tel" name="tel" placeholder="Telephone" value="{{ old('tel', $user ? $user->phone : '') }}">
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="shiping-methods">
                <div class="section-title">
                    <h4 class="title">Shiping Methods</h4>
                </div>
                <div class="order-summary clearfix">
                    <table class="shopping-cart-table table">
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th class="empty" colspan="3"></th>
                            <th>SUBTOTAL</th>
                            <th colspan="2" class="sub-total">{{ printPrice($total_price) }}</th>
                        </tr>
                        <tr>
                            <th class="empty" colspan="3"></th>
                            <th>SHIPING</th>
                            <td colspan="2">Free Shipping</td>
                        </tr>
                        <tr>
                            <th class="empty" colspan="3"></th>
                            <th>TOTAL</th>
                            <th colspan="2" class="total">{{ printPrice($total_price) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                    <div class="pull-right">
                        <button class="primary-btn">Finish Order</button>
                    </div>
                </div>
            </div>
        </div>
        {!! csrf() !!}
    </form>
    @endif
@endsection

@section('scripts')


@endsection