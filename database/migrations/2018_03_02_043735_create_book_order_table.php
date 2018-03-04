<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('person_id',255);
            $table->string('person_name',50);
            $table->timestamps();
            $table->index('person_id');
        });
        DB::statement("ALTER TABLE book_order AUTO_INCREMENT = 168168168168;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_order');
    }
}
