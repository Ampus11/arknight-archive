@php
    $isFavorite = $favoriteIds->contains($operator->id);
    $index = $operator->display_number ?: strtoupper(substr((string) $operator->char_id, 5, 6));
@endphp
<article class="op-card rarity-{{ $operator->rarity }}">
    <div class="op-card__art">
        <img src="{{ $operator->cardUrl() }}" alt="Ilustrasi {{ $operator->name }}" loading="lazy" decoding="async">
    </div>
    <span class="op-card__scan" aria-hidden="true"></span>

    <div class="op-card__top">
        <span class="op-card__class">
            @include('partials.class-icon', ['class' => $operator->class])
            {{ $operator->class }}
        </span>
        <span class="op-card__num">{{ $index }}</span>
    </div>

    <span class="op-card__go" aria-hidden="true">OPEN @include('partials.icon', ['name' => 'external'])</span>

    <div class="op-card__favorite">
        <form method="POST" action="{{ route('favorites.toggle', $operator) }}">
            @csrf
            <button type="submit" class="icon-btn {{ $isFavorite ? 'is-on' : '' }}" title="{{ $isFavorite ? 'Hapus dari favorit' : 'Tambah ke favorit' }}" aria-label="{{ $isFavorite ? 'Hapus '.$operator->name.' dari favorit' : 'Tambah '.$operator->name.' ke favorit' }}">
                @include('partials.icon', ['name' => 'heart'])
            </button>
        </form>
    </div>

    <a class="op-card__link" href="{{ route('operators.show', $operator) }}" aria-label="Buka detail {{ $operator->name }}"></a>

    <div class="op-card__info">
        <p class="op-card__sub">{{ $operator->subclass }} &middot; {{ $operator->nation }}</p>
        <h2 class="op-card__name">{{ $operator->name }}</h2>
        <div class="op-card__bottom">
            @include('partials.rarity', ['rarity' => $operator->rarity])
            <span class="op-card__pos">{{ $operator->position }}</span>
        </div>
    </div>
</article>
