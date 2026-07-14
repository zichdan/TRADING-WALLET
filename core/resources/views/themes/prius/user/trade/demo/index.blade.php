<!DOCTYPE html>
<html lang="en">

<head class="crypt-dark">
    {{--  Site Metas --}}
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> {{ $page_title }} | {{ websiteInfo('website_name') }}</title>
    <meta name="author" content="support@credcrypto.net">
    <meta name="author" content="support@credcrypto.net">
    <meta name="description" content="{{ json_decode(websiteInfo('meta'))->description }}">
    <meta name="keywords" content="{{ json_decode(websiteInfo('meta'))->keywords }}">
    {{--  Open Graph / Facebook --}}
    <meta property="og:type" content="Crypto Trading">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="{{ $page_title }} | {{ websiteInfo('website_name') }}">
    <meta property="og:description" content="{{ json_decode(websiteInfo('meta'))->description }}">
    <meta property="og:image" content="{{ asset('public/assets/imgs/' . json_decode(websiteInfo('meta'))->banner) }}">
    {{--  Twitter --}}
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ request()->url() }}">
    <meta property="twitter:title" content="{{ $page_title }} | {{ websiteInfo('website_name') }}">
    <meta property="twitter:description" content="{{ json_decode(websiteInfo('meta'))->description }}">
    <meta property="twitter:image"
        content="{{ asset('public/assets/imgs/' . json_decode(websiteInfo('meta'))->banner) }}">
    <meta name="robots" content="noindex">
    <meta name="robots" content="none">

    {{--  Favicon --}}
    <link rel="apple-touch-icon" href="{{ asset('public/assets/imgs/' . json_decode(websiteInfo('meta'))->favicon) }}">
    <link rel="icon" href="{{ asset('public/assets/imgs/' . json_decode(websiteInfo('meta'))->favicon) }}">
    <meta name="msapplication-TileColor" content="#060818">
    <meta name="theme-color" content="#060818">


    {{-- assets starts --}}
    <link rel="stylesheet" href="{{ asset('public/assets/trading/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/trading/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/trading/css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/trading/css/ui.css') }}">
</head>

<body class="crypt-dark" id="body">
    <header>
        <div class="container-full-width">
            <div class="crypt-header">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-5">
                        <div class="row">
                            <div class="col-xs-2">
                                <div class="crypt-logo"><img
                                        src="{{ asset('public/assets/imgs/' . json_decode(websiteInfo('meta'))->logo_rec) }}"
                                        alt=""></div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-8 d-none d-md-block d-lg-block">
                        <ul class="crypt-heading-menu fright">
                            <li><a href="https://trading-wallet.net/user/trading/trade/ETC/USDT">{{ ct('Trading Section') }}</a></li>
                            <li><a href="{{ route('user.dashboard') }}">{{ ct('Dashboard') }}</a></li>
 

                        </ul>
                    </div><i class="menu-toggle pe-7s-menu d-xs-block d-sm-block d-md-none d-sm-none"></i>
                </div>
            </div>
        </div>
        <div class="crypt-mobile-menu">
            <ul class="crypt-heading-menu">
                <li><a href="https://trading-wallet.net/user/trading/trade/ETC/USDT">{{ ct('Trading Section') }}</a></li>
                <li><a href="{{ route('user.dashboard') }}">{{ ct('Dashboard') }}</a></li>

            </ul>

        </div>
    </header>
     <div id="crypt-candle-chart"></div>
    
    <div class="tradingview-widget-container mb-7">
                   
                </div>
 
    <footer>

    </footer>

    <script src="{{ asset('public/assets/trading/amc/core.js') }}"></script>
    <script src="{{ asset('public/assets/trading/amc/charts.js') }}"></script>
    <script src="{{ asset('public/assets/trading/amc/dark.js') }}"></script>
    <script src="{{ asset('public/assets/trading/amc/animated.js') }}"></script>
    <script src="{{ asset('public/assets/trading/js/jquery.js') }}"></script>
    <script src="{{ asset('public/assets/trading/js/popper.min.js') }}"></script>
    <script src="{{ asset('public/assets/trading/bootstrap/js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('public/assets/trading/bootstrap/js/bootstrap.js') }}"></script>
    <script src="{{ asset('public/assets/trading/js/main.js') }}"></script>
    <script src="{{ asset('public/assets/trading/js/amc.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://s3.tradingview.com/tv.js"></script>

    <script>
        //flash starts
        $(document).ready(function() {

            setInterval(function(){
                $('.flash').each(function(i, obj) {

                    var interval = $(obj).data('flash');
                    var flashTime = "{{ rand(1, 20) }}";
                    var time = 1000;
                    if (interval > flashTime) {
                        setTimeout( function(){ 
                            $('.flash:eq('+ i +')').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                         }, time);
                        time += 1000;                       

                    }                    
                });

                // get the href
                var reloadUrl = $(location).attr("href");
                $.get(reloadUrl).done(function(r) {
                    var newDom = $(r);
                    $('#history-table').replaceWith($('#history-table',newDom));
                    $('#market-trading-table').replaceWith($('#market-trading-table',newDom));
                    $('#last-price').replaceWith($('#last-price',newDom));
                    $('#eth-table').replaceWith($('#eth-table',newDom));
                    $('#btc-table').replaceWith($('#btc-table',newDom));
                    $('#usdt-table').replaceWith($('#usdt-table',newDom));
                    $('#trx-table').replaceWith($('#trx-table',newDom));
                    $('#usdd-table').replaceWith($('#usdd-table',newDom));
                });
            }, 5000);

        });

        //toggle  tp
        $('.toggle-tp').on('click', function() {
            var target_toggle = '#' + $(this).data('toggle');
            $(target_toggle).toggleClass('hidden');
        });

        //range input
        $('.range-input').on('input', function() {
            var target_range = '#' + $(this).data('target');
            var value = $(this).val() + '%';
            $(target_range).html(value);
        });



        //filter starts here
        $(document).ready(function() {
            $("#filter-input").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#filter-div tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        //trading starts here
        $('.order-button').on('click', function(e) {
            e.preventDefault();

            var clicked = $(this);
            $(clicked).addClass('loading');
            var target = $('.loading > a').eq(0);
            var html = target.html();
            var order = $(this).data('order');
            var order_type = $(this).data('type');
            var amount = '';
            var base = $('#base_coin').html();
            var quote = $('#quote_coin').html();
            var tp = '';
            var sl = '';
            var leverage = '';
            var amount_target = '';
            var error = false;


            if (order == 'market' && order_type == 'buy') {
                amount = $('.amount-field').eq(0).val();
                amount_target = 0;
                tp = $('.tp').eq(0).val();
                sl = $('.sl').eq(0).val();
                leverage = $('#market-buy-range').html();
            } else if (order == 'market' && order_type == 'sell') {
                amount = $('.amount-field').eq(1).val();
                amount_target = 1;
                tp = $('.tp').eq(1).val();
                sl = $('.sl').eq(1).val();
                leverage = $('#market-sell-range').html();
            } else if (order == 'limit' && order_type == 'buy') {
                amount = $('.amount-field').eq(2).val();
                amount_target = 2;
                leverage = $('#limit-buy-range').html();
            } else if (order == 'limit' && order_type == 'sell') {
                amount = $('.amount-field').eq(3).val();
                amount_target = 3;
                leverage = $('#limit-sell-range').html();
            } else if (order == 'stop-limit' && order_type == 'buy') {
                amount = $('.amount-field').eq(4).val();
                amount_target = 4;
                leverage = $('#stop-limit-buy-range').html();
            } else if (order == 'stop-limit' && order_type == 'sell') {
                amount = $('.amount-field').eq(0).val(5);
                amount_target = 5;
                leverage = $('#stop-limit-sell-range').html();
            }

            if (+amount < 0.000000001) {
                error = true;
                $('.amount-field').eq(amount_target).addClass('error-border');

            } else {
                //submit
                // $('#preloader').show();
                $(target).html('Wait...').fadeOut(300).fadeIn(200);
                $.ajax({
                    url: "{{ route('user.trading.trade.trade-validate') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        order: order,
                        order_type: order_type,
                        amount: amount,
                        base: base,
                        quote: quote,
                        tp: tp,
                        sl: sl,
                        leverage: leverage

                    },
                    success: function(response) {
                        target.html('Successful');
                        Swal.fire({
                            title: '',
                            text: "Trade Place",
                            icon: 'success',
                            background: "#0e1726",
                            color: "#d1d5db",

                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });

                    },
                    error: function(response) {
                        // $('#preloader').hide(); 
                        Swal.fire({
                            title: '',
                            text: "Failed to place trade",
                            icon: 'error',
                            background: "#0e1726",
                            color: "#d1d5db",

                        });
                        $(target).html(html);


                    },
                });
            }

        });
    </script>


    <script>
        if (document.getElementById('crypt-candle-chart')) {
            new TradingView.widget({
                "width": "100%",
  "height": "600px",
                "symbol": "POLONIEX:" + "ETCUSDT",
                "interval": "1",
                "timezone": "Etc/UTC",
                "theme": "Dark",
                "style": "1",
                "locale": "en",
                "toolbar_bg": "rgba(0, 0, 0, 1)",
                "enable_publishing": false,
                "allow_symbol_change": true,
            });
        }
    </script>

    <script>
        $('.stop-trade').on('click', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('user.trading.trade.end-trade') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id,


                },
                success: function(response) {

                    Swal.fire({
                        title: '',
                        text: "Trade Stopped",
                        icon: 'success',
                        background: "#0e1726",
                        color: "#d1d5db",

                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });

                },
                error: function(response) {
                    // $('#preloader').hide(); 
                    Swal.fire({
                        title: '',
                        text: "Failed to stop trade",
                        icon: 'error',
                        background: "#0e1726",
                        color: "#d1d5db",

                    });


                },
            });

        })
    </script>




    <style>
        input[type=checkbox] {
            accent-color: #49c279;
        }

        input[type=range] {
            accent-color: #f7614e;
        }

        progress {
            accent-color: black;
        }

        .hidden {
            display: none;
        }

        .error-border {
            border-color: red !important;
        }
    </style>

</body>

</html>
