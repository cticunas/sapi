<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutcomeAuthors extends Migration
{
    public function up()
    {
        Schema::create('outcome_authors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum("role",['TI','AS','OR','EO','CO','OT','IC'])->default('OR');
            //TItular, ASesor, Miembro ORdinario, Miembro EOrdinario,COnsultor, OTros.
            $table->integer("status")->default(1);
			$table->unsignedBigInteger('author_id');
			$table->unsignedBigInteger('outcome_id');
            $table->foreign('author_id')->references('id')->on('people')->onDelete('cascade');
            $table->foreign('outcome_id')->references('id')->on('outcomes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('outcome_authors');
    }
}
