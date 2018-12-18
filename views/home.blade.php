@extends('layouts.master')

@section('title'){{ $title }}@endsection

@section('content')
    <div class="row">
        <!-- section title -->
        <div class="col-md-12">
            <div class="section-title">
                <h2 class="title">{{ $header }}</h2>
            </div>
        </div>
        <!-- section title -->

        @if(!$homepage)
        <div class="row">
            <div class="col-sm-4 col-md-10">
                <form action="/set-sort-type" method="post" id="form-sort" class="form-horizontal">
                    <div class="form-group col-sm-6 col-md-4">
                        <label for="sort" class="col-sm-3 col-md-4 control-label text-right">Sort by</label>

                        <div class="col-sm-8">
                            <select name="sort" id="sort" class="form-control">
                                <option value="newest"@if($sort=='newest') selected @endif>Newest</option>
                                <option value="lower-price"@if($sort=='lower-price') selected @endif>Lower Price</option>
                                <option value="higher-price"@if($sort=='higher-price') selected @endif>Higher Price</option>
                            </select>
                        </div>
                        <button class="btn" id="btn-sort"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                    <div class="form-group col-sm-6 col-md-4">
                        <label for="products-per-page" class="col-sm-3 col-md-4 control-label text-right">Sort by</label>

                        <div class="col-sm-4">
                            <select name="products-per-page" id="products-per-page" class="form-control">
                                <option value="12"@if($products_per_page=='12') selected @endif>12</option>
                                <option value="24"@if($products_per_page=='24') selected @endif>24</option>
                                <option value="36"@if($products_per_page=='36') selected @endif>36</option>
                            </select>
                        </div>
                    </div>
                    {!! csrf() !!}
                </form>
            </div>
        </div>
        @endif
        @foreach($products as $key=>$product)
        <!-- Product Single -->
        @if($key % 4==0)
            <div class="col-md-12">
        @endif
        <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="product product-single">
                <a href="/products/{{$product->id}}">
                <div class="product-thumb">
                    <div class="product-label">
                    @if(\App\Models\Product::isNewProduct($product))
                        <span>New</span>
                    @endif
                    @if($product->promo_price && $product->promo_price!=0)
                        <span class="sale">-{{round(($product->price - $product->promo_price) / ($product->price / 100))}}%</span>
                    @endif
                    </div>
                    <button type="button" class="main-btn quick-view"><i class="fa fa-search-plus"></i> Quick view</button>
                    @if($product->picture_id != null)
                        <img src="{{\Core\Model::query("SELECT thumbnails.path FROM thumbnails WHERE picture_id='$product->picture_id'")[0]->path}}" alt="">
                    @else
                        <img src="/placeholder.jpg" alt="">
                    @endif
                </div>
                </a>
                <div class="product-body">
                    <h3 class="product-price">
                        @if(!$product->promo_price)
                            {{printPrice($product->price)}}
                        @elseif($product->promo_price && $product->promo_price!=0)
                            <del class="product-old-price">{{printPrice($product->price)}}</del>
                            {{printPrice($product->promo_price)}}
                        @endif
                    </h3>
                    <div class="product-rating">
                        @if(!$product->rating==null)
                            @for($i=0; $i<round($product->rating); $i++)
                                <i class="fa fa-star"></i>
                            @endfor
                            @for($i=round($product->rating); $i<5; $i++)
                                <i class="fa fa-star-o empty"></i>
                            @endfor
                        @endif
                    </div>
                    <h2 class="product-name"><a href="/products/{{$product->id}}">{{$product->title}}</a></h2>
                    <form action="/order" method="post" class="form-horizontal">
                        <div class="product-btns">
                            <button type="button" class="main-btn icon-btn"><i class="fa fa-heart"></i></button>
                            <button type="button" class="main-btn icon-btn"><i class="fa fa-exchange"></i></button>
                            @if($product->variation_name)
                                <a href="/products/{{ $product->id }}" class="primary-btn add-to-cart"><i class="fa fa-cog" aria-hidden="true"></i> Options</a>
                            @else
                                <input name="qty" type="hidden" value="1">
                                <input type="hidden" name="product-id" value="{{ $product->id }}">
                                {!! csrf() !!}
                                <button type="submit" class="primary-btn add-to-cart"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Product Single -->
        @if(($key + 1)%4==0)
        </div>
        @endif
    @endforeach
    </div>
    @if($pagination)
        <div class="col-md-4 text-center" style="padding-right: 0; padding-left: 0">
            {!! $pagination !!}
        </div>
    @endif
    </div>
@endsection

@section('scripts')
    <script>
        $( document ).ready(function() {
           $('#btn-sort').hide();
        });
        $('#sort').on('change', function () {
            $('#form-sort').submit();
        })
        $('#products-per-page').on('change', function () {
            $('#form-sort').submit();
        })
    </script>
@endsection

