<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('enable_tax')->default(false)->after('store_logo');
            $table->decimal('tax_rate', 8, 2)->default(10.00)->after('enable_tax');
            $table->string('tax_name')->default('VAT')->after('tax_rate');
            $table->string('tax_number')->nullable()->after('tax_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['enable_tax', 'tax_rate', 'tax_name', 'tax_number']);
        });
    }
};
