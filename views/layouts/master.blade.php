<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title>@yield('title') | {{ $site_title }}</title>

    @if($icon)<link href="{{ server() . $icon }}" rel="shortcut icon" />@endif
    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Hind:400,700" rel="stylesheet">

    <!-- Bootstrap -->
    <link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css" />

    <!-- Slick -->
    <link type="text/css" rel="stylesheet" href="/css/slick.css" />
    <link type="text/css" rel="stylesheet" href="/css/slick-theme.css" />

    <!-- nouislider -->
    <link type="text/css" rel="stylesheet" href="/css/nouislider.min.css" />

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="/css/font-awesome.min.css">

    <!-- Custom stlylesheet -->
    <link type="text/css" rel="stylesheet" href="/css/style.css" />
    <link type="text/css" rel="stylesheet" href="/css/styles.css" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" id="cookieinfo" src="/js/cookieinfo.js" data-bg="#645862" data-fg="#FFFFFF" data-link="#F1D600" data-cookie="CookieInfoScript" data-text-align="left" data-close-text="Got it!"></script>
    @yield('styles')
</head>

<body>
<!-- HEADER -->
<header>
    <!-- top Header -->
    <div id="top-header">
        <div class="container">
            <div class="pull-left">
                @if($site_title)
                    <span>Welcome to {{ $site_title }}!</span>
                @else<span>Welcome!</span>
                @endif
            </div>
            <div class="pull-right">
                <ul class="header-top-links">
                    <li><a href="#">Store</a></li>
                    <li><a href="#">Newsletter</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li class="dropdown default-dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">ENG <i class="fa fa-caret-down"></i></a>
                        <ul class="custom-menu">
                            <li><a href="#">English (ENG)</a></li>
                            <li><a href="#">Russian (Ru)</a></li>
                            <li><a href="#">French (FR)</a></li>
                            <li><a href="#">Spanish (Es)</a></li>
                        </ul>
                    </li>
                    <li class="dropdown default-dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">USD <i class="fa fa-caret-down"></i></a>
                        <ul class="custom-menu">
                            <li><a href="#">USD ($)</a></li>
                            <li><a href="#">EUR (€)</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /top Header -->

    <!-- header -->
    <div id="header">
        <div class="container">
            <div class="pull-left">

                <!-- Logo -->
                @if($logo)
                    <div class="header-logo">
                        <a class="logo" href="/">
                            <img src="{{ $logo->path }}" alt="" style="max-width: 100px; max-height: 70px;">
                        </a>
                    </div>
                @endif
                <!-- /Logo -->

                <!-- Search -->
                <div class="header-search">
                    <form method="get" action="/search">
                        <input class="input search-input" type="text" name="q" value="@if(isset($search)){{$search}}@endif" placeholder="Enter your keyword">
                        <select class="input search-categories" name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{$category->alias}}"@if(isset($_GET['category']) && strtolower($_GET['category'])==strtolower($category->name)) selected @endif >{{$category->name}}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="search-btn"><i class="fa fa-search"></i></button>
                    </form>
                </div>
                <!-- /Search -->
            </div>
            <div class="pull-right">
                <ul class="header-btns">
                    <!-- Account -->
                    <li class="header-account dropdown default-dropdown">
                        <div class="dropdown-toggle" role="button" data-toggle="dropdown" aria-expanded="true">
                            <div class="header-btns-icon">
                                <i class="fa fa-user-o"></i>
                            </div>
                            <strong class="text-uppercase">My Account <i class="fa fa-caret-down"></i></strong>
                        </div>
                        @if(!Core\Data::userIsLoggedIn())
                            <a href="/signin" class="text-uppercase">Login</a> / <a href="/signup" class="text-uppercase">Join</a>
                        @else
                            <a href="/logout" class="text-uppercase">Logout</a>
                        @endif
                        <ul class="custom-menu">
                            @if(Core\Data::userIsLoggedIn())
                                @if(\Core\Auth::isAdmin()=='admin')<li><a href="/admin" target="_blank"><i class="fa fa-cogs"></i> Dashboard</a></li>@endif
                                <li><a href="/edit-profile"><i class="fa fa-user-o"></i> My Account</a></li>
                                <li><a href="#"><i class="fa fa-heart-o"></i> My Wishlist</a></li>
                                <li><a href="#"><i class="fa fa-exchange"></i> Compare</a></li>
                                <li><a href="#"><i class="fa fa-check"></i> Checkout</a></li>
                            @else
                                <li><a href="/signin"><i class="fa fa-unlock-alt"></i> Login</a></li>
                                <li><a href="/signup"><i class="fa fa-user-plus"></i> Create An Account</a></li>
                            @endif
                        </ul>
                    </li>
                    <!-- /Account -->

                    <!-- Cart -->
                    <li class="header-cart dropdown default-dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            <div class="header-btns-icon">
                                <i class="fa fa-shopping-cart"></i>
                                @if($orders && count($orders)>0)
                                <span class="qty" id="qty">{{ count($orders) }}</span>
                                @endif
                            </div>
                            <strong class="text-uppercase">My Cart:</strong>
                            <br>
                            @if($orders && count($orders)>0)
                                <span id="total-price">{{ printPrice($total_price) }}</span>
                            @endif
                        </a>
                        <div class="custom-menu">
                            <div id="shopping-cart">
                                <div class="shopping-cart-list">
                                    @if($orders)
                                        @foreach($orders as $order)
                                        <div class="product product-widget">
                                            <div class="product-thumb">
                                                <img src="{{ $order->path ? : '/placeholder.jpg' }}" alt="">
                                            </div>
                                            <div class="product-body">
                                                <h3 class="product-price">{{ printPrice($order->promo_price!=null ? $order->promo_price : $order->price) }} <span class="qty">x{{ $order->qty }}</span></h3>
                                                <h2 class="product-name"><a href="/products/{{ $order->product_id }}">{{ $order->title }}</a></h2>
                                                @if($order->variation_name)<span>{{ $order->variation_name }}: {{ $order->variation }}</span>@endif
                                            </div>
                                            <form action="/remove-ordered-product" method="post" class="form-delete-order">
                                                <input type="hidden" name="order-id" value="{{ $order->id }}">
                                                {!! csrf() !!}
                                                <button type="submit" class="cancel-btn delete-ordered-product"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                        @endforeach
                                    @else
                                        <p style="text-align: center">Your cart is empty</p>
                                    @endif
                                </div>
                                <div class="shopping-cart-btns">
                                    <a class="main-btn" href="/cart"><i class="fa fa-shopping-cart" aria-hidden="true"></i> View Cart</a>
                                    <a href="/checkout" class="primary-btn">Checkout <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <!-- /Cart -->

                </ul>
            </div>
        </div>
        <!-- header -->
    </div>
    <!-- container -->
</header>
<!-- /HEADER -->

<!-- NAVIGATION -->
{!! generateMenu($categories) !!}
<!-- /NAVIGATION -->
<!-- BREADCRUMB -->
<!--<div id="breadcrumb">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="#">Home</a></li>
            <li class="active">Blank</li>
        </ul>
    </div>
</div>-->
<!-- /BREADCRUMB -->

<div class="container body-container">
    @if($_SESSION['errors'])
        <div class="col-md-8 col-md-offset-2 alert alert-danger" role="alert">
            @foreach($_SESSION['errors'] as $error)
                {{ $error }} <br>
            @endforeach
        </div>
    @endif

    @if($_SESSION['messages'])
        <div class="col-md-8 col-md-offset-2 alert alert-success" role="alert">
            @foreach($_SESSION['messages'] as $message)
                {{ $message }} <br>
            @endforeach
        </div>
    @endif

</div>

<!-- section -->
<div class="section">
    <!-- container -->
    <div class="container">
        <!-- row -->
<!--        <div class="row">-->
            @yield('content')
<!--        </div>-->
        <!-- /row -->
    </div>
    <!-- /container -->
</div>
<!-- /section -->

<!-- FOOTER -->
<footer id="footer" class="section section-grey">
    <!-- container -->
    <div class="container">
        <!-- row -->
        <div class="row">
            <!-- footer widget -->
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="footer">
                    <!-- footer logo -->
                    @if($logo)
                        <div class="footer-logo">
                            <a class="logo" href="/">
                                <img src="{{ $logo->path }}" alt="" style="max-width: 100px; max-height: 70px;">
                            </a>
                        </div>
                    @endif
                    <!-- /footer logo -->

                    <p>{!! $footer_text !!}</p>

                    <!-- footer social -->
                    <ul class="footer-social">
                        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                    </ul>
                    <!-- /footer social -->
                </div>
            </div>
            <!-- /footer widget -->

            <!-- footer widget -->
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="footer">
                    <h3 class="footer-header">My Account</h3>
                    <ul class="list-links">
                        @if(\Core\Data::userIsLoggedIn())<li><a href="/edit-profile">My Account</a></li>@endif
                        <li><a href="#">My Wishlist</a></li>
                        <li><a href="#">Compare</a></li>
                        <li><a href="/checkout">Checkout</a></li>
                        @if(!Core\Data::userIsLoggedIn())<li><a href="#">Login</a></li>@endif
                    </ul>
                </div>
            </div>
            <!-- /footer widget -->

            <div class="clearfix visible-sm visible-xs"></div>

            <!-- footer widget -->
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="footer">
                    <h3 class="footer-header">Customer Service</h3>
                    <ul class="list-links">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Shiping & Return</a></li>
                        <li><a href="#">Shiping Guide</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <!-- /footer widget -->

            <!-- footer subscribe -->
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="footer">
                    <h3 class="footer-header">Stay Connected</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor.</p>
                    <form>
                        <div class="form-group">
                            <input class="input" placeholder="Enter Email Address">
                        </div>
                        <button class="primary-btn">Join Newslatter</button>
                    </form>
                </div>
            </div>
            <!-- /footer subscribe -->
        </div>
        <!-- /row -->
        <hr>
        <!-- row -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2 text-center">
                <!-- footer copyright -->
                <div class="footer-copyright">
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    Copyright &copy;{{ date("Y") }}
                    All rights reserved | This template is made with
                    <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                </div>
                <!-- /footer copyright -->
            </div>
        </div>
        <!-- /row -->
    </div>
    <!-- /container -->
</footer>
<div class="my-footer">
    <span class="footer-text">Created by <a href="https://www.facebook.com/jikata81" target="_blank"><span class="author-name">Jivko Jelev</span></a></span>
</div>
<!-- /FOOTER -->

<!-- jQuery Plugins -->
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/slick.min.js"></script>
<script src="/js/nouislider.min.js"></script>
<script src="/js/jquery.zoom.min.js"></script>
<script src="/js/main.js"></script>

@yield('scripts')
<script>
    var element_for_remove;
    $('.delete-ordered-product').on('click', function () {
        element_for_remove = $(this);
    })
    $('.form-delete-order').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url:"/remove-ordered-product",
            method:"POST",
            data:new FormData(this),
            contentType:false,
            //cache:false,
            processData:false,
            success:function(data)
            {
                $(element_for_remove).closest('div').remove();
                $('#total-price').html(data);
            }
        })
    });
</script>

</body>
</html>
