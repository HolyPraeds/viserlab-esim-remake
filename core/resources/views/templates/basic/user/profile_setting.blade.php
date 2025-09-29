@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-header">
            <h5 class="card-title">@lang('Profile')</h5>
        </div>
        <div class="card-body">
            <form class="register" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row gy-4">
                    <div class="col-sm-6">
                        <label class="form--label required">@lang('First Name')</label>
                        <input type="text" class="form-control form--control" name="firstname"
                            value="{{ $user->firstname }}" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form--label required">@lang('Last Name')</label>
                        <input type="text" class="form-control form--control" name="lastname"
                            value="{{ $user->lastname }}" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form--label">@lang('E-mail Address')</label>
                        <input class="form-control form--control" value="{{ $user->email }}" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label class="form--label">@lang('Mobile Number')</label>
                        <input class="form-control form--control" value="{{ $user->mobile }}" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label class="form--label">@lang('Address')</label>
                        <input type="text" class="form-control form--control" name="address"
                            value="{{ $user->address }}">
                    </div>
                    <div class="col-sm-6">
                        <label class="form--label">@lang('State')</label>
                        <input type="text" class="form-control form--control" name="state" value="{{ $user->state }}">
                    </div>
                    <div class="col-sm-4">
                        <label class="form--label">@lang('Zip Code')</label>
                        <input type="text" class="form-control form--control" name="zip" value="{{ $user->zip }}">
                    </div>

                    <div class="col-sm-4">
                        <label class="form--label">@lang('City')</label>
                        <input type="text" class="form-control form--control" name="city" value="{{ $user->city }}">
                    </div>

                    <div class="col-sm-4">
                        <label class="form--label">@lang('Country')</label>
                        <input class="form-control form--control" value="{{ $user->country_name }}" disabled>
                    </div>
                    <div class="col-sm-12">
                        <button type="submit" class="w-100 btn btn--base">
                            @lang('Submit')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
