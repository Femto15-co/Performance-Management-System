<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentDefectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_defect', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('comment_id');
            $table->integer('defect_id');
            $table->foreign('comment_id')->references('id')->on('comments')
                    ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('defect_id')->references('id')->on('reports')
                    ->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('comment_defect');
    }
}
