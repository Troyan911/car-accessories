@extends('layouts.app')

<?php

use App\Enums\Account\SubscriptionType as SubscriptionType;

?>

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-12 mb-3 text-center">
                <h3>
                    Your wishlist
                </h3>
            </div>
            <div class="col-12">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">@sortablelink('id', '#')</th>
                        <th scope="col">Image</th>
                        <th scope="col">@sortablelink('title', 'Title')</th>
                        <th scope="col">@sortablelink('price', 'Price')</th>
                        <th scope="col" class="text-center">Follow price</th>
                        <th scope="col" class="text-center">Follow available</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{$product->id}}</td>
                            <td><a href="{{route('products.show', $product->id)}}">
                                    <img src="{{$product->thumbnailUrl}}" alt="{{$product->title}}" width="100"
                                         height="100">
                                </a>
                            </td>
                            <td>{{$product->title}}</td>
                            <td>{{$product->finalPrice}}</td>
                            {{--                <td>{{$product->quantity}}</td>--}}
                            <td class="text-center" style="font-size: 18px"><i
                                    class="fa-solid fa-{{$product->pivot->price ? 'check' : 'xmark'}}"></i></td>
                            <td class="text-center" style="font-size: 18px"><i
                                    class="fa-solid fa-{{$product->pivot->available ? 'check' : 'xmark'}}"></i></td>
                            <td>
                                <div class="d-flex justify-content-end w-100 align-items-center">
                                    @include(
                                        'products.parts.wishlist.price', [
                                            'product' => $product,
                                            'isFollowed' => auth()->user()->isWishedProduct($product, SubscriptionType::Price),
                                            'minimized' => true
                                        ])
                                    @include(
                                        'products.parts.wishlist.available', [
                                            'product' => $product,
                                            'isFollowed' =>auth()->user()->isWishedProduct($product, SubscriptionType::Available),
                                            'minimized' => true
                                    ])
                                </div>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$products->links()}}

            </div>
        </div>
    </div>
@endsection
