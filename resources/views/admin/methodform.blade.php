

<form method="POST" action="{{route("admin.categories.destroy", $category)}}">
    @csrf
    @method("DELETE")
    <button class="btn btn-danger" type="submit" href=""><i class="fa-solid fa-trash"></i></button>
</form>
