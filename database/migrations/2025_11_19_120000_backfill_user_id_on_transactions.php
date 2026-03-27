<?php

use App\Models\Wallet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('UPDATE transactions t JOIN wallets w ON t.wallet_id = w.id SET t.user_id = w.user_id WHERE t.user_id IS NULL AND t.wallet_id IS NOT NULL');
        } catch (\Throwable $e) {
            DB::table('transactions')
                ->whereNull('user_id')
                ->whereNotNull('wallet_id')
                ->orderBy('id')
                ->chunkById(1000, function ($rows) {
                    $walletIds = collect($rows)->pluck('wallet_id')->filter()->unique()->values();
                    $map = Wallet::whereIn('id', $walletIds)->pluck('user_id', 'id');
                    foreach ($rows as $row) {
                        $uid = $map[$row->wallet_id] ?? null;
                        if ($uid) {
                            DB::table('transactions')->where('id', $row->id)->update(['user_id' => $uid]);
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        DB::table('transactions')->update(['user_id' => DB::raw('user_id')]);
    }
};
