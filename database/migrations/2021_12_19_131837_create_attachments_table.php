<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('description', 30);
            $table->binary('attachment');
            $table->string('path');
            $table->timestamps();
            $table->foreignId('uploader_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('ticket_id')
                  ->constrained('tickets')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
