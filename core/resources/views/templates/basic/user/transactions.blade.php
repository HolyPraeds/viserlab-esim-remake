@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card custom--card  mb-4">
                    <div class="card-body">
                        <form class="filter-form" >
                            <div class="d-flex flex-wrap gap-md-4 gap-3">
                                <div class="flex-grow-1">
                                    <label class="form-label">@lang('Transaction Number')</label>
                                    <input type="search" name="search" value="{{ request()->search }}" class="form-control form--control">
                                </div>
                                <div class="flex-grow-1 flex-shrink-0 select2-parent">
                                    <label class="form-label d-block">@lang('Type')</label>
                                    <select name="trx_type" class="form-select form--select select2" data-minimum-results-for-search="-1">
                                        <option value="">@lang('All')</option>
                                        <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                                        <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')</option>
                                    </select>
                                </div>
                                <div class="flex-grow-1 flex-shrink-0 select2-parent">
                                    <label class="form-label d-block">@lang('Remark')</label>
                                    <select class="form-select form--select select2" data-minimum-results-for-search="-1" name="remark">
                                        <option value="">@lang('All')</option>
                                        @foreach ($remarks as $remark)
                                            <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>{{ __(keyToTitle($remark->remark)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-grow-1 flex-shrink-0 align-self-end">
                                    <button class="btn btn--base w-100"><i class="las la-filter"></i> @lang('Filter')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="my-3">
                    <h5 class="mb-0">{{ __($pageTitle) }}</h5>
                </div>

                <div class="table--responsive mb-3">
                    <table class="table table--custom table--responsive-sm">
                        <thead>
                            <tr>
                                <th>@lang('Trx')</th>
                                <th>@lang('Transacted')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Post Balance')</th>
                                <th>@lang('Details')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>
                                        <strong>{{ $trx->trx }}</strong>
                                    </td>

                                    <td>
                                        {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                    </td>

                                    <td>
                                        <span class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                            {{ $trx->trx_type }} {{ showAmount($trx->amount, 2, true, false, true, true) }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ showAmount($trx->post_balance, 2, true, false, true, true) }}
                                    </td>


                                    <td>{{ __($trx->details) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                @if ($transactions->hasPages())
                    <div class="pagination-wrapper">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .select2-container {
            width: 100% !important;
        }
        .filter-form  .select2-container {
            min-width: 160px;
        }
    </style>
@endpush


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush
