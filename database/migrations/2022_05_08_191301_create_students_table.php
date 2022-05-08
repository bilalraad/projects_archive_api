<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->dropIfExists('students');

        Schema::connection('mysql2')->create('students', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->unsignedInteger('year_number')->nullable();
            $table->enum('level', ['master', 'bachelor', "phD"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('students');
    }
}