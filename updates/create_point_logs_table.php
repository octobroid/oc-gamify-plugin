<?php namespace Octobro\Gamify\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreatePointLogsTable extends Migration
{
    public function up()
    {
        Schema::create('octobro_gamify_point_logs', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->text('description');
            $table->decimal('previous_amount');
            $table->decimal('updated_amount');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octobro_gamify_point_logs');
    }
}
