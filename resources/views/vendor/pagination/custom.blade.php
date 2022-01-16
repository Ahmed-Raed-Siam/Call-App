@if ($paginator->hasPages())
    {{--        <div class="float-left">--}}
    {{--            <div class="dataTables_info" role="status" aria-live="polite">--}}
    {{--                <p class="">--}}
    {{--                    {!! __('Showing') !!}--}}
    {{--                    <span class="">{{ $paginator->firstItem() }}</span>--}}
    {{--                    {!! __('to') !!}--}}
    {{--                    <span class="">{{ $paginator->lastItem() }}</span>--}}
    {{--                    {!! __('of') !!}--}}
    {{--                    <span class="">{{ $paginator->total() }}</span>--}}
    {{--                    {!! __('results') !!}--}}
    {{--                </p>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    <div class="dataTables_paginate paging_simple_numbers">
        <ul class="pagination">

            @if ($paginator->onFirstPage())
                <li class="paginate_button page-item previous disabled"><span class="page-link">Previous</span>
                </li>
            @else
                <li class="paginate_button page-item previous"><a class="page-link"
                                                                  href="{{ $paginator->previousPageUrl() }}"
                                                                  rel="prev">Previous</a></li>
            @endif



            @foreach ($elements as $element)

                @if (is_string($element))
                    <li class="disabled"><span class="page-link">{{ $element }}</span></li>
                @endif



                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <li class="paginate_button page-item active"><span
                                    class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach



            @if ($paginator->hasMorePages())
                <li class="paginate_button page-item next"><a class="page-link"
                                                              href="{{ $paginator->nextPageUrl() }}"
                                                              rel="next">Next</a></li>
            @else
                <li class="paginate_button page-item next disabled"><span class="page-link">Next</span></li>
            @endif
        </ul>
    </div>
@endif
