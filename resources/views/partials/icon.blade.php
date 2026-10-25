@php
    $name = $name ?? 'arrow';
    $class = $class ?? '';
@endphp
<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="square" stroke-linejoin="miter" aria-hidden="true" focusable="false">
    @switch($name)
        @case('search')
            <circle cx="11" cy="11" r="6.5" />
            <path d="M16 16l5 5" />
            @break

        @case('heart')
            <path d="M12 20.5S3.5 15.2 3.5 9.4A4.6 4.6 0 0 1 12 6.4a4.6 4.6 0 0 1 8.5 3c0 5.8-8.5 11.1-8.5 11.1Z" />
            @break

        @case('arrow')
            <path d="M4 12h15M13 6l6 6-6 6" />
            @break

        @case('external')
            <path d="M7 17 17 7M9 7h8v8" />
            @break

        @case('close')
            <path d="M6 6l12 12M18 6 6 18" />
            @break

        @case('reset')
            <path d="M4 4v6h6" />
            <path d="M4.8 10a8 8 0 1 0 1.6-4.7" />
            @break

        @case('chevron-left')
            <path d="M15 5l-7 7 7 7" />
            @break

        @case('chevron-right')
            <path d="M9 5l7 7-7 7" />
            @break

        @case('grid')
            <path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z" />
            @break

        @default
            <path d="M4 12h16M12 4v16" />
    @endswitch
</svg>
