<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('source_codes', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('collection_id');
            $table->string('file_path')->nullable();
            $table->timestamps();

            $table->primary('id');
            $table->foreign('collection_id')
                ->references('id')
                ->on('collections')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('source_codes');
    }
};
