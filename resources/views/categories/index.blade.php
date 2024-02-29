@extends('layouts.app')

@section('content')
    @include('categories.parts.categories_block', ['categories' => $categories])
@endsection
