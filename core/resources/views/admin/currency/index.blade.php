@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Currency')</th>
                                    <th>@lang('Conversion Rate')</th>
                                    <th>@lang('Created At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currencies->where('api_currency', '!=', gs('cur_text')) as $currency)
                                    <tr>
                                        <td>{{ $currency->api_currency }}</td>

                                        <td>1 {{ __(gs('cur_text')) }} = {{ getAmount($currency->conversion_rate) . ' ' . $currency->api_currency }}</td>
                                        <td>{{ showDateTime($currency->created_at, 'd M, Y') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary editBtn" data-currency="{{ json_encode($currency) }}"><i class="las la-pen"></i>@lang('Edit')</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($currencies->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($currencies) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="currencyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Edit Currency')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Currency')</label>
                            <input type="text" name="api_currency" class="form-control currencyName" disabled>
                        </div>
                        <div class="form-group">
                            <label>@lang('Conversion Rate')</label>
                            <div class="input-group">
                                <span class="input-group-text">1 {{ gs('cur_text') }} = </span>
                                <input type="number" step="any" name="conversion_rate" class="form-control currencyRate" required>
                                <span class="input-group-text code"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn h-45 btn--primary w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";

        $('.editBtn').on('click', function() {
            const currency = $(this).data('currency');
            let modal = $('#currencyModal');
            let url = `{{ route('admin.currency.update', ':id') }}`.replace(':id', currency.id);

            modal.find('form').attr('action', url);
            modal.find('.currency-id').val(currency.id);
            modal.find('.currencyName').val(currency.api_currency);
            modal.find('.code').text(currency.api_currency);
            modal.find('.currencyRate').val(currency.conversion_rate);

            modal.modal('show');
        });
    </script>
@endpush
