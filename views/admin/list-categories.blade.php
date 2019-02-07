@extends('layouts.admin')

@section('title')Edit / Delete Categories @endsection


@section('content')
    <!-- Main content -->
    <section class="content col-md-8">
        <table class="table table-striped categories-table">
            <tbody>
            <tr>
                <th style="width: 10px">#</th>
                <th>Name</th>
                <th>Alias</th>
                <th>Parent ID</th>
                <th>Save</th>
                <th>Delete</th>
            </tr>
            <tr>
                @foreach($categories as $key=>$category)
                    <form action="/admin/categories/{{$category->id}}" method="post">
                        <td>{{$key+1}}</td>
                        <td>
                            <input type="text" name="name" class="form-control input-sm" value="{{old('name', $category->name) }}">
                        </td>
                        <td>
                            <input type="text" name="alias" class="form-control input-sm" value="{{$category->alias}}">
                        </td>
                        <td>
                            <select name="parent" id="parent" class="form-control input-sm">
                                <option value="">Without Parent</option>
                                @foreach($categories as $cat)
                                    @if($cat->id != $category->id)
                                        <option value="{{$cat->id}}"
                                                @if($cat->id==$category->parent_id) selected @endif>{{$cat->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <input type="hidden" name="_method" value="PUT">
                        <td>
                            <input type="submit" name="submit" class="btn btn-success btn-xs btn-block" value="Save">
                        </td>
                        {!! csrf() !!}
                        <input type="hidden" name="category-id" value="{{ $category->id }}">
                    </form>
                    <td>
                        <form action="/admin/categories/{{$category->id}}" method="post">
                            <input type="submit" name="submit" class="btn btn-danger btn-xs btn-block" value="Delete">
                            <input type="hidden" name="_method" value="DELETE">
                            {!! csrf() !!}
                        </form>
                    </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </section>
    <!-- /.content -->
@endsection

@section('scripts')

@endsection