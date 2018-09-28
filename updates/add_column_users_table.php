<?php namespace Octobro\Gamify\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function(Blueprint $table) {
                $table->unsignedInteger('points')->default(0);
                $table->unsignedInteger('spendable_points')->default(0);
                $table->timestamp('points_updated_at')->nullable();
                $table->timestamp('spendable_points_updated_at')->nullable();
                $table->integer('level_id')->unsigned()->nullable()->index();
                $table->timestamp('level_updated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('points');
            $table->dropColumn('spendable_points');
            $table->dropColumn('points_updated_at');
            $table->dropColumn('spendable_points_updated_at');
            $table->dropColumn('level_id');
            $table->dropColumn('level_updated_at');
        });
    }
}
