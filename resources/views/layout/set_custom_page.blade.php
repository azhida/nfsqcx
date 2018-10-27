{{--设置自定义分页--}}
@section('set_custom_page')
<div class="page">
    <div>
        <a class="prev" href="javascript:getSetCustomPageData({{ $list->currentPage() == 1 ? 1 : $list->currentPage() - 1 }})">&lt;&lt;</a>

        @if($list->currentPage() > 1)
        <a class="num" href="javascript:getSetCustomPageData(1)">1</a>
        @endif

        @if($list->currentPage() - 3 > 1)
            <span class="num">...</span>
        @endif

        @for($i = $list->currentPage() - 2; $i <= $list->currentPage() + 2; $i++)
            @if($i == $list->currentPage())
                <span class="current">{{ $i }}</span>
            @else
                @if($i > 1 && $i < $list->lastPage())
                    <a class="num" href="javascript:getSetCustomPageData({{ $i }})">{{ $i }}</a>
                @endif
            @endif
        @endfor

        @if($list->currentPage() + 3 < $list->lastPage())
            <span class="num">...</span>
        @endif

        @if($list->currentPage() < $list->lastPage())
            <a class="num" href="javascript:getSetCustomPageData({{ $list->lastPage() }})">{{ $list->lastPage() }}</a>
        @endif

        <a class="next" href="javascript:getSetCustomPageData({{ $list->currentPage() == $list->lastPage() ? $list->lastPage() : $list->currentPage() + 1 }})">&gt;&gt;</a>
    </div>
</div>
<script>
    function getSetCustomPageData(page_num) {
        var search_params = $('#search_form').serialize();
        var url = '{{ $list->url(1) }}';
        var url_array = url.split('?');
        var url = url_array[0];
        window.location.href = url + '?page=' + page_num + '&' + search_params;
        return false;
    }
</script>
@show