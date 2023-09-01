<?php

use HoomanMirghasemi\Sms\Models\SmsReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// migrated up don't touch
return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(SmsReport::TABLE, function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('mobile')->nullable();
            $table->string('message', 510)->nullable();
            $table->string('from')->nullable();
            $table->string('number')->nullable();
            $table->text('web_service_response')->nullable();
            $table->boolean('success')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(SmsReport::TABLE);
    }
};
