@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center gy-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex gap-2 justify-content-between align-items-center flex-wrap">
                    <div>
                        <small class="text-muted">@lang('Plan Title')</small>
                        <h4>
                            {{ __($plan->name) }}
                        </h4>
                    </div>

                    <span class="bg--dark text-center p-2 rounded-3">{{ showPlanCapacity($plan) }}, {{ $plan->period }} @lang('days')</span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-header">
                    <h5 class="card-title">@lang('Details')</h5>
                </div>
                <div class="card-body px-0 pt-0">
                    <ul class="list-group plan-details-list list-group-flush">

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Period')</span>
                            <span class="fw-bold">
                                {{ $plan->period }} @lang('days')
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Retail Price')</span>
                            <span class="fw-bold">{{ showAmount($plan->retail_price, currencyFormat: false) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Currency')</span>
                            <span class="fw-bold">{{ __($plan->price_currency) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Prepaid Credit')</span>
                            <span class="fw-bold">{{ showAmount($plan->prepaid_credit, currencyFormat: false) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Reloadable')</span>
                            @if ($plan->reloadable == Status::ENABLE)
                                <span class="badge badge--success">@lang('Yes')</span>
                            @else
                                <span class="badge badge--danger">@lang('No')</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Phone Number')</span>
                            @if ($plan->phone_number == Status::ENABLE)
                                <span class="badge badge--success">@lang('Yes')</span>
                            @else
                                <span class="badge badge--danger">@lang('No')</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Operator Name')</span>
                            <span class="fw-bold">{{ __($plan->operator_name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Region')</span>
                            <span class="fw-bold">{{ __($plan->region->name) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Status')</span>
                            @php echo $plan->statusBadge @endphp
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Added At')</span>
                            <span class="fw-bold">{{ showDateTime($plan->created_at, 'd M, Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-header">
                    <h5 class="card-title">@lang('Available in Countries')</h5>
                </div>
                <div class="card-body">
                    @if ($plan->countries->count())
                        @php
                            $countries = $plan->countries;
                            $columns = 3;
                            $total = $countries->count();
                            $rows = ceil($total / $columns);
                        @endphp
                        <div class="d-flex justify-content-between flex-wrap country-list">
                            @for ($col = 0; $col < $columns; $col++)
                                <ul class="d-flex flex-column gap-2">
                                    @for ($row = 0; $row < $rows; $row++)
                                        @php
                                            $index = $col * $rows + $row;
                                        @endphp
                                        <li>
                                            @if ($index < $total)
                                                @php $country = $countries[$index]; @endphp
                                                <span class="serial-number">{{ $index + 1 }}.</span> <span class="country-name">{{ $country->name }} - {{ $country->code }}</span>
                                            @endif
                                        </li>
                                    @endfor
                                </ul>
                            @endfor
                        </div>
                    @else
                        <div class="d-flex flex-column align-items-center justiy-content-center py-4">
                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 0C10.7656 0 0 10.7656 0 24C0 37.2344 10.7656 48 24 48C37.2344 48 48 37.2344 48 24C48 10.7656 37.2344 0 24 0ZM24 4C35.0703 4 44 12.9297 44 24C44 35.0703 35.0703 44 24 44C12.9297 44 4 35.0703 4 24C4 12.9297 12.9297 4 24 4ZM15 16C13.3438 16 12 17.3438 12 19C12 20.6562 13.3438 22 15 22C16.6562 22 18 20.6562 18 19C18 17.3438 16.6562 16 15 16ZM33 16C31.3438 16 30 17.3438 30 19C30 20.6562 31.3438 22 33 22C34.6562 22 36 20.6562 36 19C36 17.3438 34.6562 16 33 16ZM14 32V36H34V32H14Z" fill="#6c757d" />
                            </svg>
                            <h5 class="text-muted mb-0 mt-2">@lang('No countries assigned')</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .plan-details-list .list-group-item {
            border-color: #f1f1f1 !important;
        }

        .country-list ul {
            min-width: 200px;
            list-style: none;
            padding-left: 0;
        }

        .country-list ul li {
            font-size: 16px;
        }

        .country-list li span.serial-number:first-child {
            margin-right: 4px;
        }

        .search-input {
            padding: 0px 7px !important;
            height: 30px !important;
        }

        .search-btn {
            cursor: pointer;
        }
    </style>
@endpush
