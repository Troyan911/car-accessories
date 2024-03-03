<form action="{{route('wishlist.add', $product)}}" method="POST" class="w-100">
    @csrf
    <input type="hidden" name="type" value="available">
    <div class="mb-3 row w-100">
        <div class="col-sm-2">
            <button type="submit" id="notify" class="btn btn-outline-warning">
                <i class="fa-regular fa-envelope"></i>
            </button>
        </div>
        <label for="notify" class="col-sm-9 col-form-label">Notify when product will be available</label>
    </div>
</form>

