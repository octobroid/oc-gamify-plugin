<?php namespace Octobro\Gamify\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateRedemptionLogsTable extends Migration
{
    public function up()
    {
        Schema::create('octobro_gamify_redemption_logs', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('reward_id')->unsigned()->index();
            $table->unsignedInteger('stock');
            $table->integer('amount');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octobro_gamify_redemption_logs');
    }
}
