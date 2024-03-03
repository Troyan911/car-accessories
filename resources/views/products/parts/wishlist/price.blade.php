<form action="{{route('wishlist.add', $product)}}" method="POST" class="w-100">
    @csrf
    <input type="hidden" name="type" value="price">
    <div class="mb-3 row w-100">
        <div class="col-sm-2">
            <button type="submit" id="price" class="btn btn-outline-success">
                <i class="fa-solid fa-chart-line"></i>
            </button>
        </div>
        <label for="price" class="col-sm-9 col-form-label">Notify when price will be lower</label>
    </div>
</form>
