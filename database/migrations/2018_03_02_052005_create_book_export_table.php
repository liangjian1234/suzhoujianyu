<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookExportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_export', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('number')->comment('导出excel的名称');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
            $table->index('number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_export');
    }
}
