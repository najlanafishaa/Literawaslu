<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            if (!Schema::hasColumn('borrows', 'late_days')) {
                $table->integer('late_days')->default(0)->after('status');
            }
            if (!Schema::hasColumn('borrows', 'fine_amount')) {
                $table->decimal('fine_amount', 10, 2)->default(0)->after('late_days');
            }
            if (!Schema::hasColumn('borrows', 'fine_status')) {
                $table->string('fine_status', 50)->default('none')->after('fine_amount');
            }
            if (!Schema::hasColumn('borrows', 'fine_type')) {
                $table->string('fine_type', 50)->default('none')->after('fine_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropColumn(['fine_amount', 'fine_status']);
        });
    }
};
