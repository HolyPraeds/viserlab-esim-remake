@php
    $blogContent = getContent('blog.content', true);
    $blogElements = getContent('blog.element', false, orderById: false)->take(3);
@endphp

<section class="blog-section my-120">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading">
                    <h2 class="section-heading__title" data-underline-word="3" data-aos="fade-up" data-aos-duration="1500">{{ __($blogContent?->data_values?->subheading) }}</h2>
                    <p class="section-heading__desc" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="200">{{ __($blogContent?->data_values?->description) }}</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            @foreach ($blogElements as $blogElement)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="blog-section__item position-relative m-0">
                        <div class="blog-section__thumb">
                            <a href="{{ route('blog.details', $blogElement->slug) }}">
                                <img class="blog-section__image" src="{{ frontendImage('blog', 'thumb_' . $blogElement->data_values?->image, '1710x1000') }}" alt="blog">
                            </a>
                        </div>
                        <div class="blog-section__content">
                            <p class="blog-section__date">{{ showDateTime($blogElement->created_at, 'd M Y') }}</p>
                            <h4 class="blog-section__title">{{ __($blogElement->data_values?->title) }}</h4>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
