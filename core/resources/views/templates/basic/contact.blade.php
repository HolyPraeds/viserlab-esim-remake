@php
    $content = getContent('contact_us.content', true);
@endphp

@extends($activeTemplate . 'layouts.frontend')
@section('content')

    <section class="contact-section my-120">
        <div class="container">
            <div class="row gy-4 mb-5 justify-content-center">
                <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-duration="1500" >
                    <div class="address-items">
                        <div class="address-items__shape">
                            <div class="box"></div>
                            <div class="image"></div>
                        </div>

                        <div class="address-items__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="38" viewBox="0 0 30 38" fill="none">
                                <path
                                    d="M2.88055 21.3326L2.88053 21.3326C1.65007 19.2795 1 16.9328 1 14.5469C1 7.10666 7.2521 1 15 1C22.7479 1 29 7.10666 29 14.5469C29 16.9328 28.35 19.2794 27.1195 21.3326C26.8323 21.8117 26.511 22.2791 26.1648 22.7213L26.1647 22.7213L26.16 22.7273L15.1598 37H14.8401L3.83997 22.7273L3.83999 22.7273L3.83514 22.7211C3.48889 22.279 3.16762 21.8117 2.88055 21.3326ZM9.02551 14.5469C9.02551 17.7879 11.7339 20.3711 15 20.3711C18.2661 20.3711 20.9745 17.7879 20.9745 14.5469C20.9745 11.3058 18.2661 8.72266 15 8.72266C11.7339 8.72266 9.02551 11.3058 9.02551 14.5469Z"
                                    stroke="hsl(var(--base))" stroke-width="2" />
                            </svg>
                        </div>

                        <div class="address-items__content">
                            <h5 class="address-items__title">{{ __($content->data_values?->address_title) }}</h5>
                            <p class="address-items__info">35 Firs Avenue, London, England, N11 3NE</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="300">
                    <div class="address-items">
                        <div class="address-items__shape">
                            <div class="box"></div>
                            <div class="image"></div>
                        </div>

                        <div class="address-items__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                                <g clip-path="url(#clip0_16518_1493)">
                                    <path
                                        d="M32.4584 31.6663C32.3286 31.6663 32.1987 31.6441 32.0752 31.603L0.804404 20.9155C0.354737 20.7603 0.0412375 20.355 0.00323747 19.8816C-0.0315959 19.4081 0.216987 18.9585 0.636571 18.7384L36.2616 0.134215C36.659 -0.070035 37.1387 -0.0383683 37.4997 0.219715C37.8639 0.479382 38.0523 0.919549 37.9874 1.36288L33.6332 30.6546C33.581 31.0029 33.3767 31.3101 33.0743 31.4937C32.8875 31.6077 32.6737 31.6663 32.4584 31.6663ZM4.18957 19.5617L31.4926 28.8939L35.2942 3.31988L4.18957 19.5617Z"
                                        fill="hsl(var(--base))" />
                                    <path
                                        d="M15.041 36.4167C14.9175 36.4167 14.7924 36.3977 14.6721 36.3581C14.1828 36.1966 13.8535 35.7422 13.8535 35.2292V24.5417C13.8535 24.2424 13.9675 23.9511 14.1718 23.731L35.9426 0.376831C36.3891 -0.104503 37.1443 -0.128253 37.6225 0.318247C38.1023 0.764747 38.1276 1.51683 37.6811 1.99816L16.2285 25.0088V31.6081L20.1916 26.2137C20.5795 25.688 21.3237 25.5708 21.8525 25.9603C22.3813 26.3483 22.4938 27.0924 22.1058 27.6213L15.9989 35.9338C15.7693 36.2425 15.4115 36.4167 15.041 36.4167Z"
                                        fill="#44B678" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_16518_1493">
                                        <rect width="38" height="38" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>

                        <div class="address-items__content">
                            <h5 class="address-items__title">{{ __($content->data_values?->email_title) }}</h5>
                            <a href="mailto:support@travelsim.live" class="address-items__info">support@travelsim.live</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4"  data-aos="fade-up" data-aos-duration="1500" data-aos-delay="400">
                    <div class="address-items">
                        <div class="address-items__shape">
                            <div class="box"></div>
                            <div class="image"></div>
                        </div>

                        <div class="address-items__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                                <g clip-path="url(#clip0_16518_1478)">
                                    <path
                                        d="M30.0317 23.5397C29.2537 22.7296 28.3154 22.2965 27.3209 22.2965C26.3344 22.2965 25.388 22.7216 24.578 23.5316L22.0436 26.058C21.8351 25.9457 21.6266 25.8414 21.4261 25.7372C21.1373 25.5928 20.8646 25.4565 20.6321 25.3121C18.2581 23.8043 16.1007 21.8394 14.0315 19.297C13.0289 18.0298 12.3553 16.9631 11.866 15.8804C12.5237 15.2789 13.1332 14.6533 13.7267 14.0518C13.9513 13.8272 14.1758 13.5947 14.4004 13.3701C16.0846 11.6859 16.0846 9.50437 14.4004 7.82014L12.2109 5.63063C11.9623 5.38201 11.7056 5.12536 11.465 4.86872C10.9838 4.37147 10.4785 3.85818 9.95722 3.37697C9.17927 2.60703 8.24893 2.19801 7.27047 2.19801C6.29201 2.19801 5.34563 2.60703 4.54362 3.37697C4.5356 3.38499 4.5356 3.38499 4.52758 3.39301L1.80072 6.14392C0.77414 7.17051 0.188668 8.42165 0.0603455 9.8733C-0.132138 12.2152 0.557595 14.3967 1.08693 15.8243C2.38619 19.3291 4.32707 22.5772 7.22235 26.058C10.7352 30.2525 14.9618 33.5649 19.7899 35.8987C21.6346 36.7729 24.0968 37.8075 26.8477 37.984C27.0161 37.992 27.1926 38 27.353 38C29.2056 38 30.7615 37.3343 31.9806 36.011C31.9886 35.995 32.0046 35.9869 32.0127 35.9709C32.4297 35.4656 32.9109 35.0085 33.4162 34.5193C33.7611 34.1904 34.1139 33.8456 34.4588 33.4847C35.2528 32.6586 35.6699 31.6962 35.6699 30.7097C35.6699 29.7152 35.2448 28.7608 34.4348 27.9588L30.0317 23.5397ZM32.9029 31.9849C32.8949 31.9849 32.8949 31.9929 32.9029 31.9849C32.5901 32.3217 32.2693 32.6265 31.9244 32.9633C31.4031 33.4606 30.8738 33.9819 30.3766 34.5674C29.5665 35.4336 28.6121 35.8426 27.361 35.8426C27.2407 35.8426 27.1123 35.8426 26.992 35.8346C24.6101 35.6822 22.3965 34.7518 20.7363 33.9578C16.1969 31.7603 12.2109 28.6405 8.89856 24.6865C6.16369 21.3902 4.33509 18.3426 3.12405 15.0704C2.37817 13.0733 2.10549 11.5174 2.22579 10.0497C2.30599 9.11138 2.6669 8.33343 3.33257 7.66776L6.06745 4.93288C6.46043 4.56395 6.87748 4.36345 7.28651 4.36345C7.79178 4.36345 8.20081 4.66822 8.45745 4.92486C8.46547 4.93288 8.47349 4.9409 8.48151 4.94892C8.97074 5.40607 9.43591 5.87926 9.92514 6.38453C10.1738 6.64117 10.4304 6.89782 10.6871 7.16249L12.8766 9.35199C13.7267 10.2021 13.7267 10.9881 12.8766 11.8382C12.644 12.0708 12.4194 12.3034 12.1868 12.528C11.5131 13.2177 10.8715 13.8593 10.1738 14.4849C10.1577 14.5009 10.1417 14.509 10.1337 14.525C9.44393 15.2147 9.57226 15.8884 9.71662 16.3456C9.72464 16.3696 9.73266 16.3937 9.74068 16.4177C10.3101 17.7972 11.1121 19.0965 12.3312 20.6444L12.3392 20.6524C14.5528 23.3792 16.8866 25.5046 19.4611 27.1327C19.7899 27.3412 20.1268 27.5096 20.4476 27.67C20.7363 27.8144 21.009 27.9507 21.2416 28.0951C21.2737 28.1111 21.3058 28.1352 21.3378 28.1512C21.6105 28.2876 21.8672 28.3517 22.1318 28.3517C22.7975 28.3517 23.2146 27.9347 23.3509 27.7984L26.0938 25.0555C26.3665 24.7828 26.7996 24.454 27.3048 24.454C27.8021 24.454 28.2111 24.7667 28.4597 25.0394C28.4678 25.0474 28.4678 25.0474 28.4758 25.0555L32.8949 29.4746C33.721 30.2926 33.721 31.1347 32.9029 31.9849Z"
                                        fill="hsl(var(--base))" />
                                    <path
                                        d="M20.5357 9.0392C22.637 9.39209 24.5458 10.3866 26.0697 11.9104C27.5935 13.4342 28.58 15.343 28.9409 17.4443C29.0291 17.9737 29.4862 18.3426 30.0076 18.3426C30.0717 18.3426 30.1279 18.3346 30.192 18.3265C30.7855 18.2303 31.1785 17.6689 31.0823 17.0754C30.6492 14.533 29.4461 12.2152 27.6095 10.3786C25.7729 8.54195 23.4551 7.33893 20.9127 6.90584C20.3192 6.8096 19.7658 7.20259 19.6616 7.78806C19.5573 8.37353 19.9423 8.94296 20.5357 9.0392Z"
                                        fill="hsl(var(--base))" />
                                    <path
                                        d="M37.9549 16.7626C37.2411 12.5761 35.2681 8.76652 32.2365 5.7349C29.2049 2.70328 25.3953 0.730316 21.2088 0.0165219C20.6233 -0.0877402 20.0699 0.313268 19.9657 0.89874C19.8694 1.49223 20.2624 2.04562 20.8559 2.14988C24.5933 2.78348 28.0019 4.55593 30.7127 7.25873C33.4235 9.96954 35.1879 13.3781 35.8215 17.1155C35.9097 17.6448 36.3669 18.0138 36.8882 18.0138C36.9524 18.0138 37.0085 18.0057 37.0727 17.9977C37.6581 17.9095 38.0591 17.3481 37.9549 16.7626Z"
                                        fill="hsl(var(--base))" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_16518_1478">
                                        <rect width="38" height="38" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>

                        <div class="address-items__content">
                            <h5 class="address-items__title">{{ __($content->data_values?->phone_title) }}</h5>
                            <span class="address-items__info">+44 7413 018997</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="500">
                    <div class="address-items">
                        <div class="address-items__shape">
                            <div class="box"></div>
                            <div class="image"></div>
                        </div>
                        <div class="address-items__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="hsl(var(--base))" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div class="address-items__content">
                            <h5 class="address-items__title">@lang('Working Hours')</h5>
                            <p class="address-items__info">@lang('Mon–Fri 9:00–18:00 GMT')<br>@lang('Sat 10:00–16:00 GMT')</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row flex-wrap-reverse gy-4">
                <div class="col-lg-6">
                    <iframe class="maps-iframe" src="https://www.google.com/maps?q={{ $content->data_values?->latitude }},{{ $content->data_values?->longitude }}&hl=es&z=14&output=embed" style="border:0;"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <div class="col-lg-6">
                    <form method="post" class="verify-gcaptcha">
                        @csrf
                        <div class="section-heading style-left">
                            <h2 class="section-heading__title" data-underline-start="1" data-underline-word="2">
                                {{ __($content->data_values?->form_title) }}</h2>
                            <p class="section-heading__desc">{{ __($content->data_values?->form_subtitle) }}</p>
                        </div>

                        <div class="row gy-3">
                            <div class="col-sm-6">
                                <label for="first-name" class="form--label required">@lang('Name')</label>
                                <div class="form--control-wrapper">
                                    <input name="name" type="text" id="first-name" placeholder="Martin" class="form-control form--control"
                                        value="{{ old('name', $user->fullname ?? '') }}"@if ($user && $user->profile_complete) readonly @endif required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="email" class="form--label required">@lang('Email')</label>
                                <div class="form--control-wrapper">
                                    <input name="email" id="email" type="email" class="form-control form--control" placeholder="johndoe@example.com" value="{{ old('email', $user->email ?? '') }}"
                                        @if ($user) readonly @endif required>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <label class="form--label required">@lang('Subject')</label>
                                <div class="form--control-wrapper">
                                    <input name="subject" type="text" class="form-control form--control w-100" placeholder="Your subject" value="{{ old('subject') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <label for="message" class="form--label required">@lang('Write Messages')</label>
                                <div class="form--control-wrapper form--control-wrapper-textarea">
                                    <textarea name="message" id="message" placeholder="@lang('Write your messages.....')" class="form-control form--control w-100" required>{{ old('message') }}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <x-captcha />
                                <button type="submit" class="w-100 btn btn--base">
                                    @lang('Send Message')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @if (isset($sections->secs) && $sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
