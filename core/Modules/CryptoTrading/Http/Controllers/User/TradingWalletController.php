<?php

namespace Modules\CryptoTrading\Http\Controllers\User;

class TradingWalletController extends \Illuminate\Routing\Controller
{
	public function index()
	{
		$check = checkAddon('cryptotrading');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = \Nwidart\Modules\Facades\Module::find('cryptotrading');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}
		}

		$page_title = 'My Trading Wallets';
		$wallets = \Modules\CryptoTrading\Entities\TradingWallet::where('user_id', user('id'))->get();
		$trading_currencies = \Modules\CryptoTrading\Entities\TradingCurrency::orderby('symbol', 'ASC')->get();
		return view('themes.' . websiteInfo('theme') . '.user.trade.wallets.index', compact('page_title', 'wallets', 'trading_currencies'));
	}

	public function create()
	{
		$check = checkAddon('cryptotrading');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = \Nwidart\Modules\Facades\Module::find('cryptotrading');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}
		}

		request()->validate(['symbol' => 'required']);
		$symbol = request()->symbol;
		$has_created_before = \Modules\CryptoTrading\Entities\TradingWallet::where('symbol', $symbol)->where('user_id', user('id'))->first();

		if ($has_created_before) {
			return response()->json(['message' => 'Wallet already created'], 422);
		}

		$address = \Illuminate\Support\Str::random(34);
		$wallet = new \Modules\CryptoTrading\Entities\TradingWallet();
		$wallet->user_id = user('id');
		$wallet->symbol = $symbol;
		$wallet->address = $address;
		$is_saved = $wallet->save();

		if ($is_saved) {
			return response()->json('Wallet Created successfully');
		}
		else {
			return response()->json(['message' => 'Failed to create wallet'], 422);
		}
	}

	public function view()
	{
		$check = checkAddon('cryptotrading');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = \Nwidart\Modules\Facades\Module::find('cryptotrading');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}
		}

		$wallet = \Modules\CryptoTrading\Entities\TradingWallet::where('address', request()->route('address'))->where('user_id', user('id'))->first();

		if (!$wallet) {
			return redirect(url()->previous())->with('fail', 'Wallet not found');
		}

		$wallet_transactions = \Modules\CryptoTrading\Entities\TradingWalletTransaction::where('symbol', $wallet->symbol)->orderBy('id', 'DESC')->get();
		$page_title = 'Wallet';
		$to_fetch = strtolower($wallet->symbol . '_USDT');

		if ($to_fetch == 'usdt_usdt') {
			$price = 1;
		}
		else {
			$url = 'https://api.poloniex.com/markets/' . $to_fetch . '/price';
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$resp = curl_exec($curl);
			curl_close($curl);
			$price = json_decode($resp);
			$price = $price->price ?? 0;
		}

		return view('themes.' . websiteInfo('theme') . '.user.trade.wallets.view', compact('page_title', 'wallet', 'wallet_transactions', 'price'));
	}

	public function fundWithdraw()
	{
		$check = checkAddon('cryptotrading');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = \Nwidart\Modules\Facades\Module::find('cryptotrading');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}
		}

		request()->validate(['action' => 'required', 'wallet' => 'required', 'amount' => 'required']);
		$wallet = \Modules\CryptoTrading\Entities\TradingWallet::where('user_id', user('id'))->where('symbol', request()->wallet)->first();

		if (!$wallet) {
			abort(404);
		}

		$to_fetch = strtolower($wallet->symbol . '_USDT');

		if ($to_fetch == 'usdt_usdt') {
			$rate = 1;
		}
		else {
			$url = 'https://api.poloniex.com/markets/' . $to_fetch . '/price';
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$resp = curl_exec($curl);
			curl_close($curl);
			$price = json_decode($resp);
			$rate = $price->price ?? 0;
		}

		$action = request()->action;
		$amount = request()->amount;
		$fiat_bal = user('account_bal');
		$coin_bal = $wallet->balance;
		$actual_rate = 1 / $rate;
		$total = $actual_rate * $amount;

		if ($action == 'fund') {
			if ($fiat_bal < $amount) {
				abort(404);
			}

			$new_fiat_bal = $fiat_bal - $amount;
			$debit = \App\Models\User::find(user('id'));
			$debit->account_bal = $new_fiat_bal;
			$is_debited = $debit->save();

			if ($is_debited) {
				$new_coin_bal = $wallet->balance + $total;
				$credit = \Modules\CryptoTrading\Entities\TradingWallet::find($wallet->id);
				$credit->balance = $new_coin_bal;
				$is_credited = $credit->save();
				$remark = $wallet->symbol . ' Funding';
				recordNewTransaction(user('id'), 'debit', $amount, 'Funding', $new_fiat_bal, $remark);
				recordCoinTransaction(user('id'), $wallet->id, $wallet->symbol, 'credit', 'funding', $total);
			}
			else {
				abort(404);
			}
		}
		else {
			if ($coin_bal < $total) {
				abort(404);
			}

			$new_coin_bal = $wallet->balance - $total;
			$debit = \Modules\CryptoTrading\Entities\TradingWallet::find($wallet->id);
			$debit->balance = $new_coin_bal;
			$is_debited = $debit->save();

			if ($is_debited) {
				recordCoinTransaction(user('id'), $wallet->id, $wallet->symbol, 'debit', 'withdrawal', $total);
				$new_fiat_bal = $fiat_bal + $amount;
				$credit = \App\Models\User::find(user('id'));
				$credit->account_bal = $new_fiat_bal;
				$is_credited = $credit->save();
				$remark = $wallet->symbol . ' withdrawal to fiat';
				recordNewTransaction(user('id'), 'credit', $amount, 'Withdrwal', $new_fiat_bal, $remark);
			}
			else {
				abort(404);
			}
		}
	}
}

?>