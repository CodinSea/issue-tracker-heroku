<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title', 60);
            $table->string('description', 300);
            $table->string('priority', 6);
            $table->string('status', 20);
            $table->string('type', 20);
            $table->timestamps();
            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->onDelete('cascade');
            $table->foreignId('submitter_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->foreignId('developer_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
