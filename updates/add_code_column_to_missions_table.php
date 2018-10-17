<?php namespace Octobro\Gamify\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class add_code_column_to_missions_table extends Migration
{
    public function up()
    {
        if (Schema::hasTable('octobro_gamify_missions')) {
            Schema::table('octobro_gamify_missions', function(Blueprint $table) {
                $table->string('code')->after('name');
            });
        }
    }

    public function down()
    {
        Schema::table('octobro_gamify_missions', function(Blueprint $table) {
            $table->dropColumn('code');
        });
    }
}
