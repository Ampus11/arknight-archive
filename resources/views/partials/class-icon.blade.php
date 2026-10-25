@php
    $class = $class ?? 'Guard';
@endphp
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="square" stroke-linejoin="miter" aria-hidden="true" focusable="false">
    @switch($class)
        @case('Vanguard')
            <path d="M5 20V5l7-2v17M5 9h7" />
            <path d="M14 7l5 3v10M19 20h-5" />
            @break

        @case('Guard')
            <path d="M14 3h7v7L10 21H3v-7L14 3Z" />
            <path d="M12.5 6.5 17 11" />
            @break

        @case('Defender')
            <path d="M12 2.5 20 6v6.2c0 4.6-3.2 7.9-8 9.3-4.8-1.4-8-4.7-8-9.3V6l8-3.5Z" />
            <path d="M8.5 12h7" />
            @break

        @case('Sniper')
            <circle cx="12" cy="12" r="6.5" />
            <path d="M12 2v4M12 18v4M2 12h4M18 12h4" />
            <path d="M12 12h.01" />
            @break

        @case('Caster')
            <path d="M12 2.5 14.2 9l6.3 3-6.3 3L12 21.5 9.8 15 3.5 12l6.3-3L12 2.5Z" />
            <path d="M12 9.5v5" />
            @break

        @case('Medic')
            <path d="M12 3v18M3 12h18" />
            <path d="M5 5h14v14H5z" opacity=".35" />
            @break

        @case('Supporter')
            <path d="M4 19h16" />
            <path d="M6 19v-5M12 19V8M18 19V4" />
            <path d="M12 4.5 13.5 7 12 9.5 10.5 7 12 4.5Z" />
            @break

        @case('Specialist')
            <path d="M12 2.5 16.5 7 12 11.5 7.5 7 12 2.5Z" />
            <path d="M7 12.5 11 16.5 7 20.5 3 16.5 7 12.5Z" />
            <path d="M16.5 12.5 20.5 16.5 16.5 20.5 12.5 16.5 16.5 12.5Z" />
            @break

        @default
            <circle cx="12" cy="12" r="7" />
    @endswitch
</svg>
