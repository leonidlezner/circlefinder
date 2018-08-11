<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCirclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('circles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('uuid')->nullable()->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', config('circle.defaults.types'));
            $table->integer('limit')->unsigned();
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });

        Schema::create('circle_language', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('languages');
            
            $table->integer('circle_id')->unsigned();
            $table->foreign('circle_id')->references('id')->on('circles');
            
            $table->timestamps();
        });

        Schema::table('memberships', function ($table) {
            $table->integer('circle_id')->after('id')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('memberships', function ($table) {
            $table->dropColumn('circle_id');
        });

        Schema::dropIfExists('circle_language');

        Schema::dropIfExists('circles');
    }
}
