<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempFilesTable extends Migration
{
    public function up()
    {
        Schema::create('temp_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');
            $table->string('path');
            $table->string('name');
            $table->string('mime_type')->nullable();
            $table->bigInteger('size');
            $table->timestamps();
        });
    }
}
