@extends('layouts.master')

@section('title'){{ $product->title }}@endsection

@section('styles')
    <style>
        .size-option > li > a{
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <!--  Product Details -->
    <div class="product product-details clearfix">
        <div class="col-md-6">
            <div id="product-main-view">
                @if(count($pictures)>0)
                    @foreach($pictures as $picture)
                    <div class="product-view">
                        <img src="{{ $picture->path }}" alt="">
                    </div>
                    @endforeach
                @else
                    <div class="product-view">
                        <img src="/placeholder.jpg" alt="">
                    </div>
                @endif

            </div>
            <div id="product-view">
                @foreach($thumbnails as $thumbnail)
                <div class="product-view">
                    <img src="{{$thumbnail}}" alt="">
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-6">
            <div class="product-body">
                <div class="product-label">
                @if(\App\Models\Product::isNewProduct($product))
                    <span>New</span>
                @endif
                @if($product->promo_price)
                        <span class="sale">-{{round(($product->price - $product->promo_price) / ($product->price / 100))}}%</span>
                @endif
                </div>
                <h2 class="product-name">{{$product->title}}</h2>
                    <h3 class="product-price">
                        @if($product->promo_price && $product->promo_price!=0)
                            {{printPrice($product->promo_price)}}
                            <del class="product-old-price">{{printPrice($product->price)}}</del>
                        @elseif(!$product->promo_price)
                            {{printPrice($product->price)}}
                        @endif
                    </h3>
                <div>
                    @if($product->rating!=null)
                        <div class="product-rating">
                            @for($i=0; $i<round($product->rating); $i++)
                                <i class="fa fa-star"></i>
                            @endfor
                            @for($i=round($product->rating); $i<5; $i++)
                                <i class="fa fa-star-o empty"></i>
                            @endfor
                        </div>
                    @endif
                    <a data-toggle="tab" href="#tab2" id="add-review">{{ count($reviews) }} Review(s) / Add Review</a>
                </div>
                <p><strong>Availability:</strong> {{$product->availability}}</p>
                <p><strong>Brand:</strong> E-SHOP</p>
                {!! $product->description !!}
                <form action="/order" method="post">
                    @if($product->variation_name)
                    <div class="product-options">
                        <ul class="size-option">
                            <li><span class="text-uppercase">{{$product->variation_name}}:</span></li>
                            @foreach(explode('|', $product->variation_values) as $variation)
                            <li><a>{{$variation}}</a></li>
                            @endforeach
                            <input type="hidden" name="variation" id="variation">
                        </ul>
                    </div>
                    @endif
                        <input type="hidden" name="product-id" value="{{ $product->id }}">
                    <div class="product-btns">
                        <div class="qty-input">
                            <span class="text-uppercase">QTY: </span>
                            <input class="input" name="qty" type="number" min="1" value="1">
                        </div>
                        <button class="primary-btn add-to-cart"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                        <div class="pull-right">
                            <button class="main-btn icon-btn"><i class="fa fa-heart"></i></button>
                            <button class="main-btn icon-btn"><i class="fa fa-exchange"></i></button>
                            <button class="main-btn icon-btn"><i class="fa fa-share-alt"></i></button>
                        </div>
                    </div>
                    {!! csrf() !!}
                </form>
            </div>
        </div>
        <div class="col-md-12">
            <div class="product-tab">
                <ul class="tab-nav">
                    <li class="active"><a data-toggle="tab" href="#tab1">Description</a></li>
                    <li><a data-toggle="tab" href="#tab1">Details</a></li>
                    <li><a data-toggle="tab" href="#tab2" id="tab2-link">Reviews ({{ count($reviews) }})</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab1" class="tab-pane fade in active">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute
                            irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    </div>
                    <div id="tab2" class="tab-pane fade in">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="product-reviews">
                                    @foreach($reviews as $review)
                                    <div class="single-review">
                                        <div class="review-heading">
                                            <div><a href="#"><i class="fa fa-user-o"></i> {{ $review->name }}</a></div>
                                            <div><a href="#"><i class="fa fa-clock-o"></i> {{ date('d M Y / G:i A', strtotime($review->created_at)) }}</a></div>
                                            <div class="review-rating pull-right">
                                                @for($i=0; $i<$review->rating; $i++)
                                                    <i class="fa fa-star"></i>
                                                @endfor
                                                @for($i=$review->rating; $i<5; $i++)
                                                    <i class="fa fa-star-o empty"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="review-body">
                                            <p>{{ $review->text }}</p>
                                        </div>
                                    </div>
                                    @endforeach

                                    <ul class="reviews-pages">
                                        <li class="active">1</li>
                                        <li><a href="#">2</a></li>
                                        <li><a href="#">3</a></li>
                                        <li><a href="#"><i class="fa fa-caret-right"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="text-uppercase">Write Your Review</h4>
                                <p>Your email address will not be published.</p>
                                <form class="review-form" method="post" action="/products/{{ $product->id }}/add-review">
                                    <div class="form-group">
                                        <input class="input" type="text" name="name" placeholder="Your Name" value="{{ old('name', (\Core\Data::userIsLoggedIn() ? $user->firstname . ' ' . $user->lastname  : ''))}}"/>
                                    </div>
                                    <div class="form-group">
                                        <input class="input" type="email" name="email" placeholder="Email Address" value="{{ old('email' , \Core\Data::userIsLoggedIn() ?  $user->email : '') }}"/>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="input" name="text" placeholder="Your review">{{ old('text') }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-rating">
                                            <strong class="text-uppercase">Your Rating: </strong>
                                            <div class="stars">
                                                <input type="radio" id="star5" name="rating" value="5" /><label for="star5"></label>
                                                <input type="radio" id="star4" name="rating" value="4" /><label for="star4"></label>
                                                <input type="radio" id="star3" name="rating" value="3" /><label for="star3"></label>
                                                <input type="radio" id="star2" name="rating" value="2" /><label for="star2"></label>
                                                <input type="radio" id="star1" name="rating" value="1" /><label for="star1"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="primary-btn">Submit</button>
                                    {!! csrf() !!}
                                </form>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /Product Details -->
    </div>
@endsection

@section('scripts')
    <script>
        $('.size-option a').on('click', function () {
            $('.size-option li').removeClass('active');
            $(this).parent().addClass('active');
            $('#variation').val($(this).text());
        })
        $('#add-review').on('click',function () {
            $('#tab2-link').click();
            $('html, body').animate({
                scrollTop: $("#tab2-link").offset().top
            }, 1000);
        })
    </script>
@endsection