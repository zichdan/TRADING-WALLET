{{-- stats starts here --}}

@foreach ($view_data['sections']->where('name', 'stats') as $section)
    <section class="download-section padding-bottom section-bg-two">
        <div class="container">
            <div class="row justify-content-center">
                 <div class="col-lg-7 col-md-10">
<div class="section__header text-center max-p">
                            <h2 class="section__header-title">{!! json_decode($section->content)->section_heading !!}</h2>
                        <div>
                            {!! json_decode($section->content)->section_text !!}
                        </div>
                    </div>
                </div>
                 <!-- TradingView Widget BEGIN -->
<div class="tradingview-widget-container">
    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-screener.js" async>
  {
  "width": "100%",
  "height": "490",
  "defaultColumn": "overview",
  "screener_type": "crypto_mkt",
  "displayCurrency": "USD",
  "colorTheme": "light",
  "locale": "en",
  "isTransparent": false
}
  </script>
</div>
<!-- TradingView Widget END -->
            </div>
        </div>
    </section>
@endforeach
