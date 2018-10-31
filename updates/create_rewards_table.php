<?php namespace Octobro\Gamify\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateRewardsTable extends Migration
{
    public function up()
    {
        Schema::create('octobro_gamify_rewards', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('stock')->nullable();
            $table->unsignedInteger('min_level_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octobro_gamify_rewards');
    }
}
