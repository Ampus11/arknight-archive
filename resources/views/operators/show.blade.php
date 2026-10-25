@extends('layout')

@section('title', $operator->name.' | Operator Archive')
@section('description', 'Data operator '.$operator->name.' - '.$operator->class.', rarity '.$operator->rarity.', '.$operator->nation.'.')

@section('content')
    <div class="shell">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Archive</a>
            <span class="sep">/</span>
            <a href="{{ route('home', ['class' => $operator->class]) }}">{{ $operator->class }}</a>
            <span class="sep">/</span>
            <span>{{ $operator->subclass }}</span>
            <span class="sep">/</span>
            <span>{{ $operator->name }}</span>
        </nav>

        <article class="detail-hero rarity-{{ $operator->rarity }}">
            <div class="detail-hero__body">
                <p class="detail-hero__id">
                    <span class="bar"></span>
                    {{ $operator->display_number ?: 'NO-'.str_pad((string) $operator->id, 3, '0', STR_PAD_LEFT) }}
                    <span>/</span>
                    {{ strtoupper($operator->char_id ?: $operator->slug) }}
                </p>

                <h1 class="detail-name">{{ $operator->name }}</h1>

                <div class="detail-tagline">
                    @include('partials.rarity', ['rarity' => $operator->rarity])
                    <span class="class-name">
                        @include('partials.class-icon', ['class' => $operator->class])
                        {{ $operator->class }}
                    </span>
                    <span>{{ $operator->subclass }}</span>
                    <span class="sep">&middot;</span>
                    <span>{{ $operator->nation }}</span>
                    <span class="sep">&middot;</span>
                    <span>{{ $operator->position }}</span>
                </div>

                @if (filled($operator->description))
                    <p class="detail-desc">{{ $operator->description }}</p>
                @endif

                @if (filled($operator->usage))
                    <p class="detail-usage">{{ $operator->usage }}</p>
                @endif

                @if (! empty($operator->tags))
                    <ul class="tag-list" aria-label="Tag operator">
                        @foreach ($operator->tags as $tag)
                            <li>{{ $tag }}</li>
                        @endforeach
                    </ul>
                @endif

                <dl class="meta-grid">
                    <div class="meta-cell">
                        <dt>Class</dt>
                        <dd>{{ $operator->class }}</dd>
                    </div>
                    <div class="meta-cell">
                        <dt>Subclass</dt>
                        <dd>{{ $operator->subclass }}</dd>
                    </div>
                    <div class="meta-cell">
                        <dt>Nation</dt>
                        <dd>{{ $operator->nation }}</dd>
                    </div>
                    <div class="meta-cell">
                        <dt>Position</dt>
                        <dd>{{ $operator->position }}</dd>
                    </div>
                    <div class="meta-cell">
                        <dt>Elite Cap</dt>
                        <dd>E{{ max(0, $eliteStages - 1) }}</dd>
                    </div>
                </dl>

                <div class="detail-actions">
                    <a class="btn btn--solid" href="{{ route('planner.show', ['operator' => $operator->slug]) }}">
                        Buka Planner
                        @include('partials.icon', ['name' => 'arrow'])
                    </a>

                    <form method="POST" action="{{ route('favorites.toggle', $operator) }}">
                        @csrf
                        <button type="submit" class="btn {{ $isFavorite ? 'btn--signal' : '' }}">
                            @include('partials.icon', ['name' => 'heart'])
                            {{ $isFavorite ? 'Tersimpan' : 'Favorit' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="detail-hero__art" data-letter="{{ mb_strtoupper(mb_substr($operator->name, 0, 1)) }}">
                <img
                    src="{{ $operator->artUrl() }}"
                    alt="Full art {{ $operator->name }}"
                    fetchpriority="high"
                    decoding="async"
                >
            </div>
        </article>

        <div class="detail-layout">
            <section class="panel">
                <header class="panel__head">
                    <div class="panel__title-group">
                        <span class="panel__index">01 / Assets</span>
                        <h2 class="panel__title">Promotion Cost</h2>
                    </div>
                    <span class="panel__hint">LMD + material per elite</span>
                </header>

                @if ($promotions === [])
                    <div class="panel__body">
                        <p class="muted small">Operator ini tidak memiliki tahap promotion. Level maksimumnya sudah tercapai pada Elite 0.</p>
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Stage</th>
                                    <th>Level Cap</th>
                                    <th>LMD</th>
                                    <th>Material</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($promotions as $promotion => $cost)
                                    <tr>
                                        <td>
                                            <strong>Elite {{ $promotion }}</strong>
                                            <div class="mono small muted">E{{ $promotion - 1 }} &rarr; E{{ $promotion }}</div>
                                        </td>
                                        <td class="num">{{ $cost['max_level'] }} &rarr; {{ $cost['cap'] }}</td>
                                        <td class="num">{{ number_format($cost['lmd']) }}</td>
                                        <td>
                                            @if ($cost['materials'] === [])
                                                <span class="muted small">&mdash;</span>
                                            @else
                                                <ul class="mat-chips">
                                                    @foreach ($cost['materials'] as $material)
                                                        <li class="mat-chip" style="--tier-color: {{ $material['color'] }}">
                                                            <span class="mat-chip__tier">T{{ $material['tier'] }}</span>
                                                            <span>{{ $material['name'] }}</span>
                                                            <span class="mat-chip__qty">&times;{{ $material['qty'] }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="note">
                        Material di atas memakai estimasi generik berdasarkan rarity dan kelas. Selalu cek event atau stage terbaru sebelum farming.
                    </p>
                @endif
            </section>

            <section class="panel">
                <header class="panel__head">
                    <div class="panel__title-group">
                        <span class="panel__index">02 / Comms</span>
                        <h2 class="panel__title">Doctor Notes</h2>
                    </div>
                    <span class="panel__hint">{{ $comments->count() }} entri</span>
                </header>

                @if ($comments->isEmpty())
                    <div class="panel__body">
                        <p class="muted small">Belum ada catatan. Jadi Doctor pertama yang meninggalkan strategi untuk operator ini.</p>
                    </div>
                @else
                    <ul class="comment-list">
                        @foreach ($comments as $comment)
                            <li class="comment">
                                <div class="comment__head">
                                    <span class="comment__avatar">{{ mb_strtoupper(mb_substr($comment->doctor_name, 0, 1)) }}</span>
                                    <span class="comment__name">{{ $comment->doctor_name }}</span>
                                    <span class="comment__time">{{ $comment->created_at?->diffForHumans() }}</span>
                                </div>
                                <p class="comment__body">{{ $comment->body }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form class="comment-form" method="POST" action="{{ route('comments.store', $operator) }}">
                    @csrf
                    <label class="field">
                        <span>Doctor Name</span>
                        <input
                            class="input"
                            type="text"
                            name="doctor_name"
                            value="{{ old('doctor_name', session('doctor_name')) }}"
                            maxlength="20"
                            minlength="2"
                            required
                        >
                    </label>

                    <label class="field">
                        <span>Catatan / Strategi</span>
                        <textarea class="input" name="body" maxlength="1000" minlength="3" required>{{ old('body') }}</textarea>
                    </label>

                    <button class="btn btn--solid btn--block" type="submit">
                        Kirim Catatan
                        @include('partials.icon', ['name' => 'arrow'])
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
