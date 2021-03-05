<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->boolean('show')->default(FALSE);
            $table->bigInteger('view')->default(0);
            $table->string('name');
            $table->string('slug')->index()->nullable();
            $table->string('title');
            $table->string('description');
            $table->string('image');
            $table->boolean('on_index');
            $table->boolean('ready')->default(false);
            $table->boolean('show_img')->default(true);
            $table->boolean('show_user')->default(true);
            $table->mediumText('content');
            $table->boolean('top_left')->default(false);
            $table->boolean('top_right')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('published_at');
            $table->timestamp('top_left_at');
            $table->timestamp('top_right_at');
            $table->boolean('is_editing')->default(false);
            $table->bigInteger('editing_by')->nullable();

            $table->unique(['slug']);
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
