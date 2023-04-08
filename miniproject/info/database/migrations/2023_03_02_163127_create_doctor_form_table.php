<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctor_form', function (Blueprint $table) {
            $table->id();
            $table->string('CallSid');
            $table->string('mobile_no');
            $table->string('PatientName');
            $table->integer('Diabeties_duration');
            $table->string('stop_med');
            $table->string('continue_med');
            $table->string('reccBreakfast');
            $table->string('reccLunch');
            $table->string('reccSnacks');
            $table->string('reccDinner');
            $table->string('remarks');
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
        Schema::dropIfExists('doctor_form');
    }
}
