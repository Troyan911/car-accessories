<div class="row row-cols-md-5 g-6 mb-5 justify-content-center">
    @foreach($categories as $category)
        <div class="col d-flex align-items-center mb-3">
            @include('categories.parts.button', ['category' => $category, 'classes' => 'w-100'])
        </div>
    @endforeach
</div>

