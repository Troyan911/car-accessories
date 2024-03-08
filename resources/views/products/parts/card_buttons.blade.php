<div class="w-100 btn-group product-preview-button-container">

    <a href="{{route('products.show', $product)}}" class="btn btn-outline-info w-50">Show</a>
    @if($product->isExists)
        <form action="{{route('cart.add', $product)}}" method="POST" class="btn-group w-50">
            @csrf
            <button type="submit" class="btn btn-outline-success">Buy</button>
        </form>
    @else
        <form action="{{route('wishlist.add', $product)}}" method="POST" class="btn-group w-50">
            @csrf
            <input type="hidden" name="type" value="exists">
            <button type="submit" id="submit" class="btn btn-outline-warning">Notify</button>
        </form>
    @endif

</div>
