@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'sections.banner')

    @if (isset($sections->secs) && $sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @if (in_array($sec, (array) currentBrand('hide_home_sections'), true))
                @continue
            @endif
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
