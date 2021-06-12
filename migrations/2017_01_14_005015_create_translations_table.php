<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->increments('id');

            $table->string('table_name');
            $table->string('column_name');
            $table->integer('foreign_key')->unsigned();
            $table->string('locale');

            $table->text('value');
            
            $table->unique(
            	['table_name', 'column_name', 'foreign_key', 'locale'],
            	'translations_table_name_column_name_foreign_key_locale_unique'
            );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translations');
    }
}
