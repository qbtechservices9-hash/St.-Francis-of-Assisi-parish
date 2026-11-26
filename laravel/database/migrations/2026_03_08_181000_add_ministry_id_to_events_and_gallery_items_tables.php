<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'ministry_id')) {
                $table->foreignId('ministry_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('gallery_items', function (Blueprint $table) {
            if (!Schema::hasColumn('gallery_items', 'ministry_id')) {
                $table->foreignId('ministry_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'ministry_id')) {
                $table->dropConstrainedForeignId('ministry_id');
            }
        });

        Schema::table('gallery_items', function (Blueprint $table) {
            if (Schema::hasColumn('gallery_items', 'ministry_id')) {
                $table->dropConstrainedForeignId('ministry_id');
            }
        });
    }
};