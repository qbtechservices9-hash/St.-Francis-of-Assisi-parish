<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ministries', function (Blueprint $table) {
            if (!Schema::hasColumn('ministries', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('ministries', 'summary')) {
                $table->string('summary')->nullable()->after('description');
            }
            if (!Schema::hasColumn('ministries', 'details')) {
                $table->longText('details')->nullable()->after('summary');
            }
            if (!Schema::hasColumn('ministries', 'meeting_time')) {
                $table->string('meeting_time')->nullable()->after('contact_phone');
            }
            if (!Schema::hasColumn('ministries', 'media')) {
                $table->json('media')->nullable()->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ministries', function (Blueprint $table) {
            if (Schema::hasColumn('ministries', 'media')) {
                $table->dropColumn('media');
            }
            if (Schema::hasColumn('ministries', 'meeting_time')) {
                $table->dropColumn('meeting_time');
            }
            if (Schema::hasColumn('ministries', 'details')) {
                $table->dropColumn('details');
            }
            if (Schema::hasColumn('ministries', 'summary')) {
                $table->dropColumn('summary');
            }
            if (Schema::hasColumn('ministries', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};
