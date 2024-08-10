<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->text('subject');
            $table->text('area_of_weakness');
            $table->date('session_start_date');
            $table->date('session_end_date');
            $table->integer('improvement_target');
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
        Schema::dropIfExists('student_targets');
    }
};
