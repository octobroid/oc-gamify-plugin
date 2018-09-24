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
            $table->text('name');
            $table->decimal('points');
            $table->text('class');
            $table->text('type');
            $table->decimal('min_target');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octobro_gamify_missions');
    }
}
