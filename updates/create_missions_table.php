<?php namespace Octobro\Gamify\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateMissionsTable extends Migration
{
    public function up()
    {
        Schema::create('octobro_gamify_missions', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('points')->default(0);
            $table->boolean('is_auto_detect')->default(0);
            $table->string('class')->nullable();
            $table->string('type')->nullable(); // daily, weekly, anytime
            $table->unsignedInteger('target')->nullable();
            $table->boolean('is_auto_collect')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octobro_gamify_missions');
    }
}
