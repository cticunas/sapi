<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResearchAuthors extends Migration
{
    public function up()
    {
        Schema::create('research_authors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum("role",['TI','AS','OR','EO','CO','OT','IC'])->default('OR');
            //TItular, ASesor, Miembro ORdinario, Miembro EOrdinario,COnsultor, OTros.
            $table->integer("status")->default(1);
			$table->unsignedBigInteger('author_id');
			$table->unsignedBigInteger('research_id');
            $table->foreign('author_id')->references('id')->on('people')->onDelete('cascade');
            $table->foreign('research_id')->references('id')->on('research')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('research_authors');
    }
}
