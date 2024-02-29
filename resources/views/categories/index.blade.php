@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        @include('categories.parts.categories_block', ['categories' => $categories])
    </div>
@endsection
