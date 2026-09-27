{{-- Brand strip ticker (Calm theme). Hidden when body does not have .theme-calm — see calm-theme.css --}}
<div class="calm-brand-strip" aria-hidden="true">
    <div class="calm-brand-strip__inner">
        @php
            $calmStripItems = [
                'PayerSim — travel eSIM',
                'Worldwide connectivity',
                '190+ destinations',
                'Fast QR delivery',
                'No hidden fees',
                'Secure checkout',
                'Weekday support',
            ];
        @endphp
        @foreach (array_merge($calmStripItems, $calmStripItems, $calmStripItems, $calmStripItems) as $item)
            <div class="calm-brand-strip__item">{{ $item }}</div>
        @endforeach
    </div>
</div>
