@extends('layouts.admin')

@section('content')

    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">@sortablelink('id', '#')</th>
            <th scope="col">Image</th>
            <th scope="col">@sortablelink('title', 'Title')</th>
            <th scope="col">@sortablelink('SKU', 'SKU')</th>
            <th scope="col">Categories</th>
            <th scope="col">@sortablelink('price', 'price')</th>
            <th scope="col">@sortablelink('quantity', 'quantity')</th>

            <th scope="col">@sortablelink('created_at', 'Created')</th>
            <th scope="col">@sortablelink('modified_at', 'Modified')</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{$product->id}}</td>
                <td><img src="{{$product->thumbnailUrl}}" alt="{{$product->title}}" width="100" height="100"></td>
                <td>{{$product->title}}</td>
                <td>{{$product->SKU}}</td>

{{--                todo categories--}}
                {{--                <td>--}}
                {{--                    @if($product->categories->exists())--}}
                {{--                        {{$product->categories_count}}--}}
                {{--                <a href="{{route('admin.categories.edit', $category->parent)}}">{{$category->parent->name}}</a>--}}
                {{--                    @else--}}
                {{--                        ---}}
                {{--                    @endif--}}
                {{--                </td>--}}

                <td>{{$product->finalPrice}}</td>
                <td>{{$product->quantity}}</td>
                <td>{{$product->created_at}}</td>
                <td>{{$product->modified_at}}</td>
                <td>
                    <form method="POST" action="{{route("admin.products.destroy", $product)}}">
                        @csrf
                        @method("DELETE")
                        <a class="btn btn-warning"
                           href="{{route("admin.products.edit", $product)}}"><i
                                class="fa-regular fa-pen-to-square"></i></a>
                        <button class="btn btn-danger" type="submit" href=""><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>

                {{--                <td><a href="{{route("admin.categories.destroy", $category)}}">Delete</a></td>--}}
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$products->links()}}

@endsection
