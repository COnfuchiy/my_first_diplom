<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvailabilityMonitoringReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('availability_monitoring_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('monitoring_sites')->onDelete('cascade');
            $table->string('path');
            $table->dateTime('first_monitoring_time');
            $table->integer('http_code');
            $table->longText('message')->nullable();
            $table->bigInteger('monitoring_sequence');
            $table->dateTime('last_monitoring_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('availability_monitoring_reports');
    }
}
