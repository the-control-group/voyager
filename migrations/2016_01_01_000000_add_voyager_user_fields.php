<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Config;

class AddVoyagerUserFields extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $tableName = $this->_getUserTable();
        Schema::table($tableName, function ($table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'avatar')) {
                $table->string('avatar')->nullable()->after('email')->default('users/default.png');
            }
            $table->bigInteger('role_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $tableName = $this->_getUserTable();
        if (Schema::hasColumn($tableName, 'avatar')) {
            Schema::table($tableName, function ($table) {
                $table->dropColumn('avatar');
            });
        }
        if (Schema::hasColumn($tableName, 'role_id')) {
            Schema::table($tableName, function ($table) {
                $table->dropColumn('role_id');
            });
        }
    }

    /**
     * Get user table from configured user model.
     *
     * @return string User table from configured model
     */
    private function _getUserTable(): string
    {
        $userClass = Config::get('voyager.user.model');
        $userModel = new $userClass();
        return $userModel->getTable();
    }
}
