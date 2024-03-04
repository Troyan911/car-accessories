@if(!$isFollowed)
    <form action="{{route('wishlist.add', $product)}}" method="POST" class="@if($minimized) w-50 @else w-100 @endif">
        @csrf
        <input type="hidden" name="type" value="price">
        <div class="mb-3 row w-100">
            <div class="col-sm-2">
                <button type="submit" id="price" class="btn btn-outline-success">
                    <i class="fa-solid fa-chart-line"></i>
                </button>
            </div>
            @unless($minimized)
                <label for="price" class="col-sm-9 col-form-label">Notify when price will be lower</label>
            @endunless
        </div>
    </form>
@else
    <form action="{{route('wishlist.remove', $product)}}" method="POST" class="@if($minimized) w-50 @else w-100 @endif">
        @csrf
        @method('delete')
        <input type="hidden" name="type" value="price">
        <div class="mb-3 row w-100">
            <div class="col-sm-2">
                <button type="submit" id="price" class="btn btn-outline-danger">
                    <i class="fa-solid fa-chart-line"></i>
                </button>
            </div>
            @unless($minimized)
                <label for="price" class="col-sm-9 col-form-label">Remove price subscription</label>
            @endunless
        </div>
    </form>
@endif
