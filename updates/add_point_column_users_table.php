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
                $table->unsignedInteger('this_week_points')->after('spendable_points')->default(0);
                $table->unsignedInteger('this_month_points')->after('this_week_points')->default(0);
            });
        }
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('this_week_points');
            $table->dropColumn('this_month_points');
        });
    }
}
