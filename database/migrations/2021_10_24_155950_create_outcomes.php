<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutcomes extends Migration
{
    public function up()
    {
        Schema::create('outcomes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('type'); 
            //type: 1:Proyecto,2:Avance, 3: Informe, 4: Articulo, 5: Libro
            $table->integer('status')->default(1);
            $table->text('name');
            $table->datetime('date');
            $table->integer('period');
            $table->string('period_type');
            //Trimestre: 1:Enero a Mar.; 2:Abril - Jun.; 3: Julio a Set.; 4: Octubre a Dic.
            $table->integer("public")->default(1); // 0: privado, 1: publico
            $table->integer("approved")->default(0); // 0: No approved, 1: Approved
            $table->integer("reviewed")->default(0); // 0: No reviewed, 1: reviewed
            $table->string('url')->nullable();
            $table->string('doi')->nullable();
            $table->string('journal',255)->nullable();
            $table->string('indexed')->nullable();
            $table->string('other_indexed')->nullable();
            $table->datetime('approved_date')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->datetime('reviewed_date')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->unsignedBigInteger('research_id');
            $table->foreign('research_id')->references('id')->on('research')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('people')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('people')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('research_outcomes');
    }
}
