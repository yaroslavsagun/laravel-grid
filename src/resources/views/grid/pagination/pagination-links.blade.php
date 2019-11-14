@if($grid->wantsPagination())
    <div class="float-right">
        {{ $grid->getData()->appends(request()->query())->links($grid->getGridPaginationView(), ['pjaxTarget' => $grid->getId()]) }}
    </div>
@endif