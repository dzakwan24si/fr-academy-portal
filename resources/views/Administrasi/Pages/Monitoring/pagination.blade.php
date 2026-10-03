@if ($paginator->hasPages() || $paginator->total() > 0)
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3 text-sm text-gray-500">
            <span>Menampilkan <span class="font-semibold text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span> hingga <span class="font-semibold text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span> data</span>
            <form method="GET" action="{{ url()->current() }}" class="inline-block">
                @foreach(request()->except('per_page') as $key => $val)
                    @if(is_array($val))
                        @foreach($val as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endif
                @endforeach
                <select name="per_page" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-lg focus:ring-[#10B981] focus:border-[#10B981] py-1.5 pl-3 pr-8 shadow-sm text-gray-700 bg-white">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Baris</option>
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 Baris</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Baris</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Baris</option>
                </select>
            </form>
        </div>

        @if ($paginator->hasPages())
        <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" aria-label="@lang('pagination.previous')">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">{{ $element }}</span>
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="relative inline-flex items-center px-4 py-2 border border-[#10B981] bg-[#10B981] text-sm font-medium text-white">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" aria-label="@lang('pagination.next')">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </span>
                @endif
            </nav>
        </div>
        @endif
    </div>
@endif
