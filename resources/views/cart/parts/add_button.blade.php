<form action="{{route('cart.add', $product)}}" method="POST" class="w-25">
    @csrf
    <button type="submit" class="btn btn-outline-success w-100">Buy</button>
</form>
