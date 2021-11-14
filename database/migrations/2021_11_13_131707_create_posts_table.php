<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_post_id')->nullable()->default(null);
            $table->text('children_posts')->nullable();
            $table->integer('user_id');
            $table->integer('tax_id')->nullable()->default(null);
            $table->integer('views')->default(0);
            $table->json('liked_by')->nullable();
            $table->json('shared_by')->nullable();
            $table->integer('is_draft')->default(0);
            $table->integer('is_deleted')->default(0);
            $table->integer('visible')->default(1);
            $table->integer('reply_enabled')->default(1);
            $table->integer('show_track')->default(1);
            $table->integer('is_poll')->default(0);
            $table->json('data')->nullable();
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
        Schema::dropIfExists('posts');
    }
}
