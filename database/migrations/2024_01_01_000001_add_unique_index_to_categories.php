<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove duplicate category names (case‑insensitive), keep the smallest id
        DB::statement('DELETE c1 FROM categories c1
            INNER JOIN categories c2
            ON c1.id > c2.id AND LOWER(c1.name) = LOWER(c2.name)');

        // Add a unique index on the name column (case‑sensitive is fine for MySQL default collation)
        Schema::table('categories', function (Blueprint $table) {
            // Ensure the column exists before adding the index
            if (Schema::hasColumn('categories', 'name')) {
                $table->unique('name', 'categories_name_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique('categories_name_unique');
        });
    }
};
