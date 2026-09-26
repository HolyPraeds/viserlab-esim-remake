@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="deposit-card">
            <div class="deposit-header">
                <h4>@lang('Payment Preview')</h4>
                <p class="text-muted">@lang('Confirm your deposit details and proceed to payment')</p>
            </div>

            <div class="deposit-body">
                <div class="deposit-summary">
                    <div class="summary-line">
                        <span>@lang('Amount')</span>
                        <span>{{ showAmount($deposit->amount) }} {{ __(gs('cur_text')) }}</span>
                    </div>
                    <div class="summary-line">
                        <span>@lang('Processing Fee')</span>
                        <span>{{ showAmount($deposit->charge) }} {{ __(gs('cur_text')) }}</span>
                    </div>
                    <div class="summary-line total">
                        <strong>@lang('Total to Pay')</strong>
                        <strong>{{ showAmount($deposit->final_amount) }} {{ __(gs('cur_text')) }}</strong>
                    </div>
                    <div class="summary-line">
                        <span>@lang('Transaction ID')</span>
                        <span>{{ $deposit->trx }}</span>
                    </div>
                </div>

                @if(!empty($alppayEnabled))
                <form id="alppayRedirectForm" action="{{ route('user.deposit.alppay.create') }}" method="POST" class="mb-2">
                    @csrf
                    <input type="hidden" name="deposit_trx" value="{{ $deposit->trx }}">
                    <button type="submit" class="btn btn--primary btn-lg w-100">
                        @lang('Pay by Card')
                    </button>
                </form>
                <p class="text-center text-muted small my-2">— @lang('or') —</p>
                <a href="{{ route('user.deposit.alppay.h2h.create', ['deposit_trx' => $deposit->trx]) }}" class="btn btn-outline--primary btn-lg w-100 mb-3">
                    @lang('Pay by Card (H2H)')
                </a>
                @endif

                <a href="{{ route('user.deposit.history') }}" class="btn btn-outline--base w-100">@lang('Go to Deposit History')</a>
            </div>
        </div>
    </div>
</div>

<style>
.deposit-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
.deposit-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
.deposit-body { padding: 30px; }
.deposit-summary { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 0 0 20px 0; }
.summary-line { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.summary-line.total { border-top: 2px solid #667eea; padding-top: 15px; margin-top: 15px; }
</style>
@endsection