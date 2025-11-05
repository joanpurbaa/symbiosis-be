<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('documents', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('file_path');
      $table->string('file_type');
      $table->string('document_type');
      $table->string('size');
      $table->string('upload_date');
      $table->enum('status', ['completed', 'processing'])->default('processing');
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('documents');
  }
};
