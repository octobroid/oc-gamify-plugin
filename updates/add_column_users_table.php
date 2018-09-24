<?php namespace Octobro\Gamify\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->decimal('points');
            $table->timestamp('points_updated_at');
            $table->integer('level_id')->unsigned();
            $table->timestamp('level_updated_at');
        });
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('points');
            $table->dropColumn('points_updated_at');
            $table->dropColumn('level_id');
            $table->dropColumn('level_updated_at');
        });
    }
}
