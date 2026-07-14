@if (websiteInfo('trader_mode') == 'enabled' && (isAddonEnabled('cryptotrading') || ownedTradingEnabled()))
    @include('themes.cryptic.user.trade.trade.index')    
@else
    @include('themes.cryptic.user.dashboard-content')
@endif
