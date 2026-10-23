@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" style="display:flex; flex-direction:column; align-items:center; gap:10px; margin:20px 0 10px; width:100%;">
        <div style="font-size:12px; color:#667085; font-weight:600;">
            Menampilkan <span style="font-weight:800; color:#101828;">{{ $paginator->firstItem() }}</span> – <span style="font-weight:800; color:#101828;">{{ $paginator->lastItem() }}</span> dari <span style="font-weight:800; color:#101828;">{{ $paginator->total() }}</span> akun
        </div>

        <div style="display:inline-flex; align-items:center; gap:6px; background:#ffffff; padding:6px 8px; border-radius:14px; border:1px solid #E4E7EC; box-shadow:0 2px 8px rgba(16,24,40,0.04);">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; color:#D0D5DD; background:#F8F9FC; border:1px solid #EAECF0; cursor:not-allowed;" aria-disabled="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; color:#1455C0; background:#F0F9FF; border:1px solid #B9E6FE; text-decoration:none;" aria-label="Halaman Sebelumnya">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </a>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; color:#98A2B3; font-weight:700;">{{ $element }}</span>
                @endif

                {{-- Array of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:#1455C0; color:#ffffff; font-weight:800; font-size:13px; box-shadow:0 2px 6px rgba(20,85,192,0.3);" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:#F8F9FC; border:1px solid #E4E7EC; color:#344054; font-weight:700; font-size:13px; text-decoration:none;">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Selanjutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; color:#1455C0; background:#F0F9FF; border:1px solid #B9E6FE; text-decoration:none;" aria-label="Halaman Selanjutnya">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            @else
                <span style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; color:#D0D5DD; background:#F8F9FC; border:1px solid #EAECF0; cursor:not-allowed;" aria-disabled="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
