@extends('layouts.app')

<?php
use App\Enums\Account\SubscriptionType as SubscriptionType;
?>

@section('content')
    <div class="container mt-5">
        <div class="row row-cols-1 row-cols-sm-2 g-2 mb-5">
            <div class="col col-sm-4">
                <div id="carouselExampleIndicators" class="carousel slide">
                    <div class="carousel-indicators">
                        @foreach($gallery as $key => $image)
                            <button type="button"
                                    data-bs-target="#carouselExampleIndicators"
                                    data-bs-slide-to="{{$key}}"
                                    class="{{$key === 0 ? 'active' : ''}}"
                                    aria-current="true"
                                    aria-label="Slide {{$key + 1}}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($gallery as $key => $image)
                            <div class="carousel-item {{$key === 0 ? 'active' : ''}}">
                                <img src="{{$image}}"
                                     class="d-block w-100"
                                     alt="{{$product->title}}">
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                            data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                            data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <div class="col col-sm-8">
                <div class="d-flex flex-column align-items-start justify-content-start ms-5">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <h4 class="mb-5">{{$product->title}}</h4>
                        <small class="mb-2">SKU: {{$product->SKU}}</small>
                    </div>
                    <div class="d-flex flex-column justify-content-center w-100 align-items-center mb-3">
                        <p>Categories: </p>
                        <div class="container">
                            @include('categories.parts.categories_block', ['categories' => $product->categories])
                        </div>

                    </div>

                    <p class="mb-2">Quantity: {{$product->quantity}}</p>
                    @auth
                        <div class="d-flex justify-content-end w-100 align-items-center">
                            @if(!auth()->user()->isWishedProduct($product, SubscriptionType::Price))
                                @include('products.parts.wishlist.price', ['product' => $product])
                            @else
                                @include('products.parts.wishlist.price_remove', ['product' => $product])
                            @endif

                            @if(!$product->isExists)
                                @if(!auth()->user()->isWishedProduct($product, SubscriptionType::Available))
                                    @include('products.parts.wishlist.available', ['product' => $product])
                                @endif
                            @endif
                            @if(auth()->user()->isWishedProduct($product, SubscriptionType::Available))
                                @include('products.parts.wishlist.available_remove', ['product' => $product])
                            @endif
                        </div>
                    @endauth
                    {{--                    @if($product->isExists)--}}
                    <div class="d-flex w-100 price-container">
                        <div class="justify-content-center w-50">
                            <h5 class="mt-1 ">{{$product->price}} $</h5>
                        </div>
                        <div class="justify-content-end  w-50">
                            @if(!$isInCart)
                                @include('cart.parts.add_button', ['product' => $product, 'rowId' => $rowId])
                            @else
                                @include('cart.parts.remove_button', ['product' => $product])
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
            </div>
            <div class="col-12 text-center fs-5 mt-3">
                <p>{{$product->description}}</p>
            </div>
            <div class="col-12">
                <hr>
            </div>
        </div>
    </div>
@endsection
