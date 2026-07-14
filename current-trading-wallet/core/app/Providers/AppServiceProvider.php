<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(app_path().'/Helpers/*.php') as $filename){
            require_once($filename);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        //define public path
        $this->app->bind('path.public', function () {
            return dirname(getcwd(), 1) . '/public';
        });

        // Inject $prices for staking view if not set by controller
        view()->composer('themes.prius.user.trade.staking.index', function ($view) {
            $data = $view->getData();
            if (!isset($data['prices'])) {
                $prices = collect([
                    (object)['symbol' => 'BTC_USDT', 'price' => '62000', 'dailyChange' => '2.5'],
                    (object)['symbol' => 'ETH_USDT', 'price' => '1800', 'dailyChange' => '1.8'],
                    (object)['symbol' => 'BNB_USDT', 'price' => '300', 'dailyChange' => '0.5'],
                    (object)['symbol' => 'SOL_USDT', 'price' => '150', 'dailyChange' => '3.2'],
                    (object)['symbol' => 'XRP_USDT', 'price' => '0.52', 'dailyChange' => '-1.2'],
                    (object)['symbol' => 'ADA_USDT', 'price' => '0.45', 'dailyChange' => '0.8'],
                    (object)['symbol' => 'DOGE_USDT', 'price' => '0.12', 'dailyChange' => '5.1'],
                    (object)['symbol' => 'TRX_USDT', 'price' => '0.14', 'dailyChange' => '0.3'],
                    (object)['symbol' => 'DOT_USDT', 'price' => '7.5', 'dailyChange' => '-0.5'],
                    (object)['symbol' => 'MATIC_USDT', 'price' => '0.72', 'dailyChange' => '1.1'],
                    (object)['symbol' => 'LTC_USDT', 'price' => '85', 'dailyChange' => '0.7'],
                    (object)['symbol' => 'AVAX_USDT', 'price' => '35', 'dailyChange' => '2.1'],
                    (object)['symbol' => 'LINK_USDT', 'price' => '15', 'dailyChange' => '1.5'],
                    (object)['symbol' => 'BTC_BTC', 'price' => '1', 'dailyChange' => '0'],
                    (object)['symbol' => 'ETH_BTC', 'price' => '0.029', 'dailyChange' => '-0.5'],
                    (object)['symbol' => 'BNB_BTC', 'price' => '0.0048', 'dailyChange' => '0.3'],
                    (object)['symbol' => 'SOL_BTC', 'price' => '0.0024', 'dailyChange' => '1.2'],
                    (object)['symbol' => 'XRP_BTC', 'price' => '0.0000084', 'dailyChange' => '-2.1'],
                    (object)['symbol' => 'ADA_BTC', 'price' => '0.0000073', 'dailyChange' => '0.1'],
                    (object)['symbol' => 'DOGE_BTC', 'price' => '0.0000019', 'dailyChange' => '3.5'],
                    (object)['symbol' => 'BTC_ETH', 'price' => '34.5', 'dailyChange' => '0.5'],
                    (object)['symbol' => 'BNB_ETH', 'price' => '0.17', 'dailyChange' => '0.2'],
                    (object)['symbol' => 'SOL_ETH', 'price' => '0.083', 'dailyChange' => '1.0'],
                    (object)['symbol' => 'BTC_TRX', 'price' => '442857', 'dailyChange' => '0.1'],
                    (object)['symbol' => 'ETH_TRX', 'price' => '12857', 'dailyChange' => '-0.3'],
                    (object)['symbol' => 'BTC_USDD', 'price' => '62000', 'dailyChange' => '2.5'],
                    (object)['symbol' => 'ETH_USDD', 'price' => '1800', 'dailyChange' => '1.8'],
                ]);
                $view->with('prices', $prices);
            }
            if (!isset($data['symbol_1'])) {
                $view->with('symbol_1', 'BTC');
            }
            if (!isset($data['symbol_2'])) {
                $view->with('symbol_2', 'USDT');
            }
            if (!isset($data['page_title'])) {
                $view->with('page_title', 'Staking');
            }
        });
        
    }
}
