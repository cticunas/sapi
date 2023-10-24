<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStages extends Migration
{
    public function up()
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("code");
            $table->string("name");
            $table->string("description");
            $table->integer("status")->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('research_stages');
    }
}
