<?php

use App\Enums\LeaveStatuses;
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
        if (!Schema::hasTable('leaves')) {
            Schema::create('leaves', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('leave_type_id');
                $table->unsignedBigInteger('leave_status_id')->default(LeaveStatuses::ON_HOLD->value);
                $table->date('start_date');
                $table->date('end_date');
                $table->tinyInteger('no_days');
                $table->timestamps();

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

                $table->foreign('leave_type_id')
                    ->references('id')
                    ->on('leave_types')
                    ->onDelete('cascade');

                $table->foreign('leave_status_id')
                    ->references('id')
                    ->on('leave_statuses')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
