<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->bigInteger('tax_id')->unsigned()->change();
            $table->bigInteger('user_id')->unsigned()->change();
            $table->foreign('tax_id')->references('id')->on('taxonomies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['tax_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex('posts_tax_id_foreign');
            $table->dropIndex('posts_user_id_foreign');
        });
    }
}
