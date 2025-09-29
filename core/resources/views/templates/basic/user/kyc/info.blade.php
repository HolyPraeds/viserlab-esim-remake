@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-start">
            <div class="col-lg-8">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('KYC Documents')</h5>
                    </div>
                    <div class="card-body">
                        @if ($user->kyc_data)
                            <ul class="list-group">
                                @foreach ($user->kyc_data as $val)
                                    @continue(!$val->value)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ __($val->name) }}
                                        <span>
                                            @if ($val->type == 'checkbox')
                                                {{ implode(',', $val->value) }}
                                            @elseif($val->type == 'file')
                                                <a href="{{ route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $val->value)) }}"><i class="fa-regular fa-file"></i> @lang('Attachment') </a>
                                            @else
                                                <p>{{ __($val->value) }}</p>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                        <div class="text-center mt-3">
                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 0C10.7656 0 0 10.7656 0 24C0 37.2344 10.7656 48 24 48C37.2344 48 48 37.2344 48 24C48 10.7656 37.2344 0 24 0ZM24 4C35.0703 4 44 12.9297 44 24C44 35.0703 35.0703 44 24 44C12.9297 44 4 35.0703 4 24C4 12.9297 12.9297 4 24 4ZM15 16C13.3438 16 12 17.3438 12 19C12 20.6562 13.3438 22 15 22C16.6562 22 18 20.6562 18 19C18 17.3438 16.6562 16 15 16ZM33 16C31.3438 16 30 17.3438 30 19C30 20.6562 31.3438 22 33 22C34.6562 22 36 20.6562 36 19C36 17.3438 34.6562 16 33 16ZM14 32V36H34V32H14Z" fill="#1A1A1A" />
                            </svg>
                            <h5 class="text-center mt-1">@lang('KYC data not found')</h5>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
