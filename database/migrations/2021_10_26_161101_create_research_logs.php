<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResearchLogs extends Migration
{
    public function up()
    {
        Schema::create('research_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('date_at');
            $table->unsignedBigInteger('new_status_id');
            $table->unsignedBigInteger('research_id');
            $table->unsignedBigInteger('user_id');
            $table->text('note')->default("");
            $table->foreign('research_id')->references('id')->on('research')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('new_status_id')->references('id')->on('research_states')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('research_logs');
    }
}
