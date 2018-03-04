<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookBookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_book', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number');
            $table->string('name');
            $table->string('author');
            $table->string('price');
            $table->string('publish_year');
            $table->string('publish_type');
            $table->string('image')->nullable();
            $table->char('status',1)->default('A')->comment('A=可查可预订，C=可查不可预定，D=不可查不可预订');
            $table->timestamps();
            $table->index('number');
            $table->index('name');
            $table->index('author');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_book');
    }
}
