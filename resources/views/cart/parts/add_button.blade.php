<form action="{{route('cart.add', $product)}}" method="POST" class="w-100">
    @csrf
    <button type="submit" class="btn @if($product->isExists) btn-outline-success @else btn-outline-secondary @endif  w-100" @if(!$product->isExists) disabled @endif>Buy</button>
</form>
