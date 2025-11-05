<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    // Create Admin User
    User::create([
      'name' => 'Admin User',
      'email' => 'admin@company.com',
      'username' => 'admin',
      'password' => Hash::make('password'),
      'position' => 'System Administrator',
      'company_address' => 'Jl. Sudirman No. 123, Jakarta Selatan',
      'work_field' => 'Technology',
      'role' => 'admin',
    ]);

    // Create Regular User
    User::create([
      'name' => 'John Doe',
      'email' => 'john.doe@company.com',
      'username' => 'johndoe',
      'password' => Hash::make('password'),
      'position' => 'Software Engineer',
      'company_address' => 'Jl. Thamrin No. 456, Jakarta Pusat',
      'work_field' => 'Technology',
      'role' => 'user',
    ]);

    // Create another test user
    User::create([
      'name' => 'Jane Smith',
      'email' => 'jane.smith@company.com',
      'username' => 'janesmith',
      'password' => Hash::make('password'),
      'position' => 'Project Manager',
      'company_address' => 'Jl. Gatot Subroto No. 789, Jakarta Selatan',
      'work_field' => 'Management',
      'role' => 'user',
    ]);
  }
}
