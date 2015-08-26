<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        $users = [
            [
                'name' => 'Admin', 'email' => 'admin@gmail.com', 'password' => 'root'
            ]
        ];
        foreach ($users as $user) {
            (new User)->create($user)->save();
        }
    }
}
