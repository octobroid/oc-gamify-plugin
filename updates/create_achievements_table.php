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
            $table->integer('user_id')->unsigned()->index();
            $table->integer('mission_id')->unsigned()->index();
            $table->text('mission_type'); // daily, weekly, one-time, always
            $table->date('mission_date');
            $table->text('data')->nullable();
            $table->integer('achieved_count')->default(0);
            $table->boolean('is_achieved')->default(0);
            $table->boolean('is_collected')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octobro_gamify_achievements');
    }
}
