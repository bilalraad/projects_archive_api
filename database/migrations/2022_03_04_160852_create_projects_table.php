<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('student_name');
            $table->string('student_phone_no')->nullable();
            $table->string('supervisor_name');
            $table->date('graduation_year');
            $table->text('abstract')->nullable();
            $table->json('key_words')->nullable();
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
        Schema::dropIfExists('projects');
    }
}
