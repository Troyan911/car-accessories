@extends('layouts.admin')

@section('content')

    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Parent</th>
            <th scope="col"># products</th>
            <th scope="col">Created</th>
            <th scope="col">Modified</th>
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
                <td>{{$category->products->count()}}</td>
                <td>{{$category->created_at}}</td>
                <td>{{$category->modified_at}}</td>
                <td><a href="{{route("admin.categories.edit", $category->id)}}">Edit</a></td>
                <td><a href="{{route("admin.categories.destroy", $category->id)}}">Delete</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$categories->links()}}

@endsection
