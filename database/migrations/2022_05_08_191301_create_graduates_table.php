<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// ! NOTE: THIS TABLE IS NO LONGER BEING USED

class CreateGraduatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->dropIfExists('graduates');

        Schema::connection('mysql2')->create('graduates', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->enum('level', ['master', 'bachelor', "phD"]);
            $table->decimal('stu_avg1')->nullable();
            $table->decimal('stu_avg2')->nullable();
            $table->decimal('stu_avg3')->nullable();
            $table->decimal('stu_avg4')->nullable();
            $table->decimal('stu_avg5')->nullable();
            $table->decimal('stu_avg6')->nullable();
            $table->decimal('stu_avg7')->nullable();
            $table->decimal('stu_avg8')->nullable();
            $table->decimal('stu_avg9')->nullable();
            $table->decimal('stu_avg10')->nullable();
            $table->decimal('stu_summer_deg')->nullable();
            $table->string('note')->nullable();
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
        Schema::connection('mysql2')->dropIfExists('graduates');
    }
}