<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResearchReviews extends Migration
{
    public function up()
    {
        Schema::create('research_reviews', function (Blueprint $table) {
           $table->bigIncrements('id');
			$table->unsignedBigInteger('reviewer_id');
			$table->unsignedBigInteger('research_id');
            $table->foreign('reviewer_id')->references('id')->on('people')->onDelete('cascade');
            $table->foreign('research_id')->references('id')->on('research')->onDelete('cascade');
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('research_reviews');
    }
}
