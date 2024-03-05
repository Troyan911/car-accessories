<div class="col">
    <div class="card shadow-sm">
        <div class="bd-placeholder-img card-img-top product-preview-image" style="background-image: url('{{$product->thumbnailUrl}}')"></div>
        <div class="card-body">
            <h5 class="card-title">{{$product->title}}</h5>
            @if($product->discount)
                <small class="text-body-secondary product-preview-discount">
                    <span style="color: orangered;">Discount: {{$product->discount}}%</span></small>
            @endif
            <p class="text-body-secondary product-preview-price">{{$product->finalPrice}} $</p>
            <div class="d-flex justify-content-between align-items-center">
                    @include('products.parts.card_buttons', ['product' => $product])
            </div>
        </div>
    </div>
</div>
