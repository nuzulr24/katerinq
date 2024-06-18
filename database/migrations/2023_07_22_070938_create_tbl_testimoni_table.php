<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblTestimoniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_testimoni', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_client');
            $table->text('notes');
            $table->text('is_thumbnail');
            $table->date('is_created')->default(DB::raw('CURRENT_TIMESTAMP'));

            // Set the table's storage engine and collation
            $table->engine = 'InnoDB';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_testimoni');
    }
}
