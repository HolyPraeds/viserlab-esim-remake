 <header class="header" id="header">
     <div class="container">
         <nav class="navbar navbar-expand-lg">
             <a class="navbar-brand order-1" href="{{ route('home') }}">
                 <img src="{{ siteLogo('dark') }}" alt="logo">
             </a>

             <button class="navbar-toggler order-3 order-lg-2" type="button" data-bs-toggle="collapse" data-bs-target="#header-collapse" aria-controls="header-collapse" aria-expanded="false"
                 aria-label="Toggle navigation">
                 <i class="las la-bars"></i>
             </button>

             <div class="collapse navbar-collapse order-4 order-lg-3" id="header-collapse">
                 <ul class="navbar-nav nav-menu ms-auto align-items-xl-center">
                     <li class="nav-item {{ menuActive('home') }}">
                         <a class="nav-link" aria-current="page" href="{{ route('home') }}">
                             @lang('Home')
                         </a>
                     </li>
                     @foreach ($pages as $k => $data)
                         <li class="nav-item {{ menuActive('pages', null, $data->slug) }}">
                             <a href="{{ route('pages', [$data->slug]) }}" class="nav-link">
                                 {{ __($data->name) }}
                             </a>
                         </li>
                     @endforeach
                     <li class="nav-item {{ menuActive('destination') }}">
                         <a class="nav-link" href="{{ route('destination') }}">
                             @lang('Destination')
                         </a>
                     </li>
                     <li class="nav-item {{ menuActive('blogs') }}">
                         <a class="nav-link" href="{{ route('blogs') }}">
                             @lang('Blog')
                         </a>
                     </li>
                     <li class="nav-item {{ menuActive('contact') }}">
                         <a class="nav-link" href="{{ route('contact') }}">
                             @lang('Contact')
                         </a>
                     </li>
                     {{-- Language switcher removed by request --}}
                 </ul>
             </div>

             <div class="navbar-auth-area order-2 order-lg-4 ms-auto">
                 @auth
                     <div class="dropdown dropdown--user">
                         <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                             <span class="dropdown-toggle__avatar">
                                 <i class="fas fa-user"></i>
                             </span>
                             <span class="dropdown-toggle__username">
                                 {{ auth()->user()->username }}
                             </span>
                         </button>

                         <div class="dropdown-menu">
                             <div class="dropdown-user">
                                 <div class="dropdown-user__avatar">{{ auth()->user()->fullname[0] }}</div>
                                 <div class="dropdown-user__content">
                                     <span class="dropdown-user__name">{{ auth()->user()->fullname }}</span>
                                     <span class="dropdown-user__username">{{ auth()->user()->username }}</span>
                                     <div class="dropdown-user__balance" style="margin-top: 5px; padding: 3px 8px; background: rgba(0,0,0,0.05); border-radius: 10px; font-size: 11px; font-weight: 600;">
                                         <span style="color: #28a745;">💰 {{ showAmount(auth()->user()->balance) }}</span>
                                     </div>
                                 </div>
                             </div>

                             <div class="dropdown-menu-wrapper">
                                 <a class="dropdown-item" href="{{ route('user.home') }}">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="lucide lucide-layout-dashboard">
                                         <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                                         <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                                         <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                                         <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                                     </svg>
                                     @lang('Dashboard')
                                 </a>
                                 <a class="dropdown-item" href="{{ route('user.esim.active') }}">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="lucide lucide-card-sim-icon lucide-card-sim">
                                         <path d="M12 14v4" />
                                         <path d="M14.172 2a2 2 0 0 1 1.414.586l3.828 3.828A2 2 0 0 1 20 7.828V20a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" />
                                         <path d="M8 14h8" />
                                         <rect x="8" y="10" width="8" height="8" rx="1" />
                                     </svg>
                                     @lang('My eSIMs')
                                 </a>
                                 <a class="dropdown-item" href="{{ route('user.profile.setting') }}">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="lucide lucide-user-pen">
                                         <path d="M11.5 15H7a4 4 0 0 0-4 4v2"></path>
                                         <path d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z">
                                         </path>
                                         <circle cx="10" cy="7" r="4"></circle>
                                     </svg>
                                     @lang('Profile Setting')
                                 </a>
                                 <a class="dropdown-item" href="{{ route('user.change.password') }}">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="lucide lucide-lock-icon lucide-lock">
                                         <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                         <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                     </svg>
                                     @lang('Change Password')
                                 </a>
                                 <a class="dropdown-item" href="{{ route('user.twofactor') }}">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="lucide lucide-scan-barcode">
                                         <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                                         <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                                         <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                                         <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                                         <path d="M8 7v10"></path>
                                         <path d="M12 7v10"></path>
                                         <path d="M17 7v10"></path>
                                     </svg>
                                     @lang('2FA Security')
                                 </a>
                                 <a class="dropdown-item logout" href="{{ route('user.logout') }}">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="lucide lucide-log-out-icon lucide-log-out">
                                         <path d="m16 17 5-5-5-5" />
                                         <path d="M21 12H9" />
                                         <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                     </svg>
                                     @lang('Logout')
                                 </a>
                             </div>
                         </div>
                     </div>
                 @else
                     <a class="btn btn--sm btn--base" href="{{ route('user.login') }}">
                         @lang('Sign In')
                     </a>
                 @endauth
             </div>
         </nav>
     </div>
 </header>
