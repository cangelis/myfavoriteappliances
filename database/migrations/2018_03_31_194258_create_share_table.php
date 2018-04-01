<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('share', function ($table) {
            $table->integer('sharer')->unsigned();
            $table->integer('sharee')->unsigned();
            $table->foreign('sharer')
                ->on('users')
                ->references('id')
                ->onDelete('cascade');
            $table->foreign('sharee')
                ->on('users')
                ->references('id')
                ->onDelete('cascade');
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
        Schema::drop('share');
    }
}
