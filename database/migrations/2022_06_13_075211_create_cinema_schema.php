<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCinemaSchema extends Migration
{
    /**
    # Create a migration that creates all tables for the following user stories

    For an example on how a UI for an api using this might look like, please try to book a show at https://in.bookmyshow.com/.
    To not introduce additional complexity, please consider only one cinema.

    Please list the tables that you would create including keys, foreign keys and attributes that are required by the user stories.

    ## User Stories

     **Movie exploration**
     * As a user I want to see which films can be watched and at what times
     * As a user I want to only see the shows which are not booked out

     **Show administration**
     * As a cinema owner I want to run different films at different times
     * As a cinema owner I want to run multiple films at the same time in different locations

     **Pricing**
     * As a cinema owner I want to get paid differently per show
     * As a cinema owner I want to give different seat types a percentage premium, for example 50 % more for vip seat

     **Seating**
     * As a user I want to book a seat
     * As a user I want to book a vip seat/couple seat/super vip/whatever
     * As a user I want to see which seats are still available
     * As a user I want to know where I'm sitting on my ticket
     * As a cinema owner I dont want to configure the seating for every show
     */
    public function up()
    {
        Schema::create('cinemas', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('address');
            //..etc columns
            $table->timestamps();
        });

        Schema::create('cinema_halls', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('cinema_id')->unsigned();

            $table->foreign('cinema_id')->references('id')->on('cinemas');

            //..etc columns
            $table->timestamps();
        });

        Schema::create('seat_types', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('percent');
            //..etc columns
            $table->timestamps();
        });

        Schema::create('cinema_hall_seats', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('position'); //like A2, B22
            $table->integer('cinema_hall_id')->unsigned();
            $table->integer('seat_type_id')->unsigned();

            $table->foreign('cinema_hall_id')->references('id')->on('cinema_halls');
            $table->foreign('seat_type_id')->references('id')->on('seat_types');

            //..etc columns
            $table->timestamps();
        });

        Schema::create('movies', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('duration');
            $table->jsonb('data');//author,title,actors,etc
            $table->jsonb('tags');//author,title,actors,etc
            //..etc columns
            $table->timestamps();
        });

        Schema::create('shows', static function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cinema_hall_id')->unsigned();
            $table->integer('movie_id')->unsigned();
            $table->integer('price');
            $table->integer('status')->default(0);
            $table->dateTime('start_time');
            $table->dateTime('end_time');

            $table->foreign('cinema_hall_id')->references('id')->on('cinema_halls');
            $table->foreign('movie_id')->references('id')->on('movies');

            //..etc columns
            $table->timestamps();
        });

        Schema::create('tickets', static function (Blueprint $table) {
            $table->increments('id');
            $table->integer('show_id')->unsigned();
            $table->integer('cinema_hall_seat_id')->unsigned();
            $table->integer('price');
            $table->integer('status')->default(0);

            $table->foreign('show_id')->references('id')->on('shows');
            $table->foreign('cinema_hall_seat_id')->references('id')->on('cinema_hall_seats');

            //..etc columns
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
        Schema::drop('tickets');
        Schema::drop('shows');
        Schema::drop('movies');
        Schema::drop('cinema_hall_seats');
        Schema::drop('seat_types');
        Schema::drop('cinema_halls');
        Schema::drop('cinemas');
    }
}
