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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('students', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('students', 'phone')) {
                $table->string('phone', 20)->nullable();
            }
            if (!Schema::hasColumn('students', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('students', 'birth_date')) {
                $table->date('birth_date')->nullable();
            }
            if (!Schema::hasColumn('students', 'parent_phone')) {
                $table->string('parent_phone', 20)->nullable();
            }
            if (!Schema::hasColumn('students', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('students', 'group_id')) {
                $table->foreignId('group_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('students', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'email',
                'phone',
                'address',
                'birth_date',
                'parent_phone',
                'notes'
            ]);

            if (Schema::hasColumn('students', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
            
            if (Schema::hasColumn('students', 'group_id')) {
                $table->dropForeign(['group_id']);
                $table->dropColumn('group_id');
            }
        });
    }
};
