<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyHistories extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('histories', function (Blueprint $table) {
			$table->foreign('currency_id', 'FK_histories_currencies')->references('id')->on('currencies');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('histories', function (Blueprint $table) {
			$table->dropForeign('FK_histories_currencies');
		});
	}
}
