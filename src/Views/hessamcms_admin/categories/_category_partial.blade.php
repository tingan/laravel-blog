@foreach($category_tree as $category)
    <li>
         <span value='{{$category->category_id}}'>
              @php
              mongo_debug('ddd', $category, $language_id);
              @endphp
        {{isset($category->categoryTranslations->first()->category_name) ? $category->categoryTranslations->first()->category_name : ''}}
        </span>
        @if( count($category->siblings) > 0)
            <ul>
                @include("hessamcms_admin::categories._category_partial", ['category_tree' => $category->siblings])
            </ul>
        @endif
    </li>
@endforeach
