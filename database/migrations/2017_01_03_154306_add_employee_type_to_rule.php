<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmployeeTypeToRule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('performance_rules', function (Blueprint $table) {
            $table->dropColumn('for');
            $table->integer('employee_type')->unsigned();
            $table->foreign('employee_type')->references('id')->on('employee_types')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('performance_rules', function (Blueprint $table) {
            $table->dropForeign(['employee_type']);
            $table->dropColumn('employee_type');
            $table->string('for');
        });
    }
}
