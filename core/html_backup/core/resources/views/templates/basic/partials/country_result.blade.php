@if (empty($countries) || (is_countable($countries) && count($countries) === 0))
    <h6 class="title mb-0">@lang('No countries found')</h6>
@else
    @foreach (collect($countries)->unique('id') as $country)
        <a class="search-box-list__item" href="{{ route('country.plans', $country->slug) }}">
            <span class="flag-wrapper" style="width:28px;height:28px;border-radius:6px;">
                <img class="flag-img"
                     src="{{ asset('assets/images/flags/' . strtoupper($country->code) . '.svg') }}"
                     alt="{{ $country->name }} flag"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                <span class="flag-fallback">{{ strtoupper($country->code) }}</span>
            </span>
            <h6 class="title">{{ __($country->name) }}</h6>
        </a>
    @endforeach
@endif
