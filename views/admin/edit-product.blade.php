@extends('layouts.admin')

@section('title')Edit Product @endsection

@section('styles')
    <link rel="stylesheet" type="text/css" href="/css/image-picker.css">
    <style>
        .thumbnail img{
            width: 95px;
        }
        .tab-content{
            padding: 10px;
        }
        .col-md-3{
            margin-bottom: 5px;
        }
        .row  .col-md-12{
            padding: 0;
        }
        .row-price{
            margin-top: 20px !important;
        }
    </style>

@endsection

@section('content')
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">

                <div class="modal fade" id="modal-picture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Pictures</h4>
                            </div>
                            <div class="modal-body">
                                <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                                    <ul class="nav nav-tabs" id="myTabs" role="tablist">
                                        <li role="presentation" id="upload">
                                            <a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="false">Upload</a>
                                        </li>
                                        <li role="presentation" class="active" id="select">
                                            <a href="#div-picture" role="tab" id="div-picture-tab" data-toggle="tab" aria-controls="div-picture" aria-expanded="true">Select</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade" role="tabpanel" id="home" aria-labelledby="home-tab">
                                            <div class="row">
                                                <div class="col-md-6 pull-right">
                                                    <form id="submit_form2" action="/admin/upload" method="post" enctype="multipart/form-data">
                                                        <div class="form-group">
                                                            <input type="file" name="file[]" id="image_file" multiple style="display: none"/>
                                                            <div class="form-group">
                                                                <label for="width" class="col-sm-4 control-label">Crop</label>
                                                                <div class="col-sm-4">
                                                                    <input type="number" min="1" class="form-control" name="width" placeholder="Width" value="{{ $picture_dimensions[0] }}">
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <input type="number" min="1" class="form-control" name="height" placeholder="Height" value="{{ $picture_dimensions[1] }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <!-- checkbox -->
                                                            <div class="form-group pull-right">
                                                                <label>
                                                                    <input type="checkbox" class="minimal" name="crop" checked>
                                                                    Crop
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group pull-right">
                                                            <button type="button" class="btn btn-primary btn-sm" id="select-picture1">Select Pictures</button>
                                                            <input type="submit" name="upload_button" class="btn btn-primary btn-sm" value="Upload" />
                                                        </div>
                                                        {!! csrf() !!}
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade active in" role="tabpanel" id="div-picture" aria-labelledby="div-picture-tab">
                                            <form id="form-picture" method="post">
                                                <input type="hidden" name="selected-id-picture" >
                                                <input type="hidden" name="suffix" value="picture">
                                                {!! csrf() !!}
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="save-changes-picture" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Pictures</h4>
                            </div>
                            <div class="modal-body">
                                <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                                    <ul class="nav nav-tabs" id="myTabs" role="tablist">
                                        <li role="presentation" id="upload">
                                            <a href="#home1" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="false">Upload</a>
                                        </li>
                                        <li role="presentation" class="active" id="select">
                                            <a href="#profile1" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile" aria-expanded="true">Select</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade" role="tabpanel" id="home1" aria-labelledby="home-tab">
                                            <form id="submit_form2" action="/admin/upload" method="post" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <input type="file" name="file[]" id="image_file" multiple style="display: none"/>
                                                    <div class="form-group">
                                                        <label for="width" class="col-sm-4 control-label">Crop</label>
                                                        <div class="col-sm-4">
                                                            <input type="number" min="1" class="form-control" name="width" placeholder="Width" value="{{ $picture_dimensions[0] }}">
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <input type="number" min="1" class="form-control" name="height" placeholder="Height" value="{{ $picture_dimensions[1] }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <!-- checkbox -->
                                                    <div class="form-group pull-right">
                                                        <label>
                                                            <input type="checkbox" class="minimal" name="crop" checked>
                                                            Crop
                                                        </label>
                                                    </div>
                                                </div>
                                                {!! csrf() !!}
                                            </form>
                                        </div>
                                        <div class="tab-pane fade active in" role="tabpanel" id="div-picture" aria-labelledby="div-picture-tab">
                                            <form id="form-picture" method="post">
                                                <input type="hidden" name="selected-id-picture" >
                                                <input type="hidden" name="suffix" value="picture">
                                                {!! csrf() !!}
                                            </form>
                                        </div>
                                        <form id="product-picture3" method="post">
                                            @if($pictures)
                                                @foreach($pictures as $picture)
                                                    <input type="hidden" id="gallery-pictures[]" name="gallery-pictures[]" value="{{$picture->picture_id}}">
                                                @endforeach
                                            @endif
                                            {!! csrf() !!}
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="save-changes1" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Edit Product</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="form-horizontal" method="post" action="/admin/products/{{ $product->id }}">
                            <div class="box-body">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="title" class="col-sm-2 control-label">Title</label>

                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{old('title', $product->title)}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="parent" class="col-sm-2 control-label">Parent Category</label>
                                        <div class="col-sm-4">
                                            <select name="category-id" class="form-control" >
                                                @foreach($categories as $category)
                                                    <option value="{{$category->id}}" @if($product->category_id == $category->id)selected @endif>{{$category->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <label for="parent" class="col-sm-2 control-label">Date Created</label>
                                        <div class="col-sm-4">
                                            <input id="datetimepicker" name="datetime" class="form-control" type="text" value="{{ $product->updated_at }}">
                                        </div>

                                    </div>
                                    <textarea id="description" name="description" rows="10" cols="80">{{ $product->description }}</textarea>
                                    <div class="col-md-12 row-price">
                                        <div class="row">
                                            <div class="form-group col-sm-4 col-md-3">
                                                <label for="">Price</label>
                                                <input type="number" step=".01" min="0" name="price" class="form-control" value="{{old('price', $product->price)}}">
                                            </div>
                                            <div class="form-group col-sm-4 col-md-3">
                                                <label for="">Promo Price</label>
                                                <input type="number" step=".01" min="0" name="promo-price" class="form-control" value="{{old('promo-price', $product->promo_price)}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-sm-4 col-md-3">
                                                <label for="variation-name">Variation Name</label>
                                                <input type="text" name="variation-name" class="form-control" value="{{old('variation-name', $product->variation_name)}}">
                                            </div>
                                            <div class="form-group col-sm-4 col-md-3">
                                                <label for="variation-value">Values</label>
                                                <input type="text" name="variation-value" class="form-control" value="{{old('variation-value', $product->variation_values)}}">
                                                <p><i>Separate variations with comma</i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- footer -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-default pull-right" name="submit">Save</button>
                            </div>
                            {!! csrf() !!}
                            <div id="text">
                                @if($pictures)
                                    @foreach($pictures as $picture)
                                        <input type="hidden" id="gallery-pictures[]" name="gallery-pictures[]" value="{{$picture->picture_id}}">
                                    @endforeach
                                @endif
                            </div>
                            <div id="text1">
                                @if($product->picture_id)
                                    <input type="hidden" id="picture-id" name="picture-id" value="{{ $product->picture_id }}">
                                @endif
                            </div>
                            <input type="hidden" name="_method" value="PUT">
                        </form>
                    </div>
                </div>


                <div class="col-md-3">
                    <a href="/products/{{ $product->id }}" class="btn btn-block btn-default" target="_blank">View Product</a>
                </div>

                <div class="col-md-3">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Product Picture</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="col-md-12">
                                <a href="#" id="pictures" data-toggle="modal" class="text-center" data-target="#modal-picture">Select Product Picture</a>
                                @if(isset(\App\Models\Thumbnail::first('picture_id', $product->picture_id)->path))
                                    <img src="{{ \App\Models\Thumbnail::first('picture_id', $product->picture_id)->path }}" id="product-picture" style="width:100%">
                                @endif
                                <input type="hidden" id="picture-id-picture" name="picture-id-picture" value="">
                                </p>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- ./box-body -->

                    </div>
                    <!-- /.box -->

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Product Gallery</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="">
                            <div class="col-md-12">
                                <a href="#" id="pictures1" data-toggle="modal" data-target="#myModal1">Select Gallery Pictures</a>
                                <div id="profile-picture1">
                                    @if($pictures)
                                        @foreach($pictures as $picture)
                                            <div class="col-md-6" style="padding-left: 0; padding-right: 0;">
                                                @if(isset($picture->path))<img src="{{ $picture->path }}" style="width:100%; padding: 0 5px 3px 0;">@endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <form action="" id="files1">{!! csrf() !!}</form>
                <form action="" id="files">{!! csrf() !!}</form>
            </div>
        </div>

    </section>
@endsection

@section('scripts')
    <!-- CK Editor -->
    <script src="/bower_components/ckeditor/ckeditor.js"></script>

    <script>
        $(document).ready(function(){
            $(document).on('click', '#pictures', function(){
                $("#form-picture").submit();
            });

        });
        $('#form-picture').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url:"/admin/select-picture/",
                method:"POST",
                data:new FormData(this),
                contentType:false,
                //cache:false,
                processData:false,
                success:function(data)
                {
                    $('#div-picture').html(data);
                }
            })
        });

        $(document).ready(function(){
            $(document).on('click', '#pictures1', function(){
                $("#product-picture3").submit();
            });

        });
        $('#product-picture3').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url:"/admin/get-gallery-pictures/",
                method:"POST",
                data:new FormData(this),
                contentType:false,
                //cache:false,
                processData:false,
                success:function(data)
                {
                    $('#profile1').html(data);

                }
            })
        });
    </script>

    <script data-sample="1">
        var config = {
            extraPlugins: 'colorbutton, font',
            codeSnippet_theme: 'monokai_sublime',
            height: 250
        };

        CKEDITOR.replace( 'description', config );

    </script>
    <script src="/js/image-picker.js"></script>

    <link rel="stylesheet" type="text/css" href="/css/jquery.datetimepicker.min.css">
    <script src="/js/jquery.datetimepicker.full.min.js"></script>
    <script>
        jQuery('#datetimepicker').datetimepicker({
            format:'Y-m-d H:i',
        });
    </script>

@endsection