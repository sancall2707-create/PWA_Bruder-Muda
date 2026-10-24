@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" style="display:flex; justify-content:space-between; align-items:center; width:100%; margin:16px 0;">
        {{-- Tombol Sebelumnya --}}
        @if ($paginator->onFirstPage())
            <span style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; color:#D0D5DD; background:#F8F9FC; border:1px solid #EAECF0; font-size:12px; font-weight:700; cursor:not-allowed;" aria-disabled="true">
                &larr; Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; color:#1455C0; background:#F0F9FF; border:1px solid #B9E6FE; font-size:12px; font-weight:700; text-decoration:none;">
                &larr; Sebelumnya
            </a>
        @endif

        {{-- Tombol Selanjutnya --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; color:#1455C0; background:#F0F9FF; border:1px solid #B9E6FE; font-size:12px; font-weight:700; text-decoration:none;">
                Selanjutnya &rarr;
            </a>
        @else
            <span style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; color:#D0D5DD; background:#F8F9FC; border:1px solid #EAECF0; font-size:12px; font-weight:700; cursor:not-allowed;" aria-disabled="true">
                Selanjutnya &rarr;
            </span>
        @endif
    </nav>
@endif
