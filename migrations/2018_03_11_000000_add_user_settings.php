<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class AddUserSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = $this->_getUserTable();
        Schema::table($tableName, function (Blueprint $table) {
            $table->text('settings')->nullable()->default(null)->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName = $this->_getUserTable();
        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('settings');
        });
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
