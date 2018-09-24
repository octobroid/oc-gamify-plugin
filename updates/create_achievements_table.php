<?php namespace Octobro\Gamify\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateAchievementsTable extends Migration
{
    public function up()
    {
        Schema::create('octobro_gamify_achievements', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('mission_id')->unsigned();
            $table->text('mission_type');
            $table->date('mission_date');
            $table->integer('achieved_count');
            $table->boolean('is_achieved');
            $table->boolean('is_collected');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octobro_gamify_achievements');
    }
}
