@extends('layouts.app')

@section('content')
    <div class="album py-5 bg-body-tertiary">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h3>The order was completed, thank you!</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h4 class="mb-3">User Info:</h4>
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <td>Name</td>
                            <td>{{ $order->name }}</td>
                        </tr>
                        <tr>
                            <td>Surname</td>
                            <td>{{ $order->surname }}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ $order->email }}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>{{ $order->phone }}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>{{ $order->address }}</td>
                        </tr>
                        <tr>
                            <td>City</td>
                            <td>{{ $order->city }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12 col-md-6">
                    <h4 class="mb-3">Order Info:</h4>
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <td>Total</td>
                            <td>{{ $order->total }}</td>
                        </tr>
                        <tr>
                            {{--                            todo get vat from Cart--}}
                            <td>VAT</td>
                            <td>{{ round($order->total /( 1 + $tax ) * $tax, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>{{ $order->status->name }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12 col-md-12">
                    <h4 class="mb-3">Products Info:</h4>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <td class="text-center">Image</td>
                            <td>Name</td>
                            <td>Quantity</td>
                            <td>Price</td>
                            <td>Total</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->products as $product)
                            <tr>
                                <td style="max-width: 100px; text-align: center;">
                                    <img src="{{$product->thumbnailUrl}}" alt="{{$product->title}}"
                                         style="height: 100px">
                                </td>
                                <td>
                                    <a href="{{route('products.show', $product->id )}}">{{$product->title}}</a>
                                </td>
                                <td>{{ $product->pivot->quantity }}</td>
                                <td>{{ $product->pivot->single_price }}</td>
                                <td>{{ $product->pivot->single_price * $product->pivot->quantity  }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
