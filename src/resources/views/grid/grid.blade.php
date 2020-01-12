@extends($grid->getRenderingTemplateToUse())
@section('data')
    <div class="row">
        <div class="col-md-{{ $grid->getGridToolbarSize()[0] }}">
            @if ($grid->allowsBulkDelete())
                <button id="grid-select-all" type="button" class="btn btn-success" title="Select all items on page">
                    <i class="fa fa-square"></i>
                    Select All
                </button>

                <a href="{{ $grid->getMultipleDeleteUrl() }}" id="grid-delete-selected" class="data-remote btn btn-danger disabled" data-trigger-pjax="1" data-trigger-confirm="1" data-bulk-action="1" data-pjax-target="#product-grid">
                    <i class="fa fa-trash-alt"></i>
                    Delete Selected
                </a>
            @endif

            @if($grid->shouldRenderSearchForm())
                {!! $grid->renderSearchForm() !!}
            @endif
        </div>
        @if($grid->hasButtons('toolbar'))
            <div class="col-md-{{ $grid->getGridToolbarSize()[1] }}">
                <div class="float-right">
                    @foreach($grid->getButtons('toolbar') as $button)
                        {!! $button->render() !!}
                    @endforeach

                    <div class="page-size-wrapper">
                        <label for="grid-filter-page_size" title="Applies with filters">Page Size: </label>
                        <select name="page_size" id="grid-filter-page_size" form="{{ $grid->getFilterFormId() }}" class=""
                                title="Applies with filters">
                            @foreach([50 => 50, 100 => 100, 200 => 200, 500 => 500] as $k => $v)
                                @if((request('page_size') !== null && request('page_size') == $k) || $grid->getGridPaginationPageSize() == $k)
                                    <option value="{{ $k }}" selected>{{ $v }}</option>
                                @else
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="btn btn-info grid-filter-button"
                            title="Filter grid results by applied criterias"
                            form="{{ $grid->getFilterFormId() }}">Apply Filters&nbsp;<i class="fa fa-filter"></i>
                    </button>
                </div>
            </div>
        @endif

    </div>
    <form action="{{ $grid->getSearchUrl() }}" method="GET" id="{{ $grid->getFilterFormId() }}"></form>
    <div class="table-responsive grid-wrapper">
        <table class="{{ $grid->getClass() }}">
            <thead class="{{ $grid->getHeaderClass() }}">
            <tr class="filter-header">
                @if ($grid->allowsBulkDelete())
                    <th></th>
                @endif
                @foreach($columns as $column)

                    @if($loop->first)

                        @if($column->isSortable)
                            <th scope="col"
                                class="{{ is_callable($column->columnClass) ? call_user_func($column->columnClass) : $column->columnClass }}"
                                title="click to sort by {{ $column->key }}">
                                <a data-trigger-pjax="1" class="data-sort"
                                   href="{{ $grid->getSortUrl($column->key, $grid->getSelectedSortDirection()) }}">
                                    @if($column->useRawHtmlForLabel)
                                        {!! $column->name !!}
                                    @else
                                        {{ $column->name }}
                                    @endif
                                </a>
                            </th>
                        @else
                            <th class="{{ is_callable($column->columnClass) ? call_user_func($column->columnClass) : $column->columnClass }}">
                                @if($column->useRawHtmlForLabel)
                                    {!! $column->name !!}
                                @else
                                    {{ $column->name }}
                                @endif
                            </th>
                        @endif
                    @else
                        @if($column->isSortable)
                            <th scope="col" title="click to sort by {{ $column->key }}"
                                class="{{ is_callable($column->columnClass) ? call_user_func($column->columnClass) : $column->columnClass }}">
                                <a data-trigger-pjax="1" class="data-sort"
                                   href="{{ $grid->getSortUrl($column->key, $grid->getSelectedSortDirection()) }}">
                                    @if($column->useRawHtmlForLabel)
                                        {!! $column->name !!}
                                    @else
                                        {{ $column->name }}
                                    @endif
                                </a>
                            </th>
                        @else
                            <th scope="col"
                                class="{{ is_callable($column->columnClass) ? call_user_func($column->columnClass) : $column->columnClass }}">
                                @if($column->useRawHtmlForLabel)
                                    {!! $column->name !!}
                                @else
                                    {{ $column->name }}
                                @endif
                            </th>
                        @endif
                    @endif
                @endforeach
                {{--<th></th>--}}
            </tr>
            @if($grid->shouldRenderGridFilters())
                <tr>
                    @if ($grid->allowsBulkDelete())
                        <td></td>
                    @endif
                    {!! $grid->renderGridFilters() !!}
                </tr>
            @endif
            </thead>
            <tbody>
            @if($grid->hasItems())
                @if($grid->warnIfEmpty())
                    <div class="alert alert-warning" role="alert">
                        <strong><i class="fa fa-exclamation-triangle"></i>&nbsp;No data present!.</strong>
                    </div>
                @endif
            @else
                @foreach($grid->getData() as $item)
                    @if($grid->allowsLinkableRows())
                        @php
                            $callback = call_user_func($grid->getLinkableCallback(), $grid->transformName(), $item);
                        @endphp
                        @php
                            $trClassCallback = call_user_func($grid->getRowCssStyle(), $grid->transformName(), $item);
                        @endphp
                        <tr class="{{ trim("linkable " . $trClassCallback) }}" data-url="{{ $callback }}">
                    @else
                        @php
                            $trClassCallback = call_user_func($grid->getRowCssStyle(), $grid->transformName(), $item);
                        @endphp
                        <tr class="{{ $trClassCallback }}">
                            @endif
                            @if ($grid->allowsBulkDelete())
                                <td class="selection"><input data-id="{{ $item->id }}" type="checkbox"/></td>
                            @endif
                            @foreach($columns as $column)
                                @if($column->isLinkable())
                                    @php
                                        $columnCallback = call_user_func($grid->getLinkableCallback(), $grid->transformName(), $item);
                                    @endphp
                                @endif

                                @if(is_callable($column->data))
                                    @if($column->useRawFormat)
                                        <td class="{{ $column->rowClass }}">
                                            @if($column->isLinkable())
                                                <a href="{{ $columnCallback }}">{!! call_user_func($column->data, $item, $column->key) !!}</a>
                                            @else
                                                {!! call_user_func($column->data, $item, $column->key) !!}
                                            @endif
                                        </td>
                                    @else
                                        <td class="{{ $column->rowClass }}">
                                            @if($column->isLinkable())
                                                <a href="{{ $columnCallback }}">{{ call_user_func($column->data, $item, $column->key) }}</a>
                                            @else
                                                {{ call_user_func($column->data, $item, $column->key) }}
                                            @endif
                                        </td>
                                    @endif
                                @else
                                    @if($column->useRawFormat)
                                        <td class="{{ $column->rowClass }}">
                                            @if($column->isLinkable())
                                                <a href="{{ $columnCallback }}">{!! $item->{$column->key} !!}</a>
                                            @else
                                                {!! $item->{$column->key} !!}
                                            @endif
                                        </td>
                                    @else
                                        <td class="{{ $column->rowClass }}">
                                            @if($column->isLinkable())
                                                <a href="{{ $columnCallback }}">{{ $item->{$column->key} }}</a>
                                            @else
                                                {{ $item->{$column->key} }}
                                            @endif
                                        </td>
                                    @endif
                                @endif
                                {{--@if($loop->last && $grid->hasButtons('rows'))--}}
                                {{--<td>--}}
                                {{--<div class="float-right">--}}
                                {{--@foreach($grid->getButtons('rows') as $button)--}}
                                {{--@if(call_user_func($button->renderIf, $grid->transformName(), $item))--}}
                                {{--{!! $button->render(['gridName' => $grid->transformName(), 'gridItem' => $item]) !!}--}}
                                {{--@else--}}
                                {{--@continue--}}
                                {{--@endif--}}
                                {{--@endforeach--}}
                                {{--</div>--}}
                                {{--</td>--}}
                                {{--@endif--}}
                            @endforeach
                        </tr>
                        @endforeach
                        @if($grid->shouldShowFooter())
                            <tr class="{{ $grid->getGridFooterClass() }}">
                                @foreach($columns as $column)
                                    @if($column->footer === null)
                                        <td></td>
                                    @else
                                        <td>
                                            <b>{{ call_user_func($column->footer) }}</b>
                                        </td>
                                    @endif
                                    @if($loop->last)
                                        <td></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endif
                    @endif
            </tbody>
        </table>
    </div>
@endsection
@push('grid_js')
    <script>
        (function ($) {
            let grid = "{{ '#' . $grid->getId() }}";
            let filterForm = "{{ '#' . $grid->getFilterFormId() }}";
            let searchForm = "{{ '#' . $grid->getSearchFormId() }}";
            _grids.grid.init({
                id: grid,
                filterForm: filterForm,
                dateRangeSelector: '.date-range',
                searchForm: searchForm,
                pjax: {
                    pjaxOptions: {
                        scrollTo: false,
                    },
                    // what to do after a PJAX request. Js plugins have to be re-intialized
                    afterPjax: function (e) {
                        _grids.init();
                    },
                },
            });
        })(jQuery);
    </script>
@endpush