@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="my-80">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    @if(isset($policy))
                        @php echo $policy->data_values->details; @endphp
                    @else
                        <div class="policy-text policy-autoformat">
                            @yield('policy_fallback')
                        </div>
                    @endif
                </div>
            </div>
    </section>
@endsection

@push('style')
    <style>
        .policy-text { color:#1f2937; line-height:1.7; font-size:16px }
        .policy-text h4 { margin:1.25rem 0 .5rem; font-size:18px; font-weight:700; color:#0a2e2a }
        .policy-text p { margin:.5rem 0 }
        .policy-text .lead { font-weight:600 }
        .policy-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:24px }
    </style>
@endpush

@push('script')
    <script>
    (function(){
        const blocks = document.querySelectorAll('.policy-autoformat');
        blocks.forEach(block => {
            if (block.querySelector('.policy-no-autoformat')) return;
            const raw = block.textContent || '';
            if(!raw.trim()) return;
            const lines = raw.replace(/\r\n/g, '\n').split('\n');
            const frag = document.createDocumentFragment();
            let para = [];
            const flushPara = () => {
                if(para.length){
                    const p = document.createElement('p');
                    p.textContent = para.join(' ');
                    frag.appendChild(p);
                    para = [];
                }
            };
            lines.forEach((ln, idx) => {
                const t = ln.trim();
                if(!t){ flushPara(); return; }
                if(/^\d+\./.test(t)){
                    flushPara();
                    const h = document.createElement('h4');
                    h.textContent = t;
                    frag.appendChild(h);
                } else if(/^(Terms and Conditions|Privacy Policy|Cookies Policy|Refund Policy|Disclosure Disclaimer)$/i.test(t)){
                    flushPara();
                    const h = document.createElement('h4');
                    h.textContent = t;
                    frag.appendChild(h);
                } else if(/^(BROOKBURN INTERNATIONAL LTD|Company Registration Number:|Registered Address:|Last Updated:)/.test(t)){
                    const p = document.createElement('p');
                    p.className = 'lead';
                    p.textContent = t;
                    frag.appendChild(p);
                } else {
                    para.push(t);
                }
            });
            flushPara();
            block.innerHTML = '';
            block.appendChild(frag);
        });
    })();
    </script>
@endpush
