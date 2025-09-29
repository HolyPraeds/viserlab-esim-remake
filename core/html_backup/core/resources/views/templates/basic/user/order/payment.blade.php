@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="my-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="payment-system-list p-3 border rounded">
                        <h4 class="mb-3">@lang('Card')</h4>
                        <p class="text-muted mb-4">@lang('Pay securely by card')</p>

                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <div class="deposit-info">
                                    <div class="deposit-info__title"><p class="text mb-0">@lang('Amount')</p></div>
                                    <div class="deposit-info__input">{{ showAmount($order->total_amount) }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="deposit-info">
                                    <div class="deposit-info__title"><p class="text mb-0">@lang('Processing Charge')</p></div>
                                    <div class="deposit-info__input">@lang('0.00') {{ __(gs('cur_text')) }}</div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('user.order.payment.taurixy.direct') }}" method="POST" class="checkout-form">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="payment_method" value="BASIC_CARD">

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('First name')</label>
                                    <input type="text" name="first_name" class="form-control form--control" value="{{ auth()->user()?->firstname ?? 'Guest' }}" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Last name')</label>
                                    <input type="text" name="last_name" class="form-control form--control" value="{{ auth()->user()?->lastname ?? 'User' }}" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Country / Region')</label>
                                    <input type="text" name="country" class="form-control form--control" value="{{ auth()->user()?->country_name ?? 'Latvia' }}" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Town / City')</label>
                                    <input type="text" name="city" class="form-control form--control" value="{{ auth()->user()?->city ?? '' }}" required>
                                </div>
                                <div class="col-sm-8">
                                    <label class="form--label required">@lang('Street address')</label>
                                    <input type="text" name="address" class="form-control form--control" value="{{ auth()->user()?->address ?? '' }}" required>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form--label required">@lang('Postcode / ZIP')</label>
                                    <input type="text" name="zip" class="form-control form--control" value="{{ auth()->user()?->zip ?? '' }}" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Email address')</label>
                                    <input type="email" name="email" class="form-control form--control" value="{{ auth()->user()?->email ?? 'guest@example.com' }}" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Phone')</label>
                                    <input type="text" name="phone" class="form-control form--control" value="{{ auth()->user()?->mobile ?? '1234567890' }}" required>
                                </div>
                                <div class="col-12 pt-2">
                                    <button type="submit" class="btn btn--base w-100">@lang('Pay')</button>
                                    <p class="mt-2 text-muted small"><input type="checkbox">@lang(' Your personal data will be used to process your order and for other purposes described in the privacy policy. Buy proceeding with the purchase I accept the Terms and Conditions and Privacy Policies of ')<a href="https://travelsim.live/">travelsim.live</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
@endpush
