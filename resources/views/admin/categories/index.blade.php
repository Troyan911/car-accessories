@extends('layouts.admin')

@section('content')

    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">@sortablelink('id', '#')</th>
            <th scope="col">@sortablelink('name', 'Name')</th>
            <th scope="col">@sortablelink('parent_id', 'Parent')</th>
            <th scope="col">@sortablelink('products_count', '# products')</th>
            <th scope="col">@sortablelink('created_at', 'Created')</th>
            <th scope="col">@sortablelink('updated_at', 'Modified')</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
            <tr>
                <td>{{$category->id}}</td>
                <td>{{$category->name}}</td>
                <td>
                    @if($category->parent)
                        <a href="{{route('admin.categories.edit', $category->parent)}}">{{$category->parent->name}}</a>
                    @else
                        -
                    @endif
                </td>
                {{--                <td>{{$category->products->count()}}</td>--}}
                <td>{{$category->products_count}}</td>
                <td>{{$category->created_at}}</td>
                <td>{{$category->updated_at}}</td>
                <td>
                    <form method="POST" action="{{route("admin.categories.destroy", $category)}}">
                        @csrf
                        @method("DELETE")
                        <a class="btn btn-warning"
                           href="{{route("admin.categories.edit", $category)}}"><i
                                class="fa-regular fa-pen-to-square"></i></a>
                        <button class="btn btn-danger" type="submit" href=""><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>

                {{--                <td><a href="{{route("admin.categories.destroy", $category)}}">Delete</a></td>--}}
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$categories->links()}}

@endsection
