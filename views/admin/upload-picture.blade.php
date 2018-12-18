@extends('layouts.admin')

@section('title')Upload Pictures @endsection

@section('styles')
    <style>
        .row {
        margin-right: 0;
        margin-left: 0;
        }
    </style>
@endsection


@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="col-md-4">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Thumbnails</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <div class="box-body">
                <form id="submit_form" action="/admin/upload" method="post" enctype="multipart/form-data">
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
                        <button type="button" class="btn btn-primary btn-sm" id="picture">Select Pictures</button>
                        <input type="submit" name="upload_button" class="btn btn-primary btn-sm" value="Upload" />
                    </div>
                    {!! csrf() !!}
                </form>
                </div>
            </div>


        </div>
    </section>
    <!-- /Main content -->
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $(document).on('click', '#picture', function(){
                $("#image_file").click();
            });
        });
    </script>

@endsection