<?php namespace Octobro\Gamify\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateVouchersTable extends Migration
{
    public function up()
    {
        Schema::create('octobro_gamify_vouchers', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('code');
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('used')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octobro_gamify_vouchers');
    }
}
