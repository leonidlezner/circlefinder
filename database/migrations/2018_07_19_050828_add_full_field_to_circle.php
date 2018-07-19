<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFullFieldToCircle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('circles', function ($table) {
            $table->boolean('full')->after('completed')->default(false);
        });

        foreach(\App\Circle::all() as $circle) {
            $circle->updateIsFullField();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('circles', function ($table) {
            $table->dropColumn([
                'full',
            ]);
        });
    }
}
