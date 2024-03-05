@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row g-2 mb-5 text-center">
            <h3>Cart</h3>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 g-2 mb-5">
            <div class="col-12 col-sm-8 col-md-9">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($content as $row)
                        <td style="max-width: 100px; text-align: center;">
                            <img src="{{$row->model->thumbnailUrl}}" alt="{{$row->name}}" style="height: 100px">
                        </td>
                        <td>
                            <a href="{{route('products.show', $row->id )}}">{{$row->name}}</a>
                        </td>
                        <td>
                            <form action="{{route('cart.update', $row->model)}}" method="POST"
                                  style="max-width: 150px; text-align: center;">
                                @csrf
                                <input type="hidden" name="rowId" value="{{$row->rowId}}"/>
                                {{--                                <button class="btn btn-outline-secondary" type="button" id="button-addon1">-</button>--}}
                                <input type="number" name="count" class="form-control counter"
                                       value="{{$row->qty}}" max="{{$row->model->quantity + 1}}" min="1"/>
                                {{--                                <button class="btn btn-outline-secondary" type="button" id="button-addon1">+</button>--}}
                            </form>
                        </td>
                        <td>{{$row->finalPrice}} $</td>
                        <td>{{$row->subtotal}} $</td>
                        <td>
                            <form action="{{route('cart.remove')}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="rowId" value="{{$row->rowId}}"/>
                                <button type="submit" class="btn btn-outline-danger"><i
                                        class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-12 col-sm-4 col-md-3">
                @include('cart.parts.subtotal_block', compact('subTotal', 'tax', 'total'))
                <hr>
                @auth()
                    <a href="{{route('checkout')}}" class="btn btn-outline-success w-100">Proceed to checkout</a>
                @else
                    <a href="{{route('login')}}" class="btn btn-outline-info w-100 mb-3">Sign in</a>
                    <a href="{{route('register')}}" class="btn btn-outline-warning w-100">Sign up</a>
                @endauth

            </div>
        </div>
    </div>
@endsection

@push('footer.js')
    @vite(['resources/js/cart.js'])
@endpush
