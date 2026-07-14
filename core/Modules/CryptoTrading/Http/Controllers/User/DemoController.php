<?php

namespace Modules\CryptoTrading\Http\Controllers\User;

class DemoController extends \Illuminate\Routing\Controller
{
	public function index()
	{
		$page_title = 'Trading Chart';
		return view('themes.' . websiteInfo('theme') . '.user.trade.demo.index', compact('page_title'));
		
	}

	public function trade()
	{
		$symbol_1 = request()->symbol1;
		$symbol_2 = request()->symbol2;
		$page_title = 'Trade History ' . $symbol_1 . '/' . $symbol_2;
		$symbols = \Modules\CryptoTrading\Entities\TradingCurrency::orderBy('symbol', 'ASC')->get();
		$pair = $symbol_1 . '_' . $symbol_2;
		$wallets = \Modules\CryptoTrading\Entities\TradingWallet::where('user_id', user('id'))->get();
		$base = $wallets->where('symbol', $symbol_1)->first();
		$quote = $wallets->where('symbol', $symbol_2)->first();
		$url = 'https://api.poloniex.com/currencies?includeMultiChainCurrencies=false';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		$currencies = json_decode($resp);
		$url = 'https://api.poloniex.com/markets/' . $pair . '/ticker24h';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		$pair_info = json_decode($resp);
		$url = 'https://api.poloniex.com/markets/' . $pair . '/candles?interval=MINUTE_1&limit=13';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		$candles = json_decode($resp);
		$url = 'https://api.poloniex.com/markets/' . $pair . '/trades?limit=13';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		$market_trades = json_decode($resp);
		$url = 'https://api.poloniex.com/markets/price';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		$prices = json_decode($resp) ?? [];
		$trades = \Modules\CryptoTrading\Entities\TradingLog::where('pair', $pair)->where('user_id', user('id'))->orderBy('id', 'DESC')->get();
		$is_trade = $trades->where('status', 'active');
		return view('themes.' . websiteInfo('theme') . '.user.trade.demo.trade', compact('page_title', 'symbol_1', 'symbol_2', 'pair_info', 'candles', 'market_trades', 'wallets', 'base', 'quote', 'prices', 'trades', 'is_trade'));
	}
	public function endTrade()
	{
		request()->validate(['id' => 'required']);
		$id = \Illuminate\Support\Facades\Crypt::decryptString(request()->id);
		$trade = \Modules\CryptoTrading\Entities\TradingLog::where('user_id', user('id'))->where('id', $id)->where('status', 'active')->first();
		$time = time();

		if ($trade->order != 'market') {
			$update = \Modules\CryptoTrading\Entities\TradingLog::find($trade->id);
			$update->status = 'win';
			$update->save();
		}
		else {
			$current_price = (pairPrice($trade->pair) + $trade->coinz);
			$entry_price = $trade->price;
			$profit_loss = '';
			$trade_status = '';
			$profit_loss = str_replace('%', '', $trade->leverage) * (($current_price - $entry_price) * $trade->amount);
			if ($trade->order_type == 'buy') {
				if ($entry_price < $current_price) {
					$trade_status = 'win';
				}
				else {
					$trade_status = 'lose';
				}
			}
			else if ($current_price < $entry_price) {
				$trade_status = 'win';
			}
			else {
				$trade_status = 'lose';
			}

			$update = \Modules\CryptoTrading\Entities\TradingLog::find($trade->id);
			$update->status = $trade_status;
			$update->finalz = $current_price;
			$update->profit = $profit_loss;
			$update->save();
			$wallets = explode('_', $trade->pair);
			$wallet = $wallets[1];
			$trading_wallet = \Modules\CryptoTrading\Entities\TradingWallet::where('symbol', $wallet)->where('user_id', $trade->user_id)->first();

			if ($trading_wallet) {
				$new_bal = $trading_wallet->balance + $profit_loss;
				$credit = \Modules\CryptoTrading\Entities\TradingWallet::find($trading_wallet->id);
				$credit->balance = $new_bal;
				$credit->save();
				recordCoinTransaction($trade->user_id, $trading_wallet->id, $wallet, 'credit', 'market', $trade->amount);
			}
		}
	}
}


?>