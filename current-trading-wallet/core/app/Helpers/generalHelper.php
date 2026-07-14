<?php

use App\Models\WebsiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\ManualDepositMethod;
use App\Models\Loan;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\CryptoTrading\Entities\TradingWallet;
use Nwidart\Modules\Facades\Module;

//define custom root path
if (!function_exists('root_path')) {
    function root_path()
    {
        $root_path = str_replace('core', '', base_path());
        return $root_path;
        //note: this would return with a trailing '\' on local system or '/' on  a live server
    }
}

//get website setting value by name

if (!function_exists('websiteInfo')) {
    function websiteInfo($name)
    {
        if (!$name) {
            return NULL;
        }

        //for cached config
        if (config('tradingwallet.' . $name)) {
            return config('tradingwallet.' . $name);
        }

        // for livechat js only
        if ($name == 'livechat_script') {
            return (file_get_contents(resource_path('store/livechat.store')));
        }

        // for custom js
        if ($name == 'custom_js') {
            return (file_get_contents(resource_path('store/customjs.store')));
        }

        // for custom css
        if ($name == 'custom_css') {
            return (file_get_contents(resource_path('store/customcss.store')));
        }

        //else query the database
        $website_info = WebsiteSetting::where('name', $name)->first();
        if (!$website_info) {
            return NULL;
        }
        return $website_info->value;
    }
}

//update website setting value by name
if (!function_exists('websiteInfoUpdate')) {
    function websiteInfoUpdate($name, $value)
    {
        if (!$name) {
            return NULL;
        }

        $allowed_config_keys = [
            'trader_mode',
            'kyc_mode',
            'loan_mode',
            'referral_mode',
            'livechat',
            'tawkto',
            'whatsapp',
            'custom_js',
            'custom_css',
            'maintenance_mode',
            'theme',
            'website_name',
            'meta',
            'logo',
            'banner',
            'favicon',
        ];

        if (config('tradingwallet.' . $name) && in_array($name, $allowed_config_keys)) {
            $path = base_path() . '/config/tradingwallet.php';
            file_put_contents($path, str_replace(
                "'" . $name . "' => '" . config('tradingwallet.' . $name) . "'",
                "'" . $name . "' => '" . $value . "'",
                file_get_contents($path)
            )
            );
        } elseif (in_array($name, $allowed_config_keys)) {
            $path = base_path() . '/config/tradingwallet.php';
            $old_content = file_get_contents($path);
            $new_content = str_replace('];', '', $old_content);
            $insert = "    '" . $name . "' => '" . $value . "',";
            $new_content .= PHP_EOL . $insert . PHP_EOL . '];';
            file_put_contents($path, $new_content);
        }

        $website_info = WebsiteSetting::where('name', $name)->first();
        if (!$website_info) {
            $new_config = new WebsiteSetting();
            $new_config->name = $name;
            $new_config->value = $value;
            $new_config->save();
        } else {
            $update = WebsiteSetting::find($website_info->id);
            $update->value = $value;
            $update->save();
        }


        Artisan::call('optimize:clear');
        return true;
    }
}

//get payment method currency by type

if (!function_exists('paymentCurreny')) {
    function paymentCurrency($type)
    {
        if (!$type) {
            return NULL;
        }

        $method = ManualDepositMethod::where('type', $type)->first();
        return $method->currency;
    }
}

//retrieve all countries
if (!function_exists('countryList')) {
    function countryList()
    {
        return [
            'countries' => [
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'United Kingdom', 'code' => 'GB'],
            ['name' => 'Canada', 'code' => 'CA'],
            ['name' => 'Australia', 'code' => 'AU'],
            ['name' => 'Germany', 'code' => 'DE'],
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'Italy', 'code' => 'IT'],
            ['name' => 'Spain', 'code' => 'ES'],
            ['name' => 'Netherlands', 'code' => 'NL'],
            ['name' => 'Belgium', 'code' => 'BE'],
            ['name' => 'Switzerland', 'code' => 'CH'],
            ['name' => 'Austria', 'code' => 'AT'],
            ['name' => 'Sweden', 'code' => 'SE'],
            ['name' => 'Norway', 'code' => 'NO'],
            ['name' => 'Denmark', 'code' => 'DK'],
            ['name' => 'Finland', 'code' => 'FI'],
            ['name' => 'Poland', 'code' => 'PL'],
            ['name' => 'Portugal', 'code' => 'PT'],
            ['name' => 'Ireland', 'code' => 'IE'],
            ['name' => 'Greece', 'code' => 'GR'],
            ['name' => 'Czech Republic', 'code' => 'CZ'],
            ['name' => 'Hungary', 'code' => 'HU'],
            ['name' => 'Romania', 'code' => 'RO'],
            ['name' => 'Bulgaria', 'code' => 'BG'],
            ['name' => 'Croatia', 'code' => 'HR'],
            ['name' => 'Slovakia', 'code' => 'SK'],
            ['name' => 'Slovenia', 'code' => 'SI'],
            ['name' => 'Lithuania', 'code' => 'LT'],
            ['name' => 'Latvia', 'code' => 'LV'],
            ['name' => 'Estonia', 'code' => 'EE'],
            ['name' => 'Russia', 'code' => 'RU'],
            ['name' => 'Ukraine', 'code' => 'UA'],
            ['name' => 'Turkey', 'code' => 'TR'],
            ['name' => 'India', 'code' => 'IN'],
            ['name' => 'Pakistan', 'code' => 'PK'],
            ['name' => 'Bangladesh', 'code' => 'BD'],
            ['name' => 'Sri Lanka', 'code' => 'LK'],
            ['name' => 'Nepal', 'code' => 'NP'],
            ['name' => 'China', 'code' => 'CN'],
            ['name' => 'Japan', 'code' => 'JP'],
            ['name' => 'South Korea', 'code' => 'KR'],
            ['name' => 'Singapore', 'code' => 'SG'],
            ['name' => 'Malaysia', 'code' => 'MY'],
            ['name' => 'Thailand', 'code' => 'TH'],
            ['name' => 'Vietnam', 'code' => 'VN'],
            ['name' => 'Philippines', 'code' => 'PH'],
            ['name' => 'Indonesia', 'code' => 'ID'],
            ['name' => 'Hong Kong', 'code' => 'HK'],
            ['name' => 'Taiwan', 'code' => 'TW'],
            ['name' => 'Israel', 'code' => 'IL'],
            ['name' => 'United Arab Emirates', 'code' => 'AE'],
            ['name' => 'Saudi Arabia', 'code' => 'SA'],
            ['name' => 'Qatar', 'code' => 'QA'],
            ['name' => 'Kuwait', 'code' => 'KW'],
            ['name' => 'Bahrain', 'code' => 'BH'],
            ['name' => 'Oman', 'code' => 'OM'],
            ['name' => 'Jordan', 'code' => 'JO'],
            ['name' => 'Lebanon', 'code' => 'LB'],
            ['name' => 'Egypt', 'code' => 'EG'],
            ['name' => 'Morocco', 'code' => 'MA'],
            ['name' => 'Nigeria', 'code' => 'NG'],
            ['name' => 'Ghana', 'code' => 'GH'],
            ['name' => 'Kenya', 'code' => 'KE'],
            ['name' => 'South Africa', 'code' => 'ZA'],
            ['name' => 'Ethiopia', 'code' => 'ET'],
            ['name' => 'Tanzania', 'code' => 'TZ'],
            ['name' => 'Uganda', 'code' => 'UG'],
            ['name' => 'Cameroon', 'code' => 'CM'],
            ['name' => 'Ivory Coast', 'code' => 'CI'],
            ['name' => 'Senegal', 'code' => 'SN'],
            ['name' => 'Zimbabwe', 'code' => 'ZW'],
            ['name' => 'Botswana', 'code' => 'BW'],
            ['name' => 'Namibia', 'code' => 'NA'],
            ['name' => 'Zambia', 'code' => 'ZM'],
            ['name' => 'Rwanda', 'code' => 'RW'],
            ['name' => 'Brazil', 'code' => 'BR'],
            ['name' => 'Argentina', 'code' => 'AR'],
            ['name' => 'Mexico', 'code' => 'MX'],
            ['name' => 'Colombia', 'code' => 'CO'],
            ['name' => 'Chile', 'code' => 'CL'],
            ['name' => 'Peru', 'code' => 'PE'],
            ['name' => 'Venezuela', 'code' => 'VE'],
            ['name' => 'Ecuador', 'code' => 'EC'],
            ['name' => 'Bolivia', 'code' => 'BO'],
            ['name' => 'Paraguay', 'code' => 'PY'],
            ['name' => 'Uruguay', 'code' => 'UY'],
            ['name' => 'Costa Rica', 'code' => 'CR'],
            ['name' => 'Panama', 'code' => 'PA'],
            ['name' => 'Dominican Republic', 'code' => 'DO'],
            ['name' => 'Guatemala', 'code' => 'GT'],
            ['name' => 'Honduras', 'code' => 'HN'],
            ['name' => 'El Salvador', 'code' => 'SV'],
            ['name' => 'Nicaragua', 'code' => 'NI'],
            ['name' => 'Jamaica', 'code' => 'JM'],
            ['name' => 'Trinidad and Tobago', 'code' => 'TT'],
            ['name' => 'Barbados', 'code' => 'BB'],
            ['name' => 'Bahamas', 'code' => 'BS'],
            ['name' => 'New Zealand', 'code' => 'NZ'],
            ['name' => 'Fiji', 'code' => 'FJ'],
            ]
        ];
    }
}


//format currency and amount

if (!function_exists('formatAmount')) {
    function formatAmount($amount)
    {
        $formatted = websiteInfo('general_currency') . number_format($amount, websiteInfo('decimal_places'));
        return $formatted;
    }
}




//convert currency
if (!function_exists('currencyConverter')) {
    function currencyConverter($ac, $mc, $amount)
    {
        $rates = [
            'USD' => 1,
            'EUR' => 0.92,
            'GBP' => 0.79,
            'NGN' => 1600,
            'CAD' => 1.36,
            'AUD' => 1.52,
            'INR' => 83,
            'PKR' => 278,
            'BRL' => 5.05,
            'ZAR' => 18.5,
            'KES' => 129,
            'GHS' => 15,
            'AED' => 3.67,
            'SAR' => 3.75,
            'MYR' => 4.7,
            'SGD' => 1.35,
            'JPY' => 150,
            'CNY' => 7.25,
            'KRW' => 1370,
            'TRY' => 32,
            'RUB' => 92,
            'UAH' => 39,
            'EGP' => 48,
            'MAD' => 9.9,
            'PHP' => 56,
            'IDR' => 15800,
            'VND' => 25000,
            'THB' => 36,
            'BDT' => 110,
            'LKR' => 300,
            'NPR' => 133,
            'TZS' => 2550,
            'UGX' => 3800,
            'XOF' => 600,
            'ETB' => 58,
            'CMX' => 600,
            'ZMW' => 26,
            'NAD' => 18.5,
            'BWP' => 13.5,
            'RWF' => 1300,
        ];

        $ac_rate = $rates[strtoupper($ac)] ?? 1;
        $mc_rate = $rates[strtoupper($mc)] ?? 1;
        $converted_amount = ($amount / $ac_rate) * $mc_rate;

        return [
            'amount' => round($converted_amount, 8),
            'currency' => $mc
        ];
    }
}


//image uploader

if (!function_exists('uploadImage')) {
    function uploadImage($image, $path)
    {
        $name = $image->hashName();
        $upload_image = $image->storeAs('public/' . $path . '/', $name);
        return $name;
    }
}

if (!function_exists('uploadImagePublic')) {
    function uploadImagePublic($file, $file_name)
    {
        $path = root_path() . 'public/assets/imgs/';
        $file->move($path, $file_name);
        return $file_name;
    }
}

//get currencies
if (!function_exists('getCurrency')) {
    function getCurrency()
    {
        $codes = ['USD', 'EUR', 'GBP', 'NGN', 'CAD', 'AUD', 'INR', 'PKR', 'BRL', 'ZAR', 'KES', 'GHS', 'AED', 'SAR', 'MYR', 'SGD', 'JPY', 'CNY', 'KRW', 'TRY', 'RUB', 'UAH', 'EGP', 'MAD', 'PHP', 'IDR', 'VND', 'THB', 'BDT', 'LKR', 'NPR', 'TZS', 'UGX', 'XOF', 'ETB', 'ZMW', 'NAD', 'BWP', 'RWF'];
        $names = [
            'USD' => 'US Dollar', 'EUR' => 'Euro', 'GBP' => 'British Pound', 'NGN' => 'Nigerian Naira',
            'CAD' => 'Canadian Dollar', 'AUD' => 'Australian Dollar', 'INR' => 'Indian Rupee', 'PKR' => 'Pakistani Rupee',
            'BRL' => 'Brazilian Real', 'ZAR' => 'South African Rand', 'KES' => 'Kenyan Shilling', 'GHS' => 'Ghanaian Cedi',
            'AED' => 'UAE Dirham', 'SAR' => 'Saudi Riyal', 'MYR' => 'Malaysian Ringgit', 'SGD' => 'Singapore Dollar',
            'JPY' => 'Japanese Yen', 'CNY' => 'Chinese Yuan', 'KRW' => 'Korean Won', 'TRY' => 'Turkish Lira',
            'RUB' => 'Russian Ruble', 'UAH' => 'Ukrainian Hryvnia', 'EGP' => 'Egyptian Pound', 'MAD' => 'Moroccan Dirham',
            'PHP' => 'Philippine Peso', 'IDR' => 'Indonesian Rupiah', 'VND' => 'Vietnamese Dong', 'THB' => 'Thai Baht',
            'BDT' => 'Bangladeshi Taka', 'LKR' => 'Sri Lankan Rupee', 'NPR' => 'Nepalese Rupee', 'TZS' => 'Tanzanian Shilling',
            'UGX' => 'Ugandan Shilling', 'XOF' => 'West African CFA Franc', 'ETB' => 'Ethiopian Birr', 'ZMW' => 'Zambian Kwacha',
            'NAD' => 'Namibian Dollar', 'BWP' => 'Botswana Pula', 'RWF' => 'Rwandan Franc',
        ];
        return array_map(function ($code) use ($names) {
            return ['currency_code' => $code, 'currency_name' => $names[$code] ?? $code];
        }, $codes);
    }
}

//processDeposit($request->id, $request->user_id, $request->action, $request->additional_info);
//process deposit
if (!function_exists('processDeposit')) {
    function processDeposit($id, $user_id, $action, $additional_info)
    {
        if ($action == 'reject') {
            //change status 
            $deposit = Deposit::find($id);
            $deposit->status = 'rejected';
            $deposit->additional_info = $additional_info;
            $update_deposit = $deposit->save();

            if ($update_deposit) {
                //send notification email
                sendDepositRejectedEmail($id);
            }
        } elseif ($action == 'approve') {
            //get deposit information
            $deposit = Deposit::where('id', $id)->first();

            //credit user
            $credit_amount = $deposit->amount;
            $new_bal = ($deposit->amount + adminUser($user_id, 'account_bal'));

            //credit the wallet address
            $currencies = [
                'btc',
                'eth',
                'usdt',
                'usdc',
                'bnb',
                'xrp',
                'ada',
                'ltc',
                'trx',
                'xmr',
            ];

            $currency = $deposit->currency;
            if (in_array($currency, $currencies) & isAddonEnabled('cryptotrading')) {
                //check if the user has the wallet
                $wallet = TradingWallet::where('user_id', $deposit->user_id)
                    ->where('symbol', $currency)
                    ->first();

                if (!$wallet) {
                    //create wallet
                    $create = new TradingWallet();
                    $create->symbol = $currency;
                    $create->user_id = $deposit->user_id;
                    $create->balance = 0;
                    $create->address = Str::random(32);
                    $create->save();

                    $wallet = $create;
                }

                //credit the wallet
                $credit = TradingWallet::find($wallet->id);
                $credit->balance = $wallet->balance + $deposit->converted_amount;
                $save_credit = $credit->save();

            } else {
                $credit = User::find($user_id);
                $credit->account_bal = $new_bal;
                $save_credit = $credit->save();
            }



            //change deposit status
            $update_deposit = Deposit::find($id);
            $update_deposit->status = 'approved';
            $update_deposit->additional_info = $additional_info;
            $update_deposit->save();

            //record transaction                             
            recordNewTransaction($user_id, 'credit', $credit_amount, 'Deposit', $new_bal, $deposit->method . ' Deposit');

            //credit referrer
            if (websiteInfo('ref_bonus') > 0) {
                $referrer = User::where('account_id', adminUser($user_id, 'referred_by'))->first();

                if ($referrer) {
                    //calculate bonus
                    $credit_amount = (websiteInfo('ref_bonus') / 100 * $deposit->amount);
                    $credit_bonus = User::find($referrer->id);
                    $credit_bonus->account_bal = ($referrer->account_bal + $credit_amount);
                    $credit_bonus->save();

                    //record transaction
                    $new_bal = ($referrer->account_bal + $credit_amount);
                    recordNewTransaction($referrer->id, 'credit', $credit_amount, 'Bonus', $new_bal, 'Referal Bonus');
                }
            }

            //send email notification
            sendDepositApprovedEmail($id);
        }
    }
}



//record transction
if (!function_exists('recordNewTransaction')) {
    function recordNewTransaction($user_id, $type, $amount, $method, $new_bal, $remark)
    {
        //generate transaction id 
        $txn_id = bin2hex(random_bytes(16));

        $save_transaction = new Transaction();
        $save_transaction->user_id = $user_id;
        $save_transaction->type = $type;
        $save_transaction->amount = $amount;
        $save_transaction->balance_after_transaction = $new_bal;
        $save_transaction->method = $method;
        $save_transaction->txn_id = $txn_id;
        $save_transaction->remark = $remark;
        $save_new_transaction = $save_transaction->save();

        sendTransactionNotification($user_id, $type, $amount, $new_bal, $method, $remark, $txn_id);
        return true;
    }
}



//retrieve blogs
if (!function_exists('fetchBlogs')) {
    function fetchBlogs()
    {
        if (class_exists('\\App\\Models\\Blog')) {
            return \App\Models\Blog::orderBy('created_at', 'desc')->get()->toArray();
        }
        return [];
    }
}

//processWithdrawal($withdrawal, $action, $additional_info);
//process Withdrawal
if (!function_exists('processWithdrawal')) {
    function processWithdrawal($withdrawal, $action, $additional_info)
    {
        if ($action == 'reject') {
            $status = 'rejected';
        } else {
            $status = 'approved';
        }
        $process_withdrawal = Withdrawal::find($withdrawal->id);
        $process_withdrawal->status = $status;
        $process_withdrawal->save();

        if ($status != 'approved') {
            //credit the user back
            $old_bal = adminUser($withdrawal->user_id, 'account_bal');
            $new_bal = $old_bal + $withdrawal->total;
            $credit = User::find($withdrawal->user_id);
            $credit->account_bal = $new_bal;
            $credit->save();

            //recored new transaction
            recordNewTransaction($withdrawal->user_id, 'credit', $withdrawal->total, 'Refund', $new_bal, 'Withdrawal Request Declined');
        }

        //send notification email
        sendWithdrawalProcessedEmail($withdrawal, $status);

        return true;
    }
}



//format past date
if (!function_exists('formatPastDate')) {
    function formatPastDate($date)
    {
        //types are attachments and replys
        if (!$date) {
            return false;
        }

        $date_to_seconds = (time() - $date);
        $date = $date_to_seconds . ' secs ago';

        if ($date_to_seconds > 59) {
            $date_to_minutes = floor($date_to_seconds / 60);
            if ($date_to_minutes < 2) {
                $date = $date_to_minutes . ' min ago';
            } else {
                $date = $date_to_minutes . ' mins ago';
            }
        }

        if (($date_to_seconds / 60) > 60) {
            $date_to_hours = floor($date_to_seconds / 3600);
            if ($date_to_hours < 2) {
                $date = $date_to_hours . ' hour ago';
            } else {
                $date = $date_to_hours . ' hours ago';
            }
        }

        if (($date_to_seconds / 3600) > 24) {
            $date_to_day = floor($date_to_seconds / 86400);
            if ($date_to_day < 2) {
                $date = $date_to_day . ' day ago';
            } else {
                $date = $date_to_day . ' days ago';
            }
        }

        if (($date_to_seconds / 86400) > 6) {
            $date_to_week = floor($date_to_seconds / 604800);
            if ($date_to_week < 2) {
                $date = $date_to_week . ' week ago';
            } else {
                $date = $date_to_week . ' weeks ago';
            }
        }

        if (($date_to_seconds / 604800) > 29) {
            $date_to_month = floor($date_to_seconds / (604800 * 30));
            if ($date_to_month < 2) {
                $date = $date_to_month . ' month ago';
            } else {
                $date = $date_to_month . ' months ago';
            }
        }

        return $date;
    }
}


//format future date
if (!function_exists('formatFutureDate')) {
    function formatFutureDate($date)
    {
        //types are attachments and replys
        if (!$date) {
            return false;
        }

        if ($date < time()) {
            return 'past';
        }

        $date_to_seconds = ($date - time());
        $date = 'in ' . $date_to_seconds . ' secs';

        if ($date_to_seconds > 59) {
            $date_to_minutes = ceil($date_to_seconds / 60);
            if ($date_to_minutes < 2) {
                $date = 'in ' . $date_to_minutes . ' min';
            } else {
                $date = 'in ' . $date_to_minutes . ' mins';
            }
        }

        if (($date_to_seconds / 60) > 60) {
            $date_to_hours = ceil($date_to_seconds / 3600);
            if ($date_to_hours < 2) {
                $date = 'in ' . $date_to_hours . ' hour';
            } else {
                $date = 'in ' . $date_to_hours . ' hours';
            }
        }

        if (($date_to_seconds / 3600) > 24) {
            $date_to_day = ceil($date_to_seconds / 86400);
            if ($date_to_day < 2) {
                $date = 'in ' . $date_to_day . ' day';
            } else {
                $date = 'in ' . $date_to_day . ' days';
            }
        }

        if (($date_to_seconds / 86400) > 6) {
            $date_to_week = ceil($date_to_seconds / 604800);
            if ($date_to_week < 2) {
                $date = 'in ' . $date_to_week . ' week';
            } else {
                $date = 'in ' . $date_to_week . ' weeks';
            }
        }

        if (($date_to_seconds / 604800) > 29) {
            $date_to_month = ceil($date_to_seconds / (604800 * 30));
            if ($date_to_month < 2) {
                $date = 'in ' . $date_to_month . ' month';
            } else {
                $date = 'in ' . $date_to_month . ' months';
            }
        }

        return $date;
    }
}

//check if a section is in a page
if (!function_exists('isSectionInPage')) {
    function isPageInSection($view_data, $page, $section)
    {
        $sections = $view_data['sections'];
        $sections = $sections->where('name', $section);

        if ($sections->count() < 1) {
            return false;
        }
        $pages = [];
        foreach ($sections as $section) {
            array_push($pages, json_decode($section->pages));
        }
        //check if the page exist in the array
        if (in_array($page, $pages[0])) {
            return true;
        } else {
            return false;
        }
    }
}

//list all icon
if (!function_exists('listIcons')) {
    function listIcons()
    {
        $icon_file = file_get_contents(app_path('SvgIcons.php'));
        //$regex = '/"' . $config_name . '" => "(.+?)"/s';
        $regex = '/"(.+?)"/s';
        \preg_match_all($regex, $icon_file, $matches);
        //return $matches[1];


        $icons = [];
        foreach ($matches[1] as $icon) {
            array_push($icons, $icon);
        }

        return $icons;
    }
}

//return icon
if (!function_exists('icon')) {
    function icon($icon, $size = null)
    {
        $size = $size ?? 6;
        $icon = '<svg class="w-' . $size . ' h-' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d=" ' . $icon . '"></path></svg>';
        return $icon;
    }
}


//check if a addon is enabled
if (!function_exists('isAddonEnabled')) {
    function isAddonEnabled($name)
    {
        $addons = [];
        $enabled = Module::allEnabled();
        foreach ($enabled as $addon) {
            array_push($addons, strtolower($addon->getName()));
        }

        if (in_array(strtolower($name), $addons)) {
            return true;
        } else {
            return false;
        }
    }
}

//make archive
if (!function_exists('makeArchive')) {
    function makeArchive($path, $check = false)
    {

        if ($check && file_exists($path . '.zip')) {
            return false;
        }


        //zip the files        
        $zipPath = $path;
        $time = '.zip';

        // Initialize archive object
        $zip = new \ZipArchive();
        $zip->open($zipPath . $time, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($zipPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
        );


        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($zipPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();


        return true;
    }
}

//unzip archive 
if (!function_exists('unzipArchive')) {
    function unzipArchive($file, $path)
    {
        $zip = new \ZipArchive;
        if ($zip->open($file) === TRUE) {
            $zip->extractTo($path);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }
}


//purchase code verification
if (!function_exists('purchaseCodeVerification')) {
    function purchaseCodeVerification(string $purchase_code)
    {
        return (object) ['status' => 'success', 'message' => 'License verified'];
    }
}


//check if $cache is writable [0775]
function checkFolderPermission($folder)
{
    $folder_strip = str_replace('core/', '', $folder);
    $perm = substr(sprintf('%o', fileperms(base_path($folder_strip))), -4);
    if ($perm >= '0775') {
        $response = true;
    } else {
        $response = false;
    }

    $resp = [
        'folder' => $folder,
        'status' => $response,
        'perm' => $perm
    ];
    return $resp;
}

function ct(string $string, string $case = null)
{
    // $string = strtolower($string);
    // $translated = __($string);
    // $to_case = strtoupper($translated);

    // if ($case) {
    //     if ($case == 'l') {
    //         $to_case = strtolower($translated);
    //     } elseif ($case == 'c') {
    //         $to_case = ucwords($translated);
    //     } else {
    //         $to_case = strtoupper($translated);
    //     }
    // }

    return $string;
}