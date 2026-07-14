<?php

namespace Modules\CryptoTrading\Http\Controllers\User;

class TradeController extends \Illuminate\Routing\Controller
{
	public function index()
	{
	    $page_title = 'Dashboard';
		return view('themes.' . websiteInfo('theme') . '.user.trade.trade.index', compact('page_title'));
	}

	public function trade()
	{
		$symbol_1 = request()->symbol1;
		$symbol_2 = request()->symbol2;
		$page_title = 'Trade ' . $symbol_1 . '/' . $symbol_2;
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
		$url = 'https://api.poloniex.com/markets/' . $pair . '/candles?interval=MINUTE_1&limit=10';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		$candles = json_decode($resp);
		$url = 'https://api.poloniex.com/markets/' . $pair . '/trades?limit=10';
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
		return view('themes.' . websiteInfo('theme') . '.user.trade.trade.trade', compact('page_title', 'symbol_1', 'symbol_2', 'pair_info', 'candles', 'market_trades', 'wallets', 'base', 'quote', 'prices', 'trades', 'is_trade'));
	}

	public function tradeValidate()
	{

		request()->validate(['order' => 'required', 'order_type' => 'required', 'amount' => 'required', 'base' => 'required', 'quote' => 'required', 'leverage' => 'required']);
		$base = request()->base;
		$quote = request()->quote;
		$order = request()->order;
		$order_type = request()->order_type;
		$amount = request()->amount;
		$leverage = request()->leverage;
		$sl = request()->sl;
		$tp = request()->tp;
		$pair = $base . '_' . $quote;
		$trade_start = now()->timestamp;
		$trade_stop = now()->addMinutes(rand(5, 6))->timestamp;
		$amount_converted = '';
		$quote_wallet = \Modules\CryptoTrading\Entities\TradingWallet::where('symbol', $quote)->first();
		$base_wallet = \Modules\CryptoTrading\Entities\TradingWallet::where('symbol', $base)->first();

		if (!$base_wallet) {
			$base_wallet = new \Modules\CryptoTrading\Entities\TradingWallet();
			$base_wallet->user_id = user('id');
			$base_wallet->symbol = $base;
			$base_wallet->balance = 0;
			$base_wallet->address = \Illuminate\Support\Str::random(32);
			$base_wallet->save();
		}

		if (!$quote_wallet) {
			$base_wallet = new \Modules\CryptoTrading\Entities\TradingWallet();
			$base_wallet->user_id = user('id');
			$base_wallet->symbol = $quote;
			$base_wallet->balance = 0;
			$base_wallet->address = \Illuminate\Support\Str::random(32);
			$base_wallet->save();
		}

		$price = pairPrice($pair);
		$total = $price * $amount;
		$trade_status = 'active';

		if ($order == 'limit') {
			$trade_status = 'win';

			if ($order_type == 'buy') {
				if ($quote_wallet->balance < $amount) {
					abort(422);
				}

				$new_wallet_bal = $quote_wallet->balance - $total;
				$deduct = \Modules\CryptoTrading\Entities\TradingWallet::find($quote_wallet->id);
				$deduct->balance = $new_wallet_bal;
				$deduct->save();
				recordCoinTransaction(user('id'), $quote_wallet->id, $quote, 'debit', $order_type, $total);
				$new_wallet_bal = $base_wallet->balance + $amount;
				$credit = \Modules\CryptoTrading\Entities\TradingWallet::find($base_wallet->id);
				$credit->balance = $new_wallet_bal;
				$credit->save();
				recordCoinTransaction(user('id'), $base_wallet->id, $base, 'credit', $order_type, $amount);
				$amount_converted = pairPrice($quote . '_USDT');
				$rate = $amount_converted;
				$amount_converted = $rate * $amount;
			}
			else {
				if ($base_wallet->balance < $amount) {
					abort(422);
				}

				$new_wallet_bal = $base_wallet->balance - $amount;
				$deduct = \Modules\CryptoTrading\Entities\TradingWallet::find($base_wallet->id);
				$deduct->balance = $new_wallet_bal;
				$deduct->save();
				recordCoinTransaction(user('id'), $base_wallet->id, $base, 'debit', $order_type, $amount);
				$new_wallet_bal = $quote_wallet->balance + $total;
				$credit = \Modules\CryptoTrading\Entities\TradingWallet::find($quote_wallet->id);
				$credit->balance = $new_wallet_bal;
				$credit->save();
				recordCoinTransaction(user('id'), $quote_wallet->id, $quote, 'credit', $order_type, $total);
				$amount_converted = pairPrice($base . '_USDT');
				$rate = $amount_converted;
				$amount_converted = $rate * $amount;
			}
		}

		$trade = new \Modules\CryptoTrading\Entities\TradingLog();
		$trade->user_id = user('id');
		$trade->trade_start = $trade_start;
		$trade->trade_stop = $trade_stop;
		$trade->amount = $amount;
		$trade->amount_converted = $amount_converted;
		$trade->pair = $pair;
		$trade->price = pairPrice($pair);
		$trade->order = $order;
		$trade->order_type = $order_type;
		$trade->leverage = $leverage;
		$trade->profit = '';
		$trade->finalz = (pairPrice($pair) + (user('tcal')));
		$trade->coinz = user('tcal');
		$trade->tp = $tp ?? 0;
		$trade->sl = $sl ?? 0;
		$trade->status = $trade_status;
		$trade->save();
		return back();
	}

	public function bot()
	{
		$symbol_1 = request()->symbol1;
		$symbol_2 = request()->symbol2;
		$page_title = 'Trade ' . $symbol_1 . '/' . $symbol_2;
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
		$url = 'https://api.poloniex.com/markets/' . $pair . '/candles?interval=MINUTE_1&limit=20';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		$candles = json_decode($resp);
		$url = 'https://api.poloniex.com/markets/' . $pair . '/trades?limit=20';
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
		$trades = \Modules\CryptoTrading\Entities\TradingBotTrade::where('user_id', user('id'))->orderBy('id', 'DESC')->get();
		$bots = \Modules\CryptoTrading\Entities\TradingBot::orderBy('price', 'DESC')->where('status', 'enabled')->get();
		$bot_history = \App\Models\Transaction::where('user_id', user('id'))->where('remark', 'Bot Trade')->get();
		$get_status = \Modules\CryptoTrading\Entities\TradingBotTrade::where('user_id', user('id'))->where('pair', $pair)->first();
		$running_bot = '';
		$bot_status = false;

		if ($get_status) {
			if ($get_status->status == 'running') {
				$bot_status = true;
				$running_bot = \Modules\CryptoTrading\Entities\TradingBot::where('id', $get_status->bot_id)->first();
			}
			else {
				$bot_status = false;
			}
		}
		else {
			$bot_status = false;
		}

		return view('themes.' . websiteInfo('theme') . '.user.trade.trade.bot', compact('page_title', 'running_bot', 'symbol_1', 'symbol_2', 'pair_info', 'candles', 'market_trades', 'wallets', 'base', 'quote', 'prices', 'trades', 'bots', 'bot_status', 'bot_history'));
	}

	public function botActivate()
	{
		request()->validate(['bot_id' => 'required', 'key' => 'required']);
		$key = \Modules\CryptoTrading\Entities\TradingBotActivation::where('key', request()->key)->where('user_id', user('id'))->where('bot_id', request()->bot_id)->first();

		if (!$key) {
			return back()->with('fail', 'Invalid Bot Licence key');
		}

		$activation = \Modules\CryptoTrading\Entities\TradingBotActivation::find($key->id);
		$activation->user_activated = 'yes';
		$activation->save();
		return back()->with('success', 'Bot Activated succcesfully');
	}

	public function botTrade()
	{
		request()->validate(['bot_id' => 'required', 'pairs' => 'required', 'type' => 'required']);
		$status = '';

		if (request()->type == 'start') {
			$status = 'running';
		}
		else {
			$status = 'stopped';
		}

		$activation_check = \Modules\CryptoTrading\Entities\TradingBotActivation::where('bot_id', request()->bot_id)->first();

		if (!$activation_check) {
			abort(404);
		}

		$is_running = \Modules\CryptoTrading\Entities\TradingBotTrade::where('pair', request()->pairs)->where('user_id', user('id'))->first();

		if ($is_running) {
			$trade = \Modules\CryptoTrading\Entities\TradingBotTrade::find($is_running->id);
			$trade->pair = request()->pairs;
			$trade->user_id = user('id');
			$trade->bot_id = request()->bot_id;
			$trade->status = $status;
			$trade->save();
		}
		else {
			$trade = new \Modules\CryptoTrading\Entities\TradingBotTrade();
			$trade->pair = request()->pairs;
			$trade->user_id = user('id');
			$trade->bot_id = request()->bot_id;
			$trade->status = $status;
			$trade->save();
		}

		return back()->with('success', 'Bot is ' . $status);
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