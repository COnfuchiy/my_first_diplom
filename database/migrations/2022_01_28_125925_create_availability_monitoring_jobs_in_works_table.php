<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvailabilityMonitoringJobsInWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('availability_monitoring_jobs_in_works', function (Blueprint $table) {
            $table->id();
            $table->integer('monitoring_frequently');
            $table->boolean('in_work');
            $table->bigInteger('num_sites_in_work');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('availability_monitoring_jobs_in_works');
    }
}
