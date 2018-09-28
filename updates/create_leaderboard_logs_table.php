<?php namespace Octobro\Gamify\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateLeaderboardLogsTable extends Migration
{
    public function up()
    {
        Schema::create('octobro_gamify_leaderboard_logs', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('type')->nullable(); // weekly, monthly, all
            $table->date('date')->nullable();
            $table->text('data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octobro_gamify_leaderboard_logs');
    }
}
