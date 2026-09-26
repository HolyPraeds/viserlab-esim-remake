@extends($activeTemplate . 'layouts.master')
@section('content')
    <h5 class="mb-3">{{ __($pageTitle) }}</h5>
    
    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('user.order.track') }}" method="GET" class="row g-3">
                <div class="col-md-8">
                    <label class="form--label">@lang('Order Number')</label>
                    <input type="text" name="order_number" class="form-control form--control" 
                           value="{{ request('order_number') }}" 
                           placeholder="@lang('Enter your order number')" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn--base w-100">
                        <i class="las la-search"></i> @lang('Track Order')
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Order Details -->
    @if(request('order_number'))
        @if($order)
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">@lang('Order Information')</h6>
                </div>
                <div class="card-body">
                    <div class="table--responsive">
                        <table class="table table--custom table--responsive-sm">
                            <tbody>
                                <tr>
                                    <td><strong>@lang('Order Number')</strong></td>
                                    <td>{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Status')</strong></td>
                                    <td>@php echo $order->statusBadge @endphp</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Plan')</strong></td>
                                    <td>
                                        @if($order->orderItem && $order->orderItem->plan)
                                            {{ __($order->orderItem->plan->name) }} 
                                            ({{ $order->orderItem->plan->capacity . $order->orderItem->plan->capacity_unit . ' - ' . $order->orderItem->plan->period . ' ' . __('days') }})
                                        @else
                                            <span class="text-muted">@lang('Plan information not available')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Amount')</strong></td>
                                    <td>{{ showAmount($order->total_amount) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Order Date')</strong></td>
                                    <td>{{ showDateTime($order->created_at, 'd M Y, h:i A') }}</td>
                                </tr>
                                @if($order->updated_at != $order->created_at)
                                <tr>
                                    <td><strong>@lang('Last Updated')</strong></td>
                                    <td>{{ showDateTime($order->updated_at, 'd M Y, h:i A') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 d-flex gap-2 flex-wrap">
                        @if($order->status == \App\Constants\Status::ORDER_PENDING)
                            <a href="{{ route('user.order.payment', $order->order_number) }}" class="btn btn--primary">
                                <i class="las la-credit-card"></i> @lang('Pay Now')
                            </a>
                        @endif
                        
                        @if($order->status == \App\Constants\Status::ORDER_COMPLETED)
                            @if(auth()->check())
                                <a href="{{ route('user.esim.active') }}" class="btn btn--success">
                                    <i class="las la-sim-card"></i> @lang('View My eSIMs')
                                </a>
                            @endif
                        @endif
                        
                        @if(auth()->check() && $order->user_id == auth()->id())
                            <a href="{{ route('user.order.completed') }}" class="btn btn--base">
                                <i class="las la-list"></i> @lang('All Orders')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning" role="alert">
                <i class="las la-exclamation-triangle"></i> 
                @lang('Order not found. Please check your order number and try again.')
            </div>
        @endif
    @else
        <div class="alert alert-info" role="alert">
            <i class="las la-info-circle"></i> 
            @lang('Enter your order number above to track your order status.')
        </div>
    @endif
@endsection
