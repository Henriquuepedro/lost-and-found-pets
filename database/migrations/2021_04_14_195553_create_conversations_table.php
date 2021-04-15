<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_start')->unsigned();
            $table->bigInteger('user_animal')->unsigned();
            $table->bigInteger('animal_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_start')->references('id')->on('users');
            $table->foreign('user_animal')->references('id')->on('users');
            $table->foreign('animal_id')->references('id')->on('animals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversations');
    }
}
