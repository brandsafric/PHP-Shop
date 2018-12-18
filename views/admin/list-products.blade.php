@extends('layouts.admin')

@section('title')List Products @endsection

@section('styles')
    <link rel="stylesheet" href="../../bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <style>
        #example1 img{
            width: 50px;
        }
        .btn {
            padding-right: 10px !important;
        }
    </style>
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">List Products</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><i class="fa fa-fw fa-file-image-o"></i></th>
                                <th>Title</th>
                                <th>Availability</th>
                                <th>Price</th>
                                <th>Promo Price</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $key=>$product)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>@if($product->path)<img src="{{$product->path}}" alt="">@endif</td>
                                <td><a href="/admin/products/{{$product->id}}/edit">{{ $product->title }}</a></td>
                                <td>{{ $product->availability }}</td>
                                <td>{{ $product->price }}</td>
                                <td>{{ $product->promo_price }}</td>
                                <td>
                                    <a href="/admin/products/{{$product->id}}/edit" class="btn btn-success btn-block btn-xs"><i class="fa fa-fw fa-edit"></i> Edit</a>
                                </td>
                                <td>
                                    <form action="/admin/products/{{$product->id}}" method="post">
                                        <button type="submit" class="btn btn-danger btn-block btn-xs"><i class="fa fa-fw fa-remove"></i> Delete</button>
                                        <input type="hidden" name="_method" value="DELETE">
                                        {!! csrf() !!}
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                 </tbody>
                            <tfoot>
                            <tr>
                                <th>#</th>
                                <th><i class="fa fa-fw fa-file-image-o"></i></th>
                                <th>Title</th>
                                <th>Availability</th>
                                <th>Price</th>
                                <th>Promo Price</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
@endsection

@section('scripts')
    <!-- DataTables -->
    <script src="../../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script>
        $(function () {
            $('#example1').DataTable()
        })
    </script>
@endsection