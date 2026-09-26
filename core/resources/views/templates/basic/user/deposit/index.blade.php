@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card custom--card">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <h6 class="mb-0">@lang('Deposit History')</h6>
                <div class="d-flex align-items-center gap-2">
                    <form action="{{ url('webhooks/alppay') }}" method="GET" class="me-2">
                        <button type="submit" class="btn btn-outline--base btn-xs">@lang('Refresh Deposit Status')</button>
                    </form>
                    <a href="{{ route('user.deposit.index') }}" class="btn btn--base btn-xs">@lang('Add Balance')</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table--light style--two mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="small">@lang('Date')</th>
                                <th class="small">@lang('TRX')</th>
                                <th class="small">@lang('Amount')</th>
                                <th class="small">@lang('Credits Added')</th>
                                <th class="small text-end">@lang('Status')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($deposits as $deposit)
                            @php
                                // Определяем символ валюты
                                $currencySymbols = ['EUR' => '€', 'GBP' => '£', 'USD' => '$'];
                                $paymentCurrency = $deposit->method_currency ?? 'EUR';
                                $currencySymbol = $currencySymbols[$paymentCurrency] ?? '€';
                                
                                // Вычисляем сколько кредитов (EUR) было добавлено
                                // Используем ту же логику, что и в PaymentController::userDataUpdate
                                $creditsAdded = $deposit->amount;
                                if ($deposit->status == 1 && $deposit->order_id == 0) { // PAYMENT_SUCCESS = 1
                                    if ($paymentCurrency !== 'EUR') {
                                        $exchangeRates = [
                                            'GBP' => 0.87,  // 1 EUR = 0.87 GBP => 1 GBP = 1/0.87 EUR
                                            'USD' => 1.18,  // 1 EUR = 1.18 USD => 1 USD = 1/1.18 EUR
                                        ];
                                        if (isset($exchangeRates[$paymentCurrency])) {
                                            $creditsAdded = $deposit->amount / $exchangeRates[$paymentCurrency];
                                        }
                                    }
                                }
                                
                                // Показываем оригинальную сумму депозита
                                $originalAmount = number_format($deposit->amount, 2);
                            @endphp
                            <tr>
                                <td>{{ showDateTime($deposit->created_at) }}</td>
                                <td>{{ $deposit->trx }}</td>
                                <td>
                                    <div>
                                        {{ $currencySymbol }}{{ $originalAmount }} {{ $paymentCurrency }}
                                    </div>
                                </td>
                                <td>
                                    @if($deposit->status == 1) {{-- PAYMENT_SUCCESS --}}
                                        <div>
                                            {{ number_format($creditsAdded, 2) }} Credits
                                        </div>
                                    @else
                                        <div class="text-muted">-</div>
                                    @endif
                                </td>
                                <td class="text-end status-cell">
                                    <span class="status-badge">{!! $deposit->statusBadge !!}</span>
                                    <form action="{{ url('webhooks/alppay') }}" method="GET">
                                        <button type="submit" class="btn btn-outline--base btn-xs">@lang('Check')</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">@lang('No deposits found')</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($deposits, 'links'))
                <div class="card-footer py-2">{{ $deposits->links() }}</div>
            @endif
        </div>
    </div>
</div>
<style>
    .btn.btn-xs{padding:4px 8px;font-size:12px;line-height:1.2;border-radius:4px}
    .table td,.table th{padding:.45rem .5rem}
    .badge{transform:scale(.9);transform-origin:right}
    .card.custom--card{box-shadow:none;border:1px solid #eef0f3}
    .table-responsive{border-top:1px solid #f1f2f6}
    .status-cell{white-space:nowrap}
    .status-cell form{display:inline-block;margin-left:6px}
    .status-badge{display:inline-block;vertical-align:middle}
    /* Shrink text in first 5 columns (Date, TRX, Amount, Charge, Total) */
    .table thead th:nth-child(-n+5){font-size:12px}
    .table tbody td:nth-child(-n+5){font-size:12px}
</style>
@endsection