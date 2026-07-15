<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ManualDepositMethod;
use App\Models\TradingCurrency;
use App\Models\TradingPair;

echo "=== DEPOSIT METHODS ===\n";
$methods = ManualDepositMethod::where('status', 'active')->get();
foreach ($methods as $m) {
    echo "ID:{$m->id} | {$m->name} | type:{$m->type} | cur:{$m->currency} | min:{$m->min_amount} | max:{$m->max_amount} | charge:{$m->charge} | charge_type:{$m->charge_type}\n";
}

echo "\n=== TRADING CURRENCIES ===\n";
$currencies = TradingCurrency::all();
foreach ($currencies as $c) {
    echo "ID:{$c->id} | {$c->name} | symbol:{$c->symbol} | type:{$c->type} | status:{$c->status}\n";
}

echo "\n=== TRADING PAIRS ===\n";
$pairs = TradingPair::all();
foreach ($pairs as $p) {
    echo "ID:{$p->id} | {$p->name} | symbol1:{$p->symbol1} | symbol2:{$p->symbol2} | status:{$p->status}\n";
}
