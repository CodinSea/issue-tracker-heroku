<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 20);
            $table->string('last_name', 30);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('role', 20)->nullable();
            $table->string('picture', 30)->nullable();
            $table->string('city', 20)->nullable();
            $table->string('province', 30)->nullable();
            $table->string('country', 20)->nullable();
            $table->string('website', 30)->nullable();
            $table->string('github', 30)->nullable();
            $table->string('twitter', 30)->nullable();
            $table->string('instagram', 30)->nullable();
            $table->string('facebook', 30)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('mobile', 30)->nullable();
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
        Schema::dropIfExists('users');
    }
}
