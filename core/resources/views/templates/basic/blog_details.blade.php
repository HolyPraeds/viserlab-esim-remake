@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-details my-120">
        <div class="container">
            <div class="row gy-5 justify-content-center">
                <div class="col-lg-8 col-xl-9">
                    <div class="blog-details">
                        <div class="blog-details__thumb mb-3">
                            <img src="{{ frontendImage('blog', $blog->data_values->image, '1710x1000') }}" class="fit-image blog-details__top-image" alt="blog">
                        </div>
                        <div class="blog-details__content">
                            <span class="blog-item__date mb-2"><span class="blog-item__date-icon"><i class="las la-clock"></i></span>
                                {{ showDateTime($blog->created_at, 'd M Y') }}</span>
                            <h3 class="blog-details__title"> {{ __($blog->data_values->title) }} </h3>
                            <p class="blog-details__desc mb-3">
                                @php
                                    echo $blog->data_values->description;
                                @endphp
                            </p>

                            <div class="tag-share">
                                <div class="share">
                                    <div class="share-label">@lang('Share')</div>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" class="share-icon-button" target="_blank">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="https://x.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($blog->data_values->title) }}" class="share-icon-button" target="_blank">
                                        <i class="fab fa-x-twitter"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($blog->data_values->title) }}" class="share-icon-button" target="_blank">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&media={{ urlencode(frontendImage('blog', $blog->data_values->image)) }}&description={{ urlencode($blog->data_values->title) }}"
                                        class="share-icon-button" target="_blank">
                                        <i class="fab fa-pinterest"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-xl-3">
                    <div class="blog-sidebar">
                        <div class="blog-sidebar__header">
                            <h5 class="blog-page__search-title">
                                @lang('Latest Blogs')
                            </h5>
                        </div>
                        <div class="blog-sidebar__body">
                            @foreach ($latest as $blog)
                                <div class="recant-news__item">
                                    <a class="recant-news__thumb" href="{{ route('blog.details', $blog->slug) }}">
                                        <img class="recant-news__image" src="{{ frontendImage('blog', 'thumb_' . $blog->data_values?->image, '1710x1000') }}" alt="image">
                                    </a>
                                    <div class="recant-news__content">
                                        <h5 class="recant-news__title">
                                            <a href="{{ route('blog.details', $blog->slug) }}">
                                                {{ __($blog->data_values->title) }}
                                            </a>
                                        </h5>

                                        <span class="recant-news__date">
                                            <i class="las la-calendar"></i>
                                            {{ showDateTime($blog->created_at, 'd M Y') }}
                                        </span>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="fb-comments" data-href="{{ url()->current() }}" data-numposts="5"></div>
            </div>
        </div>
    </section>
@endsection
@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush
