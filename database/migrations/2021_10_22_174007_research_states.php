<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ResearchStates extends Migration
{
    public function up()
    {
        Schema::create('research_states', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("name");
        });
    }

    public function down()
    {
        Schema::dropIfExists('research_states');
    }
}
