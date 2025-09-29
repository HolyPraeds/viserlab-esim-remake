@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-page-section my-120">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-12">
                    <div class="row gy-4">
                        @forelse ($blogs as $blog)
                            <div class="col-md-4" data-aos="fade-up" data-aos-duration="1500">
                                <div class="blog-section__item position-relative m-0">
                                    <div class="blog-section__thumb">
                                        <a href="{{ route('blog.details', $blog->slug) }}"><img class="blog-section__image" src="{{ frontendImage('blog', 'thumb_' . $blog->data_values->image, '1710x1000') }}"  alt="blog"></a>
                                    </div>
                                    <div class="blog-section__content">
                                        <p class="blog-section__date">{{ showDateTime($blog->created_at, 'd M Y') }}</p>
                                        <h4 class="blog-section__title"><a href="{{ route('blog.details', $blog->slug) }}">{{ __($blog->data_values->title) }}</a>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <x-empty-message message="No blog post found" />
                        @endforelse
                    </div>
                </div>
            </div>
            @if ($blogs->hasPages())
                <div class="pagination-wrapper">
                    {{ paginateLinks($blogs) }}
                </div>
            @endif
        </div>
    </section>


    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
