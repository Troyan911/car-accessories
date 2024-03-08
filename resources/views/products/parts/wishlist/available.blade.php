@if(!$product->isExists && !$isFollowed)
    <form action="{{route('wishlist.add', $product)}}" method="POST" class="@if($minimized) w-50 @else w-100 @endif">
        @csrf
        <input type="hidden" name="type" value="available">
        <div class="mb-3 row w-100">
            <div class="col-sm-2">
                <button type="submit" id="notify" class="btn btn-outline-warning">
                    <i class="fa-regular fa-envelope"></i>
                </button>
            </div>
            @unless($minimized)
                <label for="notify" class="col-sm-9 col-form-label">Notify when product will be available</label>
            @endunless
        </div>
    </form>
@endif

@if($isFollowed)
    <form action="{{route('wishlist.add', $product)}}" method="POST" class="@if($minimized) w-50 @else w-100 @endif">
        @csrf
        @method('delete')
        <input type="hidden" name="type" value="available">
        <div class="mb-3 row w-100">
            <div class="col-sm-2">
                <button type="submit" id="notify" class="btn btn-outline-danger">
                    <i class="fa-regular fa-envelope"></i>
                </button>
            </div>
            @unless($minimized)
                <label for="notify" class="col-sm-9 col-form-label">Remove available subscription</label>
            @endunless
        </div>
    </form>
@endif

