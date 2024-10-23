<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothingArticlesTable extends Migration
{
    public function up()
    {
        Schema::create('clothing_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('article');
            $table->string('type')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('design_print')->nullable();
            $table->string('image_path');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clothing_articles');
    }
}
