@extends('layouts.app')

{{--todo colums--}}
@section('content')
    <div class="container mt-5">
        <h5 class="text-center">Category</h5>
        @include('categories.parts.categories_block', ['categories' => [$category]])

        @if($category->parent !== null)
            <h5 class="text-center">Parent Category:</h5>
            @include('categories.parts.categories_block', ['categories' => [$category->parent]])
        @endif
        <div class="album bg-body-tertiary">
            @if($categories->count() > 0)
                <div class="container">
                    <h5 class="text-center mb-3">Child categories</h5>
                    @include('categories.parts.categories_block', ['categories' => $categories])
                </div>
            @endif
        </div>

        <div class="row row-cols-1 row-cols-sm-3 row-cols-md-4 g-4">
            @each('products.parts.card', $products, 'product')
        </div>
    </div>
@endsection


