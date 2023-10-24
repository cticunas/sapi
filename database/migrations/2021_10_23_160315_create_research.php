<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResearch extends Migration
{
    public function up()
    {
        Schema::create('research', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("code",50);
            $table->text("title");
            $table->date("date_init");
            $table->date("date_end");
            $table->float("budget")->nullable();
            $table->integer("external")->default(0); // 0 = internal, 1 = external
            $table->integer("incentive")->default(0); // 0 = NO incentive, 1 = YES incentive
            $table->string("grade")->nullable();//1:Pregrado , 2:Posgradum
            $table->string("type_research")->nullable(); //1:Tesis ;2:Inv. Docente ;3: Exp.Profesional;4:Py. innovacion 
            $table->string("document",255)->nullable();//Resolucion N
            $table->unsignedBigInteger("plan")->nullable();
            $table->string("fin_type")->default(3);//1:Financiamiento interno;2:Financiamiento externo; 3:Autofinanciado
            $table->string("fin_company",255)->nullable();
            $table->text("location")->default('Universidad Nacional Agraria de la Selva');
            $table->text("objectives")->default("");
            $table->integer("status")->default(1);
            $table->integer("own_research")->default(1);

            $table->unsignedBigInteger("research_state_id")->default(2);
           // $table->unsignedBigInteger('faculty_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('line_id')->nullable();
            $table->timestamps();

            $table->foreign('research_state_id')->references('id')->on('research_states')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('categories')->onDelete('SET NULL');
            $table->foreign('line_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('plan')->references('id')->on('plans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('research');
    }
}
