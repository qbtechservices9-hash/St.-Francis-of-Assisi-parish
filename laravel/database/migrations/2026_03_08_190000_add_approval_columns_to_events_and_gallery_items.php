<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'approval_status')) {
                $table->string('approval_status')->default('draft')->index()->after('is_published');
            }

            if (!Schema::hasColumn('events', 'submitted_by')) {
                $table->foreignId('submitted_by')->nullable()->after('approval_status')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('events', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('submitted_by')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('events', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('events', 'approval_notes')) {
                $table->text('approval_notes')->nullable()->after('approved_at');
            }
        });

        Schema::table('gallery_items', function (Blueprint $table) {
            if (!Schema::hasColumn('gallery_items', 'approval_status')) {
                $table->string('approval_status')->default('draft')->index()->after('is_published');
            }

            if (!Schema::hasColumn('gallery_items', 'submitted_by')) {
                $table->foreignId('submitted_by')->nullable()->after('approval_status')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('gallery_items', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('submitted_by')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('gallery_items', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('gallery_items', 'approval_notes')) {
                $table->text('approval_notes')->nullable()->after('approved_at');
            }
        });

        DB::table('events')
            ->where('is_published', true)
            ->update(['approval_status' => 'approved']);

        DB::table('events')
            ->where('is_published', false)
            ->update(['approval_status' => 'draft']);

        DB::table('gallery_items')
            ->where('is_published', true)
            ->update(['approval_status' => 'approved']);

        DB::table('gallery_items')
            ->where('is_published', false)
            ->update(['approval_status' => 'draft']);
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            if (Schema::hasColumn('events', 'submitted_by')) {
                $table->dropConstrainedForeignId('submitted_by');
            }

            if (Schema::hasColumn('events', 'approval_notes')) {
                $table->dropColumn('approval_notes');
            }

            if (Schema::hasColumn('events', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('events', 'approval_status')) {
                $table->dropColumn('approval_status');
            }
        });

        Schema::table('gallery_items', function (Blueprint $table) {
            if (Schema::hasColumn('gallery_items', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            if (Schema::hasColumn('gallery_items', 'submitted_by')) {
                $table->dropConstrainedForeignId('submitted_by');
            }

            if (Schema::hasColumn('gallery_items', 'approval_notes')) {
                $table->dropColumn('approval_notes');
            }

            if (Schema::hasColumn('gallery_items', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('gallery_items', 'approval_status')) {
                $table->dropColumn('approval_status');
            }
        });
    }
};