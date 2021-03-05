<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryNewsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('category_news', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')->index()->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('news_id')->index()->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');

            $table->unique(['category_id', 'news_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('news');
    }

}
