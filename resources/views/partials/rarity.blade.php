@php
    $rarity = (int) ($rarity ?? 1);
@endphp
<span class="rarity-dots rarity-{{ $rarity }}" aria-label="Rarity {{ $rarity }}">@for ($i = 1; $i <= $rarity; $i++)<i class="dia"></i>@endfor</span>
