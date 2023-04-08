<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfoFromCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_from_customers', function (Blueprint $table) {
            $table->id();
            $table->string('CallSid');
            $table->string('PatientName');
            $table->string('mobile_no');
            $table->string('tablets');
            $table->string('isTablets');
            $table->string('noOfTabs');
            $table->string('isInsulin');
            $table->string('Mg');
            $table->string('times');
            $table->string('age');
            $table->string('gender');
            $table->string('city');
            $table->string('Diabeties_duration');
            $table->string('FBS');
            $table->string('PPBS');
            $table->string('HBA1C');
            $table->string('RBS');
            $table->string('date');
            $table->integer('stage');
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
        Schema::dropIfExists('info_from_customers');
    }
}
