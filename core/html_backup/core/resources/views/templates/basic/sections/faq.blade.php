@php
    $faqContent = getContent('faq.content', true);
    $faqElements = getContent('faq.element', false, 4, true);
@endphp

<section class="faq-section my-120">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-lg-6">
                <div class="faq-content">
                    <div class="section-heading style-left">
                        <h2 class="section-heading__title" data-underline-word="3" data-aos="fade-up" data-aos-duration="1500">{{ __($faqContent?->data_values?->subheading) }}</h2>
                        <p class="section-heading__desc" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="200">{{ __($faqContent?->data_values?->description) }}</p>
                    </div>
                    <a class="btn btn--base d-none d-lg-inline-block" href="{{ route('contact') }}" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="230">
                        @lang('Get In Touch')
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="accordion custom--accordion" id="faq-accordion">
                    @foreach ($faqElements as $index => $element)
                        <div class="accordion-item" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="{{ $index * 10 }}">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-collapse-{{ $index }}" aria-expanded="false" aria-controls="faq-collapse-{{ $index }}">
                                    {{ __($element->data_values?->question) }}
                                </button>
                            </h2>
                            <div id="faq-collapse-{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="faq-collapse-{{ $index }}" data-bs-parent="#faq-accordion">
                                <div class="accordion-body">
                                    <p class="accordion-text">{{ __($element->data_values?->answer) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 d-lg-none text-center">
                    <a class="btn btn--lg btn--base" href="{{ route('contact') }}">
                        @lang('Get In Touch')
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
