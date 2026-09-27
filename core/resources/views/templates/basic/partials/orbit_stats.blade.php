{{-- Stats ticker (Orbit theme). Hidden when body does not have .theme-orbit — see orbit-theme.css --}}
<div class="orbit-stats" aria-hidden="true">
    <div class="orbit-stats__inner">
        @php
            $orbitStats = [
                ['value' => '190+', 'label' => 'countries & regions · PayerSim'],
                ['value' => '100K+', 'label' => 'active connections'],
                ['value' => '99.9%', 'label' => 'service uptime'],
                ['value' => '<2 min', 'label' => 'to eSIM ready'],
            ];
        @endphp
        @foreach (array_merge($orbitStats, $orbitStats, $orbitStats, $orbitStats) as $s)
            <div class="orbit-stats__item">
                <span class="orbit-stats__value">{{ $s['value'] }}</span>
                <span class="orbit-stats__label">{{ $s['label'] }}</span>
            </div>
        @endforeach
    </div>
</div>
