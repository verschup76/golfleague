<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMatchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('matches', function(Blueprint $table) {
			$table->increments('id');
			$table->date('date')->unique();
			$table->integer('course_id')->unsigned()->index();
			$table->decimal('purse', 12, 2);
			$table->decimal('skinsamoney', 12, 2);
			$table->decimal('skinsbmoney', 12, 2);
			$table->decimal('grossmoney', 12, 2);
			$table->decimal('netmoney', 12, 2);

			$table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');

			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('matches');
	}

}
