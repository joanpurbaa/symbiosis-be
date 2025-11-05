<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('users', function (Blueprint $table) {
      // Cek dulu apakah kolom sudah ada sebelum menambah
      if (!Schema::hasColumn('users', 'username')) {
        $table->string('username')->unique()->after('email');
      }

      if (!Schema::hasColumn('users', 'position')) {
        $table->string('position')->nullable()->after('username');
      }

      if (!Schema::hasColumn('users', 'company_address')) {
        $table->text('company_address')->nullable()->after('position');
      }

      if (!Schema::hasColumn('users', 'work_field')) {
        $table->string('work_field')->nullable()->after('company_address');
      }

      if (!Schema::hasColumn('users', 'role')) {
        $table->string('role')->default('user')->after('work_field');
      }
    });
  }

  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropColumn(['username', 'position', 'company_address', 'work_field', 'role']);
    });
  }
};
