 <header class="header" id="header">
     <div class="container">
         <nav class="navbar navbar-expand-lg">
             <a class="navbar-brand order-1 {{ currentBrand('navbar_brand_class') }}" href="{{ route('home') }}">
                 <img src="{{ siteLogo('dark') }}" alt="{{ currentBrand('name') }}" class="{{ currentBrand('logo_img_class') }}">
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
                     <li class="nav-item {{ menuActive('contact') }}">
                         <a class="nav-link" href="{{ route('contact') }}">
                             @lang('Contact')
                         </a>
                     </li>
                     {{-- Language switcher removed by request --}}
                 </ul>
             </div>

            <div class="navbar-auth-area order-2 order-lg-4 ms-auto d-flex align-items-center gap-3">
                {{-- Currency Switcher --}}
                <div class="currency-switcher" style="display: flex; gap: 5px; align-items: center;">
                    <button type="button" class="btn-currency btn-currency-eur" data-currency="EUR" style="padding: 5px 12px; border: 1px solid #ddd; background: #fff; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.3s;">
                        € EUR
                    </button>
                    <button type="button" class="btn-currency btn-currency-gbp" data-currency="GBP" style="padding: 5px 12px; border: 1px solid #ddd; background: #fff; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.3s;">
                        £ GBP
                    </button>
                    <button type="button" class="btn-currency btn-currency-usd" data-currency="USD" style="padding: 5px 12px; border: 1px solid #ddd; background: #fff; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.3s;">
                        $ USD
                    </button>
                </div>

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
                                        <span style="color: #28a745;">💰 {{ showAmount(auth()->user()->balance, 2, true, false, true, true) }}</span>
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

@push('script')
<script>
    (function() {
        'use strict';
        
        // Fixed exchange rates
        const EXCHANGE_RATES = {
            'GBP': 0.87,  // 1 EUR = 0.87 GBP
            'USD': 1.18   // 1 EUR = 1.18 USD
        };
        const BASE_CURRENCY = 'EUR';
        
        // Currency symbols
        const CURRENCY_SYMBOLS = {
            'EUR': '€',
            'GBP': '£',
            'USD': '$'
        };
        
        // Get current currency from localStorage or default to EUR
        let currentCurrency = localStorage.getItem('selectedCurrency') || BASE_CURRENCY;
        
        // Initialize currency switcher
        function initCurrencySwitcher() {
            const eurBtn = document.querySelector('.btn-currency-eur');
            const gbpBtn = document.querySelector('.btn-currency-gbp');
            const usdBtn = document.querySelector('.btn-currency-usd');
            
            if (!eurBtn || !gbpBtn || !usdBtn) {
                console.warn('Currency buttons not found, retrying...');
                setTimeout(initCurrencySwitcher, 100);
                return;
            }
            
            // Set active button
            updateActiveButton();
            
            // Add click handlers
            eurBtn.addEventListener('click', () => switchCurrency('EUR'));
            gbpBtn.addEventListener('click', () => switchCurrency('GBP'));
            usdBtn.addEventListener('click', () => switchCurrency('USD'));
            
            console.log('Currency switcher initialized');
        }
        
        // Update active button style
        function updateActiveButton() {
            const eurBtn = document.querySelector('.btn-currency-eur');
            const gbpBtn = document.querySelector('.btn-currency-gbp');
            const usdBtn = document.querySelector('.btn-currency-usd');
            
            // Reset all buttons
            [eurBtn, gbpBtn, usdBtn].forEach(btn => {
                if (btn) {
                    btn.style.background = '#fff';
                    btn.style.color = '#000';
                    btn.style.borderColor = '#ddd';
                }
            });
            
            // Set active button
            const activeBtn = document.querySelector(`.btn-currency-${currentCurrency.toLowerCase()}`);
            if (activeBtn) {
                activeBtn.style.background = '#007bff';
                activeBtn.style.color = '#fff';
                activeBtn.style.borderColor = '#007bff';
            }
        }
        
        // Switch currency
        function switchCurrency(currency) {
            if (currency === currentCurrency) return;
            
            currentCurrency = currency;
            localStorage.setItem('selectedCurrency', currency);
            
            // Update global currency immediately
            window.globalCurrency = currency;
            
            updateActiveButton();
            updateAllPrices();
            
            // Dispatch event for other scripts
            window.dispatchEvent(new CustomEvent('currencyChanged', { detail: { currency } }));
        }
        
        // Convert price
        function convertPrice(amount, fromCurrency, toCurrency) {
            if (fromCurrency === toCurrency) return amount;
            
            // Always convert from EUR (base currency) to target currency
            if (fromCurrency === BASE_CURRENCY && EXCHANGE_RATES[toCurrency]) {
                return amount * EXCHANGE_RATES[toCurrency];
            }
            
            // Convert from target currency back to EUR (base)
            if (toCurrency === BASE_CURRENCY && EXCHANGE_RATES[fromCurrency]) {
                return amount / EXCHANGE_RATES[fromCurrency];
            }
            
            return amount;
        }
        
        // Format price
        function formatPrice(amount) {
            return parseFloat(amount).toFixed(2);
        }
        
        // Update all prices on page
        function updateAllPrices() {
            // Update prices in choose-plan-item__price (using data attributes)
            document.querySelectorAll('.choose-plan-item__price').forEach(function(element) {
                // Try to get base amount from data attribute first
                const baseAmount = parseFloat(element.getAttribute('data-base-amount'));
                const baseCurrency = element.getAttribute('data-base-currency') || BASE_CURRENCY;
                
                if (!isNaN(baseAmount) && baseAmount > 0) {
                    // Use data attributes
                    if (currentCurrency === BASE_CURRENCY) {
                        // Show original EUR price
                        const formatted = formatPrice(baseAmount);
                        element.innerHTML = formatted + ' <span class="currency">EUR</span>';
                    } else {
                        // Convert to target currency
                        const converted = convertPrice(baseAmount, baseCurrency, currentCurrency);
                        const formatted = formatPrice(converted);
                        element.innerHTML = formatted + ' <span class="currency">' + currentCurrency + '</span>';
                    }
                } else {
                    // Fallback: parse from text
                    const currencySpan = element.querySelector('.currency');
                    if (!currencySpan) return;
                    
                    let text = element.textContent.trim();
                    const currencyMatch = text.match(/([€£$]|EUR|GBP|USD)\s*([\d,]+\.?\d*)/);
                    
                    if (currencyMatch) {
                        const baseAmount = parseFloat(currencyMatch[2].replace(/,/g, ''));
                        let baseCurrency = BASE_CURRENCY;
                        if (currencyMatch[1] === '£' || currencyMatch[1] === 'GBP') baseCurrency = 'GBP';
                        else if (currencyMatch[1] === '$' || currencyMatch[1] === 'USD') baseCurrency = 'USD';
                        
                        if (currentCurrency === BASE_CURRENCY) {
                            const formatted = formatPrice(baseAmount);
                            element.innerHTML = formatted + ' <span class="currency">EUR</span>';
                        } else {
                            const converted = convertPrice(baseAmount, baseCurrency, currentCurrency);
                            const formatted = formatPrice(converted);
                            element.innerHTML = formatted + ' <span class="currency">' + currentCurrency + '</span>';
                        }
                    }
                }
            });
            
            // Update prices in esim-plan-card__price (using data attributes)
            document.querySelectorAll('.esim-plan-card__price').forEach(function(element) {
                // Try to get base amount from data attribute first
                const baseAmount = parseFloat(element.getAttribute('data-base-amount'));
                const baseCurrency = element.getAttribute('data-base-currency') || BASE_CURRENCY;
                
                if (!isNaN(baseAmount) && baseAmount > 0) {
                    // Use data attributes
                    if (currentCurrency === BASE_CURRENCY) {
                        // Show original EUR price
                        const formatted = formatPrice(baseAmount);
                        element.textContent = 'From ' + CURRENCY_SYMBOLS[BASE_CURRENCY] + formatted + ' ' + BASE_CURRENCY;
                    } else {
                        // Convert to target currency
                        const converted = convertPrice(baseAmount, baseCurrency, currentCurrency);
                        const formatted = formatPrice(converted);
                        element.textContent = 'From ' + CURRENCY_SYMBOLS[currentCurrency] + formatted + ' ' + currentCurrency;
                    }
                } else {
                    // Fallback: parse from text
                    let text = element.textContent.trim();
                    const match = text.match(/From\s*([€£$]|EUR|GBP|USD)?\s*([\d,]+\.?\d*)\s*(EUR|GBP|USD)?/i);
                    
                    if (match) {
                        const amount = parseFloat(match[2].replace(/,/g, ''));
                        let baseCurrency = BASE_CURRENCY;
                        
                        if (match[1] === '£' || match[3] === 'GBP') {
                            baseCurrency = 'GBP';
                        } else if (match[1] === '$' || match[3] === 'USD') {
                            baseCurrency = 'USD';
                        }
                        
                        if (currentCurrency === BASE_CURRENCY) {
                            const formatted = formatPrice(amount);
                            element.textContent = 'From ' + CURRENCY_SYMBOLS[BASE_CURRENCY] + formatted + ' ' + BASE_CURRENCY;
                        } else {
                            const converted = convertPrice(amount, baseCurrency, currentCurrency);
                            const formatted = formatPrice(converted);
                            element.textContent = 'From ' + CURRENCY_SYMBOLS[currentCurrency] + formatted + ' ' + currentCurrency;
                        }
                    }
                }
            });
            
            // Update prices in destination page (country cards)
            document.querySelectorAll('.country-card__price, .search-result-item__price').forEach(function(element) {
                const baseAmount = parseFloat(element.getAttribute('data-base-amount'));
                const baseCurrencyAttr = element.getAttribute('data-base-currency') || BASE_CURRENCY;
                
                if (!isNaN(baseAmount) && baseAmount > 0) {
                    if (currentCurrency === BASE_CURRENCY) {
                        const formatted = formatPrice(baseAmount);
                        // Remove existing currency elements and add new one
                        element.querySelectorAll('.currency').forEach(el => el.remove());
                        element.textContent = CURRENCY_SYMBOLS[BASE_CURRENCY] + formatted + ' ';
                        const currencySpan = document.createElement('span');
                        currencySpan.className = 'currency';
                        currencySpan.textContent = BASE_CURRENCY;
                        element.appendChild(currencySpan);
                    } else {
                        const converted = convertPrice(baseAmount, baseCurrencyAttr, currentCurrency);
                        const formatted = formatPrice(converted);
                        // Remove existing currency elements and add new one
                        element.querySelectorAll('.currency').forEach(el => el.remove());
                        element.textContent = CURRENCY_SYMBOLS[currentCurrency] + formatted + ' ';
                        const currencySpan = document.createElement('span');
                        currencySpan.className = 'currency';
                        currencySpan.textContent = currentCurrency;
                        element.appendChild(currencySpan);
                    }
                } else {
                    // Fallback: parse from text
                    let text = element.textContent.trim();
                    const match = text.match(/([€£$]|EUR|GBP|USD)?\s*([\d,]+\.?\d*)\s*(EUR|GBP|USD)?/i);
                    
                    if (match) {
                        const amount = parseFloat(match[2].replace(/,/g, ''));
                        let baseCurrency = BASE_CURRENCY;
                        
                        if (match[1] === '£' || match[3] === 'GBP') {
                            baseCurrency = 'GBP';
                        } else if (match[1] === '$' || match[3] === 'USD') {
                            baseCurrency = 'USD';
                        }
                        
                        if (currentCurrency === BASE_CURRENCY) {
                            const formatted = formatPrice(amount);
                            element.textContent = CURRENCY_SYMBOLS[BASE_CURRENCY] + formatted + ' ' + BASE_CURRENCY;
                        } else {
                            const converted = convertPrice(amount, baseCurrency, currentCurrency);
                            const formatted = formatPrice(converted);
                            element.textContent = CURRENCY_SYMBOLS[currentCurrency] + formatted + ' ' + currentCurrency;
                        }
                    }
                }
            });
            
            // Update sidebar plan details
            const sidebarPrice = document.querySelector('.choose-plan-sidebar__price');
            if (sidebarPrice) {
                const selectedPlan = document.querySelector('input[name="plan_id"]:checked');
                if (selectedPlan && selectedPlan.dataset.price) {
                    const priceData = selectedPlan.dataset.price.split(' ');
                    if (priceData.length >= 2) {
                        const baseCurrency = priceData[0];
                        const amount = parseFloat(priceData[1]);
                        
                        if (currentCurrency === BASE_CURRENCY) {
                            sidebarPrice.textContent = CURRENCY_SYMBOLS[BASE_CURRENCY] + formatPrice(amount) + ' ' + BASE_CURRENCY;
                        } else {
                            const converted = convertPrice(amount, baseCurrency, currentCurrency);
                            sidebarPrice.textContent = CURRENCY_SYMBOLS[currentCurrency] + formatPrice(converted) + ' ' + currentCurrency;
                        }
                    }
                }
            }
        }
        
        // Initialize on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initCurrencySwitcher();
                updateAllPrices();
                
                // Set initial global currency
                window.globalCurrency = currentCurrency;
            });
        } else {
            // DOM already loaded
            initCurrencySwitcher();
            updateAllPrices();
            window.globalCurrency = currentCurrency;
        }
        
        // Make functions globally available
        window.switchCurrency = switchCurrency;
        window.updateAllPrices = updateAllPrices;
        
        // Set initial global currency
        window.globalCurrency = currentCurrency;
    })();
</script>
@endpush
