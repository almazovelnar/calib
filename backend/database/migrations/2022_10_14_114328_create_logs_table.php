<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->ipAddress('ip');
            $table->string('agent');
            $table->string('action');
            $table->string('description');
            $table->string('subject_type');
            $table->foreignId('news_id')->index()->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('user_id')->index()->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->json('properties');
            $table->timestamp('action_at', 0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
