<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            return;
        }

        // The deploy script and the container entrypoint can both run migrations at once.
        try {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->string('method');                       // bkash, nagad, card
                $table->string('provider')->default('manual');  // manual, sslcommerz
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('currency', 3)->default('BDT');
                // pending, verifying, paid, failed, cancelled, refunded
                $table->string('status')->default('pending');
                $table->string('tran_id')->nullable()->unique(); // our reference sent to the gateway
                $table->string('trx_id')->nullable();            // customer's bKash/Nagad TrxID
                $table->string('sender_number')->nullable();
                $table->string('gateway_ref')->nullable();       // gateway validation id / bank transaction id
                $table->json('payload')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
                $table->index(['order_id', 'status']);
                $table->index(['method', 'trx_id']);
            });
        } catch (QueryException $e) {
            if (! Schema::hasTable('payments')) {
                throw $e;
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
