<?php

use App\OAuthClient;
use Illuminate\Database\Seeder;

class AuthClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $oauth_client = OAuthClient::find(2);
        $oauth_client->secret = '57tnXJQDozaxadpHMOfhEde7F4Rhwwh6Hp9AhgCE';
        $oauth_client->save();
    }
}
