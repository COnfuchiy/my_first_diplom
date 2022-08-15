<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoringSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('domain');
            $table->string('sitemap_url')->nullable();
            $table->integer('monitoring_period');
            $table->integer('timeout')->nullable();
            $table->boolean('ssl_check');
            $table->integer('ssl_notify_num_days')->nullable();
            $table->boolean('seo_psi_mobile_check');
            $table->boolean('seo_psi_desktop_check');
            $table->integer('seo_psi_period_num_days')->nullable();
            $table->integer('seo_psi_mobile_min_value')->nullable();
            $table->integer('seo_psi_desktop_min_value')->nullable();
            $table->boolean('meta_check');
            $table->integer('chat_id');
            $table->foreign('chat_id')->references('telegram_id')->on('telegram_chats');
            $table->integer('monitoring_report_clear_num_days')->nullable();
            $table->integer('performance_report_clear_num_days')->nullable();
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitoring_sites');
    }
}
