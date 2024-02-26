<a href="{{route('categories.show', $category)}}" type="button"
   class="btn btn-outline-dark {{!empty($classes) ? $classes : ''}}">
    {{$category->name}}
</a>
