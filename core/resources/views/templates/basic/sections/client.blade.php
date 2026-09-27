@php
    $clientContent = getContent('client.content', true);
    $clientElements = getContent('client.element', false, orderById: true);
    $travelpalBenefits = [
        'Instant QR delivery',
        'No roaming surprises',
        '190+ destinations',
        'Flexible plans',
        'Secure checkout',
        '24/7 support',
        'One-tap activation',
    ];
@endphp

<section class="client-section py-60">
    <div class="container">
        <h6 class="text-center mb-4">{{ __($clientContent?->data_values?->title) }}</h6>
        @if (currentBrand('id') === 'travelpal')
            <div class="tp-benefits-strip" data-aos="fade-up" data-aos-duration="1000">
                <div class="tp-benefits-strip__inner">
                    @foreach (array_merge($travelpalBenefits, $travelpalBenefits, $travelpalBenefits) as $item)
                        <div class="tp-benefits-strip__item">
                            <span>{{ __($item) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="tp-benefits-grid">
                @foreach ($travelpalBenefits as $item)
                    <div class="tp-benefits-grid__item">
                        <span>{{ __($item) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="client-slider">
                @foreach ($clientElements as $clientElement)
                    <div class="client-slider__slide" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="{{ $loop->index * 50 }}">
                        <img class="client-slider__logo" src="{{ frontendImage('client', $clientElement->data_values?->image, '300x65') }}" alt="image">
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
