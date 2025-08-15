<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_number')->unique()->comment('رقم الخطة');
            $table->string('group_number')->comment('رقم المجموعة');
            $table->unsignedInteger('lessons_count')->default(0)->comment('عدد الدروس');
            $table->decimal('min_performance', 5, 2)->default(0)->comment('أقل أداء بالنسبة المئوية');
            $table->tinyInteger('status')->default(1)->comment('الحالة: 1=فعال، 0=غير فعال');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plans');
    }
};
