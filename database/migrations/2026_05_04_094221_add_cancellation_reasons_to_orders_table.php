<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('customer_cancellation_reason')->nullable()->after('status');
            $table->text('executor_cancellation_reason')->nullable()->after('customer_cancellation_reason');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_cancellation_reason', 'executor_cancellation_reason']);
        });
    }
};