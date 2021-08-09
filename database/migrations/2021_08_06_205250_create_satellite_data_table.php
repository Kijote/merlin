<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSatelliteDataTable extends Migration
{
/*
        CREATE TABLE satellite_data (
            data_id int NOT NULL AUTO_INCREMENT, 
            name varchar(20), 
            request_time timestamp, 
            distance float, 
            message varchar(512),
            PRIMARY KEY (data_id)
        );
*/

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('satellite_data', function (Blueprint $table) {
            $table->id('id')->unsigned();
            $table->string('name');
            $table->timestamp('request_time');
            $table->decimal('distance', 13, 4);
            $table->string('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('satellite_data');
    }
}
