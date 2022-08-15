<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceMonitoringReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_monitoring_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('monitoring_sites')->onDelete('cascade');
            $table->string('path');
            $table->dateTime('monitoring_time');
            $table->boolean('strategy');
            $table->float('total_score');
            $table->float('FCP');
            $table->float('TTI');
            $table->float('speed_index');
            $table->float('TBT');
            $table->float('LCP');
            $table->float('CLS');
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('h1')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_monitoring_reports');
    }
}
