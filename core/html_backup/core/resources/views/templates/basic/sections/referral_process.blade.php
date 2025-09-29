@php
    $referralContent = getContent('referral_process.content', true);
    $referralElements = getContent('referral_process.element', false, orderById: true);
@endphp

<section class="work-process-section my-120">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading">
                    <h2 class="section-heading__title mb-3" data-underline-word="3" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="100">{{ __($referralContent?->data_values?->subheading) }}</h2>
                    <p class="section-heading__desc" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="300" >{{ __($referralContent?->data_values?->description) }}</p>
                </div>
            </div>
        </div>
        <div class="row gy-4 gx-xl-5 justify-content-center">
            @foreach ($referralElements as $referralElement)
                <div class="col-xsm-6 col-sm-6 col-lg-4 " data-aos="fade-up" data-aos-duration="1500" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="wotk-process_item">
                        <div class="wotk-process_item__image">
                            <img src="{{ frontendImage('referral_process', $referralElement->data_values?->image, '715x520') }}" alt="work-process">
                        </div>

                        <div class="wotk-process_item__content">
                            <div class="wotk-process_item__count">
                                <p class="wotk-process_item__counts">{{ $loop->iteration }}</p>
                            </div>

                            <h5 class="wotk-process_item__desc m-0">{{ __($referralElement->data_values?->title) }}</h5>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
