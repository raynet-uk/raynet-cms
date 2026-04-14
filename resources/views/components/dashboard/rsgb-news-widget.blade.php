<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
        Latest RSGB News
    </div>
    <div class="card-body">
        @php
            $data = cache('rsgb_news', ['headlines' => [], 'updated_at' => null, 'error' => null]);
        @endphp

        @if (!empty($data['error']))
            <div class="alert alert-warning">
                ⚠ {{ $data['error'] }}
            </div>
        @elseif (empty($data['headlines']))
            <p class="text-muted">No recent news items</p>
        @else
            <ul class="list-unstyled mb-0">
                @foreach ($data['headlines'] as $item)
                    <li class="mb-2">
                        <a href="{{ $item['link'] }}" target="_blank" rel="noopener noreferrer">
                            {{ $item['title'] }}
                        </a>
                        <small class="text-muted">({{ $item['date'] }})</small>
                    </li>
                @endforeach
            </ul>
            <small class="text-muted d-block mt-3">
                Updated: {{ $data['updated_at'] ?? '—' }}
            </small>
        @endif
    </div>
</div>