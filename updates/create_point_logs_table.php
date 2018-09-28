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
            $table->integer('user_id')->unsigned()->index();
            $table->text('description')->nullable();
            $table->integer('amount');
            $table->unsignedInteger('previous_amount');
            $table->unsignedInteger('updated_amount');
            $table->nullableMorphs('related');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octobro_gamify_point_logs');
    }
}
