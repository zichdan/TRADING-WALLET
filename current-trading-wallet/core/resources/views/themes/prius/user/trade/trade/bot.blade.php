@php
    use Modules\CryptoTrading\Entities\TradingBot;
    function botName($bot_id) 
    {
        $bot = TradingBot::where('id', $bot_id)->first();
        return $bot->name;
    }
@endphp


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
    <meta property="og:image" content="{{ asset('public/assets/imgs/' . json_decode(websiteInfo('meta'))->banner ) }}">
    {{--  Twitter --}}
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ request()->url() }}">
    <meta property="twitter:title" content="{{ $page_title }} | {{ websiteInfo('website_name') }}">
    <meta property="twitter:description" content="{{ json_decode(websiteInfo('meta'))->description }}">
    <meta property="twitter:image" content="{{ asset('public/assets/imgs/' . json_decode(websiteInfo('meta'))->banner ) }}">
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
                            <div class="col-xs-2">
                                <div class="crypt-mega-dropdown-menu"> <a href=""
                                        class="crypt-mega-dropdown-toggle">{{ $symbol_1 . '/' . $symbol_2 }}</a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-8 d-none d-md-block d-lg-block">
                        <ul class="crypt-heading-menu fright">
                            <li><a href="{{ route('user.dashboard') }}">{{ ct('Dashboard') }}</a></li>


                        </ul>
                    </div><i class="menu-toggle pe-7s-menu d-xs-block d-sm-block d-md-none d-sm-none"></i>
                </div>
            </div>
        </div>
        <div class="crypt-mobile-menu">
            <ul class="crypt-heading-menu">
                <li><a href="{{ route('user.dashboard') }}">{{ ct('Dashboard') }}</a></li>

            </ul>
            
        </div>
    </header>
    <div class="container-fluid">
       <div class="w-full flex justify-center"> <center><h3>This Section is Currently Under Maintenance</h3></center>

</div>
    </div>
   
    <footer>
        <style>
            #preloader {
                background: rgb(14, 23, 38, 0.9);
                position: fixed;
                top: 0px;
                right: 0px;
                bottom: 0px;
                left: 0px;
                z-index: 9999999999999;
                display: flex;
                justify-content: center;
            }

            #loading-bar-spinner.spinner {
                left: 50%;
                margin-left: -20px;
                top: 50%;
                margin-top: -20px;
                position: absolute;
                z-index: 19 !important;
                -webkit-animation: loading-bar-spinner 400ms linear infinite;
                animation: loading-bar-spinner 400ms linear infinite;
            }

            #loading-bar-spinner.spinner .spinner-icon {
                width: 40px;
                height: 40px;
                border: solid 4px transparent;
                border-top-color: rgb(234, 88, 12) !important;
                border-left-color: rgb(234, 88, 12) !important;
                border-radius: 50%;
            }

            @-webkit-keyframes loading-bar-spinner {
                0% {
                    transform: rotate(0deg);
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                    transform: rotate(360deg);
                }
            }

            @keyframes loading-bar-spinner {
                0% {
                    transform: rotate(0deg);
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                    transform: rotate(360deg);
                }
            }

            #bot-change-button {
                cursor: pointer;
            }
        </style>
    </footer>

    <script src="/public/assets/trading/amc/core.js"></script>
    <script src="/public/assets/trading/amc/charts.js"></script>
    <script src="/public/assets/trading/amc/dark.js"></script>
    <script src="/public/assets/trading/amc/animated.js"></script>
    <script src="/public/assets/trading/js/jquery.js"></script>
    <script src="/public/assets/trading/js/popper.min.js"></script>
    <script src="/public/assets/trading/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="/public/assets/trading/bootstrap/js/bootstrap.js"></script>
    <script src="/public/assets/trading/js/main.js"></script>
    <script src="/public/assets/trading/js/amc.js"></script>
    <script src="https://s3.tradingview.com/tv.js"></script>
    
    <script>       


        //flash starts
        $(document).ready(function(){           

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
                    $('#usdt-table').replaceWith($('#usdt-table',newDom));
                    $('#btc-table').replaceWith($('#btc-table',newDom));
                    $('#eth-table').replaceWith($('#eth-table',newDom));
                    $('#trx-table').replaceWith($('#trx-table',newDom));
                    $('#usdd-table').replaceWith($('#usdd-table',newDom));
                });
            }, 5000);


            $('#preloader').hide();
            
        });

        $('#bot-change-button').on('click', function(){
            $('.bots-popup').toggleClass('hidden');
        });

        $('.is_not_activated').on('click', function(){
            var bot_id = $(this).data('bot_id');
            $('.bots-popup').toggleClass('hidden');
            $('.form-popup').toggleClass('hidden');
            $('#bot_id').val(bot_id);
        });


        //close  activation form
        $('.form-close-btn').on('click', function(){
            $('.bots-popup').toggleClass('hidden');
            $('.form-popup').toggleClass('hidden');
        });

         //close  popup form
         $('.popup-close-btn').on('click', function(){
            $('.bots-popup').toggleClass('hidden');
        });
        

        //select the bot
        $('.is_activated').on('click', function(){
            var bot_name  = $(this).data('bot_name');
            var bot_id = $(this).data('bot_id');
            $('#selected_bot_id').val(bot_id);
            $('#selected_bot_name').val(bot_name);
            $('.bots-popup').toggleClass('hidden');
        });

       

       

        //range input
        


        //filter starts here
        $(document).ready(function(){
            $("#filter-input").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#filter-div tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        //trading starts here
        $('.order-button').on('click', function(e){
            e.preventDefault();
            
            var  clicked = $(this);
            $(clicked).addClass('loading');
            var target  = $('.loading > a').eq(0);
            var html = target.html();            
            var bot_id = $('#selected_bot_id').val();
            var pairs = "{{ $symbol_1 . '_' . $symbol_2 }}";
            var type  = $(this).data('type');

            $(target).html('Wait...').fadeOut(300).fadeIn(200);
            $.ajax({
                url: "{{  route('user.trading.trade.bot-trade-validate') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    bot_id:bot_id,
                    pairs:pairs,
                    type:type
                    
                },
                success: function(response) {
                    target.html('Successful');
                    alert('Success ' + type + ' bot');
                    window.location.reload();                        
                },
                error: function(response) {                    
                    // $('#preloader').hide(); 
                    alert('Failed to ' + type + ' bot');
                    $(target).html(html);  
                    console.log(response);                     

                },
            });
            
        });

        
    </script>


    <script>
        if (document.getElementById('crypt-candle-chart')) {
            new TradingView.widget({
                "autosize": true,
                "symbol": "POLONIEX:" + "{{ $symbol_1 . $symbol_2 }}",
                "interval": "1",
                "timezone": "Etc/UTC",
                "theme": "Dark",
                "style": "1",
                "locale": "en",
                "toolbar_bg": "rgba(0, 0, 0, 1)",
                "enable_publishing": false,
                "allow_symbol_change": true,
                "container_id": "crypt-candle-chart"
            });
        }
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
