<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentToReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('comment_id');
            $table->integer('report_id');
            $table->foreign('comment_id')->references('id')->on('comments')
                    ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('report_id')->references('id')->on('reports')
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
        Schema::dropIfExists('comment_report');
    }
}
