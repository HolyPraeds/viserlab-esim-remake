@if (!request()->routeIs('user.deposit*') || Route::is('user.deposit.history'))
    <aside id="dashboard-offcanvas-sidebar" class="offcanvas-sidebar offcanvas-sidebar--dashboard">
        <button class="btn-close btn--close btn--xsm d-lg-none" type="button"></button>
        <div class="offcanvas-sidebar__header">
            <div class="user-profile">
                <div class="user-profile__avatar">{{ auth()->user()->fullname[0] }}</div>
                <div class="user-profile__content">
                    <span class="user-profile__name">{{ auth()->user()->fullname }}</span>
                    <span class="user-profile__username">{{ auth()->user()->username }}</span>
                    <div class="user-profile__balance" style="margin-top: 5px; padding: 5px 10px; background: rgba(255,255,255,0.1); border-radius: 15px; font-size: 12px; font-weight: 600;">
                        <span style="color: #F9C80E;">💰 {{ showAmount(auth()->user()->balance) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas-sidebar__body">
            <ul class="offcanvas-sidebar-menu">
                <li class="offcanvas-sidebar-menu__item {{ menuActive('user.home') }}">
                    <a class="offcanvas-sidebar-menu__link" href="{{ route('user.home') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-d\ashboard">
                            <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                            <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                            <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                            <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                        </svg>
                        <span>@lang('Dashboard')</span>
                    </a>
                </li>
                <li class="offcanvas-sidebar-menu__item {{ menuActive(['user.esim.*']) }}">
                    <a class="offcanvas-sidebar-menu__link" href="{{ route('user.esim.active') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-card-sim-icon lucide-card-sim">
                            <path d="M12 14v4" />
                            <path d="M14.172 2a2 2 0 0 1 1.414.586l3.828 3.828A2 2 0 0 1 20 7.828V20a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" />
                            <path d="M8 14h8" />
                            <rect x="8" y="10" width="8" height="8" rx="1" />
                        </svg>
                        <span>@lang('My eSIMs')</span>
                    </a>
                </li>
                <li class="offcanvas-sidebar-menu__item">
                    <span class="offcanvas-sidebar-menu__label">
                        @lang('Manage Orders')
                    </span>
                </li>

                @php
                    $pendingOrderCount = auth()->user()->orders()->pending()->count();
                    $pendingDepositCount = auth()->user()->deposits()->pending()->count();
                @endphp

                <li class="offcanvas-sidebar-menu__item {{ menuActive('user.order.pending') }}">
                    <a class="offcanvas-sidebar-menu__link" href="{{ route('user.order.pending') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-dashed-icon lucide-circle-dashed">
                            <path d="M10.1 2.182a10 10 0 0 1 3.8 0" />
                            <path d="M13.9 21.818a10 10 0 0 1-3.8 0" />
                            <path d="M17.609 3.721a10 10 0 0 1 2.69 2.7" />
                            <path d="M2.182 13.9a10 10 0 0 1 0-3.8" />
                            <path d="M20.279 17.609a10 10 0 0 1-2.7 2.69" />
                            <path d="M21.818 10.1a10 10 0 0 1 0 3.8" />
                            <path d="M3.721 6.391a10 10 0 0 1 2.7-2.69" />
                            <path d="M6.391 20.279a10 10 0 0 1-2.69-2.7" />
                        </svg>
                        @lang('Pending Orders')
                        @if ($pendingOrderCount > 0)
                            <span class="badge badge--xs badge--warning">{{ $pendingOrderCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="offcanvas-sidebar-menu__item {{ menuActive('user.order.completed') }}">
                    <a class="offcanvas-sidebar-menu__link" href="{{ route('user.order.completed') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-ordered-icon lucide-list-ordered">
                            <path d="M10 12h11" />
                            <path d="M10 18h11" />
                            <path d="M10 6h11" />
                            <path d="M4 10h2" />
                            <path d="M4 6h1v4" />
                            <path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1" />
                        </svg>
                        @lang('Completed Orders')
                    </a>
                </li>
                <li class="offcanvas-sidebar-menu__item">
                    <span class="offcanvas-sidebar-menu__label">
                        @lang('Finance & Reports')
                    </span>
                </li>
                <li class="offcanvas-sidebar-menu__item {{ menuActive(['user.deposit.index','user.deposit.history']) }}">
                    <a class="offcanvas-sidebar-menu__link" href="{{ route('user.deposit.history') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-stack-icon lucide-file-stack">
                            <path d="M21 7h-3a2 2 0 0 1-2-2V2" />
                            <path d="M21 6v6.5c0 .8-.7 1.5-1.5 1.5h-7c-.8 0-1.5-.7-1.5-1.5v-9c0-.8.7-1.5 1.5-1.5H17Z" />
                            <path d="M7 8v8.8c0 .3.2.6.4.8.2.2.5.4.8.4H15" />
                            <path d="M3 12v8.8c0 .3.2.6.4.8.2.2.5.4.8.4H11" />
                        </svg>
                        @lang('Deposit History')
                        @if ($pendingDepositCount > 0)
                            <span class="badge badge--xs badge--warning">{{ $pendingDepositCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="offcanvas-sidebar-menu__item {{ menuActive('user.deposit.index') }}">
                    <a class="offcanvas-sidebar-menu__link" href="{{ route('user.deposit.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wallet"><path d="M20 7H7a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/><path d="M7 7V5a2 2 0 0 1 2-2h11"/></svg>
                        <span>@lang('Deposit Funds')</span>
                    </a>
                </li>
                <li class="offcanvas-sidebar-menu__item {{ menuActive('user.transactions') }}">
                    <a class="offcanvas-sidebar-menu__link" href="{{ route('user.transactions') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-dollar-sign-icon lucide-badge-dollar-sign">
                            <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" />
                            <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                            <path d="M12 18V6" />
                        </svg>
                        @lang('Transactions')
                    </a>
                </li>
                <li class="offcanvas-sidebar-menu__item">
                    <span class="offcanvas-sidebar-menu__label">
                        @lang('Support')
                    </span>
                </li>
                <li class="offcanvas-sidebar-menu__item {{ menuActive('ticket.open') }}">
                    <a class="offcanvas-sidebar-menu__link" href="{{ route('ticket.open') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-plus-icon lucide-circle-plus">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M8 12h8" />
                            <path d="M12 8v8" />
                        </svg>
                        @lang('Open Ticket')
                    </a>
                </li>
                <li class="offcanvas-sidebar-menu__item {{ menuActive('ticket.index') }}">
                    <a class="offcanvas-sidebar-menu__link" href="{{ route('ticket.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ticket-icon lucide-ticket">
                            <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                            <path d="M13 5v2" />
                            <path d="M13 17v2" />
                            <path d="M13 11v2" />
                        </svg>
                        @lang('All Tickets')
                    </a>
                </li>
                <li class="offcanvas-sidebar-menu__item">
                    <span class="offcanvas-sidebar-menu__label">
                        @lang('Others')
                    </span>
                </li>
                <li class="offcanvas-sidebar-menu__item {{ menuActive('user.referral.index') }}">
                    <a class="offcanvas-sidebar-menu__link" href="{{ route('user.referral.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-icon lucide-users">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <path d="M16 3.128a4 4 0 0 1 0 7.744" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                        @lang('Referrals')
                    </a>
                </li>
                <li class="offcanvas-sidebar-menu__item">
                    <a class="offcanvas-sidebar-menu__link text--danger" href="{{ route('user.logout') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out-icon lucide-log-out"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
                        @lang('Logout')
                    </a>
                </li>
            </ul>
        </div>
    </aside>
@endif
