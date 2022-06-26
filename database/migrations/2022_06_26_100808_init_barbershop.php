<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create('barbershops', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('barbershop_events', static function (Blueprint $table) {
            $table->increments('id');
            $table->integer('barbershop_id')->unsigned();
            $table->string('name');
            $table->integer('days_duration_slots');
            $table->integer('max_clients_per_slot');
            $table->integer('minutes_every_slots');
            $table->integer('minutes_break_between_slots');
            $table->integer('nth_day_is_holiday');
            $table->jsonb('schedule_settings'); //breaks, work_times

            $table->foreign('barbershop_id')->references('id')->on('barbershops');

            //...etc columns
            $table->timestamps();
        });

        Schema::create('barbershop_appointments', static function (Blueprint $table) {
            $table->increments('id');
            $table->integer('barbershop_event_id')->unsigned();
            $table->dateTime('datetime');
            $table->string('email');
            $table->string('firstname');
            $table->string('lastname');
            $table->integer('status')->default(0); //might need
            $table->foreign('barbershop_event_id')->references('id')->on('barbershop_events');
            //...etc columns
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('barbershop_appointments');
        Schema::dropIfExists('barbershop_events');
        Schema::dropIfExists('barbershops');
    }
};
