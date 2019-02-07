@extends('layouts.admin')

@section('title')Add Category @endsection


@section('content')
    <!-- Main content -->
    <section class="content col-md-6">
        <form action="/admin/categories" method="post">
            <!-- Default box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Add Category</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                            <i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="name">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Category Name">
                    </div>
                    <div class="form-group">
                        <label for="alias">Alias</label>
                        <input type="text" class="form-control" id="alias" name="alias" placeholder="Alias">
                    </div>
                    <div class="form-group">
                        <label for="parent">Parent Category</label>
                        <select name="parent" id="parent" class="form-control" >
                            <option value="">Without Parent</option>
                            @foreach($cats as $category)
                                <option value="{{$category->id}}" @if(old('parent')==$category->id)selected @endif>{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <input type="submit" class="btn btn-primary" value="Create">
                </div>
                <!-- /.box-footer-->
            </div>
            <!-- /.box -->
            {!! csrf() !!}
        </form>
    </section>
    <!-- /.content -->
@endsection

@section('scripts')
    <script>
        $('#name').on('change', function () {
            $('#alias').val($('#name').val());
        })
    </script>
@endsection