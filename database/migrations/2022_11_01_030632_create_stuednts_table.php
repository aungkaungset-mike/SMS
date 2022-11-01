<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stuednts', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('user_id');
            $table->unsignedMediumInteger('parent_id');
            $table->unsignedMediumInteger('class_id');
            $table->unsignedBigInteger('roll_number');
            $table->string('phone');
            $table->enum('gender' ,['male', 'female', 'other']);
            $table->date('dateofbirth');
            $table->string('address');           
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
        Schema::dropIfExists('stuednts');
    }
};
