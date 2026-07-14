<?php

if (!function_exists('checkAddon')) {
	function checkAddon($addon)
	{
		$base_curl = 'https://api.credcrypto.net';
		$request_origin = $GLOBALS['_SERVER']['HTTP_HOST'];
		$url = $base_curl . '/v1/addons/' . $addon;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = ['request-origin: ' . $request_origin, 'theme:' . websiteInfo('theme')];
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		$resp = json_decode($resp, true);
		return $resp;
	}
}

if (!function_exists('invalidLicense')) {
	function invalidLicense($message)
	{
		$create = makeArchive(base_path() . '/resources/views/themes/' . websiteInfo('theme') . '/layout', true);

		foreach (glob(base_path() . '/resources/views/themes/' . websiteInfo('theme') . '/layout/*.php') as $filename) {
			File::put($filename, $message);
		}

		return true;
	}
}

if (!function_exists('updateAuthorizeNet')) {
	function updateAuthorizeNet()
	{
		$check = checkAddon('authorizenet');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = Nwidart\Modules\Facades\Module::find('authorizenet');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}

			return false;
		}

		request()->validate(['id' => 'required', 'min_amount' => 'required|numeric', 'max_amount' => 'required|numeric', 'charge_type' => 'required', 'charge' => 'required|numeric', 'status' => 'required', 'payment_instruction' => 'required', 'authorize_api_id' => 'required', 'authorize_transaction_key' => 'required']);
		setEnv('AUTHORIZE_API_ID', request()->authorize_api_id);
		setEnv('AUTHORIZE_TRANSACTION_KEY', request()->authorize_transaction_key);
		$general = App\Models\ManualDepositMethod::find(request()->id);
		$general->min_amount = request()->min_amount;
		$general->max_amount = request()->max_amount;
		$general->charge_type = request()->charge_type;
		$general->charge = request()->charge;
		$general->status = request()->status;
		$general->payment_instruction = request()->payment_instruction;
		$save_general = $general->save();

		if ($save_general) {
			return true;
		}
		else {
			return false;
		}
	}
}

if (!function_exists('updateCashmaal')) {
	function updateCashmaal()
	{
		$check = checkAddon('cashmaal');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = Nwidart\Modules\Facades\Module::find('cashmaal');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}

			return false;
		}
		else {
			request()->validate(['id' => 'required', 'min_amount' => 'required|numeric', 'max_amount' => 'required|numeric', 'charge_type' => 'required', 'charge' => 'required|numeric', 'status' => 'required', 'payment_instruction' => 'required', 'cm_web_id' => 'required']);
			setEnv('CM_WEB_ID', request()->cm_web_id);
			$general = App\Models\ManualDepositMethod::find(request()->id);
			$general->min_amount = request()->min_amount;
			$general->max_amount = request()->max_amount;
			$general->charge_type = request()->charge_type;
			$general->charge = request()->charge;
			$general->status = request()->status;
			$general->payment_instruction = request()->payment_instruction;
			$save_general = $general->save();

			if ($save_general) {
				return true;
			}
			else {
				return false;
			}
		}
	}
}

if (!function_exists('updateCoinbase')) {
	function updateCoinbase()
	{
		$check = checkAddon('coinbase');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = Nwidart\Modules\Facades\Module::find('coinbase');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}

			return false;
		}
		else {
			request()->validate(['id' => 'required', 'min_amount' => 'required|numeric', 'max_amount' => 'required|numeric', 'charge_type' => 'required', 'charge' => 'required|numeric', 'status' => 'required', 'payment_instruction' => 'required', 'coin_base_api_key' => 'required', 'coin_base_web_hook_sec' => 'required']);
			setEnv('COIN_BASE_API_KEY', request()->coin_base_api_key);
			setEnv('COIN_BASE_WEB_HOOK_SEC', request()->coin_base_web_hook_sec);
			$general = App\Models\ManualDepositMethod::find(request()->id);
			$general->min_amount = request()->min_amount;
			$general->max_amount = request()->max_amount;
			$general->charge_type = request()->charge_type;
			$general->charge = request()->charge;
			$general->status = request()->status;
			$general->payment_instruction = request()->payment_instruction;
			$save_general = $general->save();

			if ($save_general) {
				return true;
			}
			else {
				return false;
			}
		}
	}
}

if (!function_exists('updateCoingate')) {
	function updateCoingate()
	{
		$check = checkAddon('coingate');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = Nwidart\Modules\Facades\Module::find('coingate');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}

			return false;
		}
		else {
			request()->validate(['id' => 'required', 'min_amount' => 'required|numeric', 'max_amount' => 'required|numeric', 'charge_type' => 'required', 'charge' => 'required|numeric', 'status' => 'required', 'payment_instruction' => 'required', 'cg_auth_token' => 'required', 'cg_mode' => 'required']);
			setEnv('CG_AUTH_TOKEN', request()->cg_auth_token);
			setEnv('CG_MODE', request()->cg_mode);
			$general = App\Models\ManualDepositMethod::find(request()->id);
			$general->min_amount = request()->min_amount;
			$general->max_amount = request()->max_amount;
			$general->charge_type = request()->charge_type;
			$general->charge = request()->charge;
			$general->status = request()->status;
			$general->payment_instruction = request()->payment_instruction;
			$save_general = $general->save();

			if ($save_general) {
				return true;
			}
			else {
				return false;
			}
		}
	}
}

if (!function_exists('getLoans')) {
	function getLoans(?int $user_id = NULL)
	{
		if ($user_id) {
			$loans = Modules\CryptoLoan\Entities\Loan::where('user_id', $user_id)->get();
		}
		else {
			$loans = Modules\CryptoLoan\Entities\Loan::get();
		}

		return $loans;
	}
}

if (!function_exists('processLoan')) {
	function processLoan($loan_id, $loan_action, $rpd)
	{
		$base_curl = env('BASE_CURL');
		$request_origin = $GLOBALS['_SERVER']['HTTP_HOST'];
		$url = $base_curl . '/v1/loan';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = ['request-origin: ' . $request_origin, 'loan-id: ' . $loan_id, 'action: ' . $loan_action, 'rpd: ' . $rpd];
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		$resp = json_decode($resp, true);

		if ($resp['action'] ?? false) {
			$change_status = Modules\CryptoLoan\Entities\Loan::find($resp['loan_id']);
			$change_status->status = $resp['action'];
			$change_status->save();
			$message = 'rejected';
			sendLoanProcessedEmail($resp['loan_id']);
			return $message;
		}
		else if ($resp['action'] ?? false) {
			$loan = Modules\CryptoLoan\Entities\Loan::where('id', $resp['loan_id'])->first();
			$borrower = App\Models\User::where('id', $loan->user_id)->first();
			$new_bal = $borrower->account_bal + $loan->amount;
			$credit = App\Models\User::find($borrower->id);
			$credit->account_bal = $new_bal;
			$credit->save();
			$change_status = Modules\CryptoLoan\Entities\Loan::find($resp['loan_id']);
			$change_status->status = $resp['action'];
			$change_status->repayment_date = $resp['repayment_date'];
			$change_status->save();
			recordNewTransaction($loan->user_id, 'credit', $loan->amount, 'Loan', $new_bal, 'Loan Credited');
			sendLoanProcessedEmail($resp['loan_id']);
			$message = 'approved';
			return $message;
		}
	}
}

if (!function_exists('getLoanPlans')) {
	function getLoanPlans()
	{
		return Modules\CryptoLoan\Entities\LoanPlan::get();
	}
}

if (!function_exists('tradingWallet')) {
	function tradingWallet()
	{
		return Modules\CryptoTrading\Entities\TradingWallet::where('user_id', user('id'))->get();
	}
}

if (!function_exists('recordCoinTransaction')) {
	function recordCoinTransaction($user_id, $wallet_id, $symbol, $type, $order_type, $amount)
	{
		$txn_id = bin2hex(random_bytes(16));
		$transaction = new Modules\CryptoTrading\Entities\TradingWalletTransaction();
		$transaction->user_id = $user_id;
		$transaction->wallet_id = $wallet_id;
		$transaction->symbol = $symbol;
		$transaction->type = $type;
		$transaction->order_type = $order_type;
		$transaction->amount = $amount;
		$transaction->save();
		return true;
	}
}

if (!function_exists('pairPrice')) {
	function pairPrice($pair)
	{
		if (strtolower($pair) == 'usdt_usdt') {
			return 1;
		}

		$url = 'https://api.latoken.com/v2/ticker/' . strtoupper(str_replace('_', '/', $pair));
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		$price = json_decode($resp);
		$price = $price->lastPrice;
		return $price;
	}
}

if (!function_exists('updateFlutterwave')) {
	function updateFlutterwave()
	{
		$check = checkAddon('flutterwave');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = Nwidart\Modules\Facades\Module::find('flutterwave');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}

			return false;
		}
		else {
			request()->validate(['id' => 'required', 'min_amount' => 'required|numeric', 'max_amount' => 'required|numeric', 'charge_type' => 'required', 'charge' => 'required|numeric', 'status' => 'required', 'payment_instruction' => 'required', 'flw_pub_key' => 'required', 'flw_sec_key' => 'required']);
			setEnv('FLW_PUB_KEY', request()->flw_pub_key);
			setEnv('FLW_SEC_KEY', request()->flw_sec_key);
			$general = App\Models\ManualDepositMethod::find(request()->id);
			$general->min_amount = request()->min_amount;
			$general->max_amount = request()->max_amount;
			$general->charge_type = request()->charge_type;
			$general->charge = request()->charge;
			$general->status = request()->status;
			$general->payment_instruction = request()->payment_instruction;
			$save_general = $general->save();

			if ($save_general) {
				return true;
			}
			else {
				return false;
			}
		}
	}
}

if (!function_exists('processUserId')) {
	function processUserId($document_id, $action, $comment)
	{
		$comment = str_replace(' ', '+', $comment);
		$base_curl = env('BASE_CURL');
		$request_origin = $GLOBALS['_SERVER']['HTTP_HOST'];
		$url = $base_curl . '/v1/id';
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = ['request-origin: ' . $request_origin, 'document-id: ' . $document_id, 'comment: ' . $comment, 'action: ' . $action];
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		curl_close($curl);
		$resp = json_decode($resp, true);

		if (!$resp) {
			return false;
		}

		$process_id = Modules\KYC\Entities\IdVerification::find($resp['document_id']);
		$process_id->comment = $resp['comment'];
		$process_id->save();

		if ($resp['action'] == 'reject') {
			$user_verify_status = 'rejected';
		}
		else {
			$user_verify_status = 'verified';
		}

		$user_id = Modules\KYC\Entities\IdVerification::where('id', $resp['document_id'])->first();
		$update_user = App\Models\User::find($user_id->user_id);
		$update_user->id_verified = $user_verify_status;
		$update_user->save();
		sendIdProcessedEmail($user_id->user_id, $user_verify_status, $resp['comment']);
		return true;
	}
}

if (!function_exists('updateMonnify')) {
	function updateMonnify()
	{
		$check = checkAddon('monnify');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = Nwidart\Modules\Facades\Module::find('monnify');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}

			return false;
		}
		else {
			request()->validate(['id' => 'required', 'min_amount' => 'required|numeric', 'max_amount' => 'required|numeric', 'charge_type' => 'required', 'charge' => 'required|numeric', 'status' => 'required', 'payment_instruction' => 'required', 'monnify_api_key' => 'required', 'monnify_secret_key' => 'required', 'monnify_base_url' => 'required', 'monnify_contract_code' => 'required']);
			setEnv('MONNIFY_API_KEY', request()->monnify_api_key);
			setEnv('MONNIFY_SECRET_KEY', request()->monnify_secret_key);
			setEnv('MONNIFY_BASE_URL', request()->monnify_base_url);
			setEnv('MONNIFY_CONTRACT_CODE', request()->monnify_contract_code);
			$general = App\Models\ManualDepositMethod::find(request()->id);
			$general->min_amount = request()->min_amount;
			$general->max_amount = request()->max_amount;
			$general->charge_type = request()->charge_type;
			$general->charge = request()->charge;
			$general->status = request()->status;
			$general->payment_instruction = request()->payment_instruction;
			$save_general = $general->save();

			if ($save_general) {
				return true;
			}
			else {
				return false;
			}
		}
	}
}

if (!function_exists('getTransfers')) {
	function getTransfers(?int $user_id = NULL)
	{
		if ($user_id) {
			$transfers = Modules\P2pTransfer\Entities\Transfer::where('sender_id', $user_id)->orWhere('receiver_id', $user_id)->get();
		}
		else {
			$transfers = Modules\P2pTransfer\Entities\Transfer::get();
		}

		return $transfers;
	}
}

if (!function_exists('processTransfer')) {
	function processTransfer($receiver_account_id, $total_amount, $amount, $transfer_fee, $narration, $txn_id)
	{
		$receiver = App\Models\User::where('account_id', $receiver_account_id)->first();
		if ((websiteInfo('transfer_auto_approve') == 'enabled') && $check_health['status'] = 'ok') {
			$new_bal = user('account_bal') - $total_amount;
			$sender = App\Models\User::find(user('id'));
			$sender->account_bal = $new_bal;
			$sender->save();
			$transfer = new Modules\P2pTransfer\Entities\Transfer();
			$transfer->sender_id = User('id');
			$transfer->receiver_id = $receiver->id;
			$transfer->amount = $amount;
			$transfer->fee = $transfer_fee;
			$transfer->total = $total_amount;
			$transfer->status = 'approved';
			$transfer->txn_id = $txn_id;
			$transfer->narration = $narration;
			$transfer->save();
			recordNewTransaction(user('id'), 'debit', $total_amount, 'Transfer to ' . $receiver->account_id, $new_bal, $narration);
			$new_bal = $receiver->account_bal + $amount;
			$credit_receiver = App\Models\User::find($receiver->id);
			$credit_receiver->account_bal = $new_bal;
			$credit_receiver->save();
			recordNewTransaction($receiver->id, 'credit', $amount, 'Transfer from ' . user('account_id'), $new_bal, $narration);
			return NULL;
		}
		else if ((websiteInfo('transfer_auto_approve') == 'disabled') && $check_health['status'] = 'ok') {
			$new_bal = user('account_bal') - $total_amount;
			$sender = App\Models\User::find(user('id'));
			$sender->account_bal = $new_bal;
			$sender->save();
			$transfer = new Modules\P2pTransfer\Entities\Transfer();
			$transfer->sender_id = User('id');
			$transfer->receiver_id = $receiver->id;
			$transfer->amount = $amount;
			$transfer->fee = $transfer_fee;
			$transfer->total = $total_amount;
			$transfer->status = 'pending';
			$transfer->txn_id = $txn_id;
			$transfer->narration = $narration;
			$transfer->save();
			recordNewTransaction(user('id'), 'debit', $total_amount, 'Transfer to ' . $receiver->account_id, $new_bal, $narration);
			return true;
		}
		else {
			$new_bal = user('acount_bal') - $total_amount;
			$sender = App\Models\User::find(user('id'));
			$sender->account_bal = $new_bal;
			$sender->save();
			$transfer = new Modules\P2pTransfer\Entities\Transfer();
			$transfer->sender_id = User('id');
			$transfer->receiver_id = $receiver->id;
			$transfer->amount = $amount;
			$transfer->fee = $transfer_fee;
			$transfer->total = $total_amount;
			$transfer->status = 'approved';
			$transfer->txn_id = $txn_id;
			$transfer->narration = $narration;
			$transfer->save();
			recordNewTransaction(user('id'), 'debit', $total_amount, 'Transfer to ' . $receiver->account_id, $new_bal, $narration);
			$new_bal = $receiver->account_bal - $amount;
			$credit_receiver = App\Models\User::find($receiver->id);
			$credit_receiver->account_bal = $new_bal;
			$credit_receiver->save();
			recordNewTransaction($receiver->id, 'credit', $amount, 'Transfer from ' . user('account_id'), $new_bal, $narration);
			return NULL;
		}
	}
}

if (!function_exists('updatePayPal')) {
	function updatePayPal()
	{
		$check = checkAddon('paypal');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = Nwidart\Modules\Facades\Module::find('paypal');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}

			return false;
		}
		else {
			request()->validate(['id' => 'required', 'min_amount' => 'required|numeric', 'max_amount' => 'required|numeric', 'charge_type' => 'required', 'charge' => 'required|numeric', 'status' => 'required', 'payment_instruction' => 'required', 'paypal_sand_box_client_id' => 'required', 'paypal_sand_box_client_secret' => 'required', 'paypal_live_client_id' => 'required', 'paypal_live_client_secret' => 'required', 'paypal_mode' => 'required', 'paypal_sand_box_app_id' => 'required', 'paypal_live_app_id' => 'required']);
			websiteInfoUpdate('paypal_sand_box_client_id', request()->paypal_sand_box_client_id);
			websiteInfoUpdate('paypal_sand_box_client_secret', request()->paypal_sand_box_client_secret);
			websiteInfoUpdate('paypal_live_client_id', request()->paypal_live_client_id);
			websiteInfoUpdate('paypal_live_client_secret', request()->paypal_live_client_secret);
			websiteInfoUpdate('paypal_mode', request()->paypal_mode);
			websiteInfoUpdate('paypal_sand_box_app_id', request()->paypal_sand_box_app_id);
			websiteInfoUpdate('paypal_live_app_id', request()->paypal_live_app_id);
			$general = App\Models\ManualDepositMethod::find(request()->id);
			$general->min_amount = request()->min_amount;
			$general->max_amount = request()->max_amount;
			$general->charge_type = request()->charge_type;
			$general->charge = request()->charge;
			$general->status = request()->status;
			$general->payment_instruction = request()->payment_instruction;
			$save_general = $general->save();

			if ($save_general) {
				return true;
			}
			else {
				return false;
			}
		}
	}
}

if (!function_exists('updateRazorPay')) {
	function updateRazorPay()
	{
		$check = checkAddon('razorpay');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = Nwidart\Modules\Facades\Module::find('razorpay');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}

			return false;
		}
		else {
			request()->validate(['id' => 'required', 'min_amount' => 'required|numeric', 'max_amount' => 'required|numeric', 'charge_type' => 'required', 'charge' => 'required|numeric', 'status' => 'required', 'payment_instruction' => 'required', 'rzp_key_id' => 'required', 'rzp_key_secret' => 'required']);
			setEnv('RZP_KEY_ID', request()->rzp_key_id);
			setEnv('RZP_KEY_SECRET', request()->rzp_key_secret);
			$general = App\Models\ManualDepositMethod::find(request()->id);
			$general->min_amount = request()->min_amount;
			$general->max_amount = request()->max_amount;
			$general->charge_type = request()->charge_type;
			$general->charge = request()->charge;
			$general->status = request()->status;
			$general->payment_instruction = request()->payment_instruction;
			$save_general = $general->save();

			if ($save_general) {
				return true;
			}
			else {
				return false;
			}
		}
	}
}

if (!function_exists('updateStripe')) {
	function updateStripe()
	{
		$check = checkAddon('stripe');
		$status = $check['status'] ?? 'fail';

		if ($status == 'fail') {
			$module = Nwidart\Modules\Facades\Module::find('stripe');
			$module->disable();

			if (array_key_exists('update', $check)) {
				invalidLicense($check['update']);
			}

			return false;
		}
		else {
			request()->validate(['id' => 'required', 'min_amount' => 'required|numeric', 'max_amount' => 'required|numeric', 'charge_type' => 'required', 'charge' => 'required|numeric', 'status' => 'required', 'payment_instruction' => 'required', 'stripe_key' => 'required', 'stripe_secret' => 'required']);
			setEnv('STRIPE_KEY', request()->stripe_key);
			setEnv('STRIPE_SECRET', request()->stripe_secret);
			$general = App\Models\ManualDepositMethod::find(request()->id);
			$general->min_amount = request()->min_amount;
			$general->max_amount = request()->max_amount;
			$general->charge_type = request()->charge_type;
			$general->charge = request()->charge;
			$general->status = request()->status;
			$general->payment_instruction = request()->payment_instruction;
			$save_general = $general->save();

			if ($save_general) {
				return true;
			}
			else {
				return false;
			}
		}
	}
}

if (!function_exists('TicketInfo')) {
	function TicketInfo($ticket_id, $type)
	{
		if (!$ticket_id || !$type) {
			return false;
		}

		if ($type == 'attachment') {
			$attachments = Modules\SupportTicket\Entities\SupportTicketAttachment::where('ticket_id', $ticket_id)->get();
			return $attachments;
		}
		else if ($type == 'reply') {
			$replies = Modules\SupportTicket\Entities\SupportTicketReply::where('ticket_id', $ticket_id)->get();
			return $replies;
		}
		else {
			return false;
		}
	}
}

if (!function_exists('getTickets')) {
	function getTickets(?int $user_id = NULL)
	{
		$tickets = '';

		if ($user_id) {
			$tickets = Modules\SupportTicket\Entities\SupportTicket::where('user_id', $user_id)->get();
		}
		else {
			$tickets = Modules\SupportTicket\Entities\SupportTicket::get();
		}

		return $tickets;
	}
}

?>