<?php

namespace HoomanMirghasemi\Sms\Database\Seeders;

// use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SmsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        //        $params = [
        //            [ 'name' => "default_sms_driver" , 'value' => 'magfa' ],
        //            [ 'name' => "default_voice_call_driver" , 'value' => 'avanak' ],
        //        ];
        //
        //        foreach ($params as $param) {
        //            Setting::create(['name' => $param['name'] , 'value' => $param['value']]);
        //        }
    }
}
