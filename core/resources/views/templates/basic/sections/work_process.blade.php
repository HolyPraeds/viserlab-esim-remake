@php
    $workProcessContent = getContent('work_process.content', true);
    $workProcessElements = getContent('work_process.element', false, orderById: true);
@endphp

<section class="how-it-works my-120">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-6">
                <div class="section-heading style-left">
                    <h2 class="section-heading__title" data-underline-word="3" data-aos="fade-up" data-aos-duration="1500">{{ __($workProcessContent?->data_values?->subheading) }}</h2>
                    <p class="section-heading__desc" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="200">{{ __($workProcessContent?->data_values?->description) }}</p>
                </div>

                <div class="how-it-works-tab" id="how-it-works-tab" role="tablist">
                    @foreach ($workProcessElements as $index => $workProcessElement)
                        <div class="how-it-works-tab-item {{ $loop->iteration == 1 ? 'active' : '' }}" id="how-it-works-tab-item-{{ $loop->iteration }}" data-bs-toggle="tab"
                            data-bs-target="#how-it-works-tab-pane-{{ $loop->iteration }}" role="tab" aria-controls="how-it-works-tab-item-{{ $loop->iteration }}" aria-selected="true">
                            <div class="how-it-works-tab-item__icon">
                                @php
                                    echo $workProcessElement->data_values?->icon;
                                @endphp
                            </div>
                            <div class="how-it-works-tab-item__content">
                                <h6 class="how-it-works-tab-item__title">
                                    {{ __($workProcessElement->data_values?->title) }}
                                </h6>
                                <p class="how-it-works-tab-item__desc">
                                    {{ __($workProcessElement->data_values?->short_description) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="how-it-works-tab-content tab-content">
                    @foreach ($workProcessElements as $index => $workProcessElement)
                        <div class="tab-pane fade {{ $loop->iteration == 1 ? 'show active' : '' }}" id="how-it-works-tab-pane-{{ $loop->iteration }}" role="tabpanel"
                            aria-labelledby="how-it-works-tab-item-{{ $loop->iteration }}" tabindex="0">
                            <img class="how-it-works__thumb" src="{{ frontendImage('work_process', $workProcessElement->data_values?->image, '1280x1140') }}" alt="work-process">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@push('script')
    <script>
        (function($) {
            const items = document.querySelectorAll(".how-it-works-tab-item");
            let currentIndex = 1;

            function activateNextItem() {
                const tab = new bootstrap.Tab(items[currentIndex]);
                tab.show();

                currentIndex = (currentIndex + 1) % items.length;
            }

            setInterval(activateNextItem, 5000);

        })(jQuery);
    </script>
@endpush
