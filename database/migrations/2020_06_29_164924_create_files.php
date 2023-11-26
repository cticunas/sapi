<?php 
use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
//Generated By PlantUML Command
class CreateFiles extends Migration{
	public function up(){ 
 		Schema::create('files', function (Blueprint $table) { 
			$table->bigIncrements('id');
			$table->string('name')->nullable();
			$table->string('url')->nullable();
			$table->string('type')->nullable();
			$table->integer('reference_id')->nullable();
			$table->integer('user_id')->nullable();
			$table->integer('status')->default(1);
			$table->timestamps();
		});
 	} 
	public function down(){
 
	} 
}