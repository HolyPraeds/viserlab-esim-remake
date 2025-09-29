@php
    $referralContent = getContent('referral.content', true);
@endphp

<section class="refer-section my-120">
    <div class="container">
        <div class="refer-section__inner">
            <div class="row gy-4">
                <div class="col-lg-6">
                    <img class="refer-thumb"
                        src="{{ asset('assets/images/frontend/referral/68c8ad918b0751757982097.png') }}"
                        alt="refer-image">
                </div>
                <div class="col-lg-6">
                    <div class="refer-content">
                        <div class="section-heading style-left">
                            <h2 class="section-heading__title" data-underline-word="5" data-aos="fade-up" data-aos-duration="1500">
                                {{ __($referralContent?->data_values?->subheading) }}</h2>
                            <p class="section-heading__desc" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="200">
                                {{ __($referralContent?->data_values?->description) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
