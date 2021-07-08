<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class AddUserRoleRelationship extends Migration
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
            $table->bigInteger('role_id')->unsigned()->change();
            $table->foreign('role_id')->references('id')->on('roles');
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
            $table->dropForeign(['role_id']);
        });

        Schema::table($tableName, function (Blueprint $table) {
            $table->bigInteger('role_id')->change();
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
