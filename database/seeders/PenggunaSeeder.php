<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $daftarPengguna = [

            ['name'=>'Anggi (026-0425)','token'=>'486511','role'=>'sopir','nomor'=>'6285846486511'],
            ['name'=>'Rizal (015-0920)','token'=>'312811','role'=>'sopir','nomor'=>'6282117312811'],
            ['name'=>'Rohim (020-0122)','token'=>'045981','role'=>'sopir','nomor'=>'6285773045981'],
            ['name'=>'Awan (016-0920)','token'=>'059875','role'=>'sopir','nomor'=>'62882000059875'],
            ['name'=>'Budi (025-0125)','token'=>'859656','role'=>'sopir','nomor'=>'6282123859656'],
            ['name'=>'Dedi (001-0719)','token'=>'079449','role'=>'sopir','nomor'=>'6282117079449'],
            ['name'=>'Jajang (003-0719)','token'=>'760276','role'=>'sopir','nomor'=>'6282217760276'],
            ['name'=>'Irpan (002-0719)','token'=>'884055','role'=>'sopir','nomor'=>'6285213884055'],
            ['name'=>'Endang (022-0423)','token'=>'635002','role'=>'sopir','nomor'=>'6285137635002'],
            ['name'=>'Fanry (008-0920)','token'=>'619955','role'=>'sopir','nomor'=>'6282129619955'],
            ['name'=>'Hamjah (021-1022)','token'=>'631336','role'=>'sopir','nomor'=>'6285793631336'],
            ['name'=>'Nandar (018-0221)','token'=>'491682','role'=>'sopir','nomor'=>'6285237491682'],
            ['name'=>'Hidayat (023-1024)','token'=>'371961','role'=>'sopir','nomor'=>'6283181371961'],
            ['name'=>'Yoga (024-1024)','token'=>'572406','role'=>'sopir','nomor'=>'6282113572406'],
            ['name'=>'Uus (009-0920)','token'=>'821878','role'=>'sopir','nomor'=>'6282316821878'],
            ['name'=>'Yossi (011-0920)','token'=>'885665','role'=>'sopir','nomor'=>'6285353885665'],
            ['name'=>'Aang (005-0920)','token'=>'717237','role'=>'sopir','nomor'=>'6281223717237'],
            ['name'=>'Hernomo (019-0521)','token'=>'038061','role'=>'kepala_sopir','nomor'=>'6287808038061'],

            ['name'=>'Darussalam (001-0426)','token'=>'000001','role'=>'sopir','nomor'=>'000000000001'],
            ['name'=>'Taufik (002-0426)','token'=>'000002','role'=>'sopir','nomor'=>'000000000002'],

            ['name'=>'Nazaruddin','token'=>'151425','role'=>'penumpang','nomor'=>'6282307151425'],
            ['name'=>'Yanuaris','token'=>'303328','role'=>'penumpang','nomor'=>'628111303328'],
            ['name'=>'Firdaus','token'=>'371597','role'=>'penumpang','nomor'=>'6281323371597'],
            ['name'=>'Hendra','token'=>'511455','role'=>'penumpang','nomor'=>'6281323511455'],
            ['name'=>'Wahyudin','token'=>'583404','role'=>'penumpang','nomor'=>'6281394583404'],
            ['name'=>'Acep','token'=>'868474','role'=>'penumpang','nomor'=>'6281312868474'],
            ['name'=>'Dzuriat','token'=>'815751','role'=>'penumpang','nomor'=>'6281392815751'],
            ['name'=>'Awaludin','token'=>'587957','role'=>'penumpang','nomor'=>'6282116587957'],
            ['name'=>'Neneng','token'=>'797303','role'=>'penumpang','nomor'=>'6281323797303'],
            ['name'=>'Dadang','token'=>'587511','role'=>'penumpang','nomor'=>'6281395587511'],
            ['name'=>'Suryadi','token'=>'004512','role'=>'penumpang','nomor'=>'6281322004512'],
            ['name'=>'Adhitya','token'=>'258882','role'=>'penumpang','nomor'=>'6282317258882'],
            ['name'=>'Mansur','token'=>'271093','role'=>'penumpang','nomor'=>'6285649271093'],
            ['name'=>'Yayas','token'=>'864255','role'=>'penumpang','nomor'=>'6282179864255'],
            ['name'=>'Deky','token'=>'720423','role'=>'penumpang','nomor'=>'62811720423'],
            ['name'=>'Sofyan','token'=>'238345','role'=>'penumpang','nomor'=>'628117238345'],

            ['name'=>'Fauzi','token'=>'000003','role'=>'penumpang','nomor'=>'000000000003'],

            ['name'=>'Atep','token'=>'836275','role'=>'penumpang','nomor'=>'6282216836275'],
            ['name'=>'Sabdian','token'=>'508875','role'=>'penumpang','nomor'=>'6281315508875'],
            ['name'=>'Rama','token'=>'918506','role'=>'penumpang','nomor'=>'6281380918506'],
            ['name'=>'Kurnia','token'=>'584904','role'=>'penumpang','nomor'=>'6281222584904'],
            ['name'=>'Ernanda','token'=>'256250','role'=>'penumpang','nomor'=>'6281996256250'],
            ['name'=>'Cecep','token'=>'487147','role'=>'penumpang','nomor'=>'6285722487147'],
            ['name'=>'Didin','token'=>'577299','role'=>'penumpang','nomor'=>'6285749577299'],
            ['name'=>'Gilang','token'=>'129994','role'=>'penumpang','nomor'=>'6285174129994'],
            ['name'=>'Ayubbi','token'=>'949494','role'=>'penumpang','nomor'=>'6281320949494'],
            ['name'=>'Nana','token'=>'181918','role'=>'penumpang','nomor'=>'6282295181918'],
            ['name'=>'Ridwan','token'=>'179997','role'=>'penumpang','nomor'=>'6285695179997'],
            ['name'=>'Wegi','token'=>'171315','role'=>'penumpang','nomor'=>'6282358171315'],
            ['name'=>'Laurentius','token'=>'333014','role'=>'penumpang','nomor'=>'6287825333014'],
            ['name'=>'Rio','token'=>'131724','role'=>'penumpang','nomor'=>'6281394131724'],
            ['name'=>'Taufik','token'=>'095955','role'=>'penumpang','nomor'=>'6282177095955'],
            ['name'=>'Dicki','token'=>'199147','role'=>'penumpang','nomor'=>'6281383199147'],
            ['name'=>'Hada','token'=>'772711','role'=>'penumpang','nomor'=>'6282177772711'],
            ['name'=>'Nur','token'=>'355417','role'=>'penumpang','nomor'=>'6285781355417'],
            ['name'=>'Dani','token'=>'763869','role'=>'penumpang','nomor'=>'6281290763869'],
            ['name'=>'Gading','token'=>'524656','role'=>'penumpang','nomor'=>'6282117524656'],
            ['name'=>'Fadhlurrahman','token'=>'557235','role'=>'penumpang','nomor'=>'6281292557235'],
            ['name'=>'Nafatul','token'=>'789835','role'=>'penumpang','nomor'=>'6282337789835'],
            ['name'=>'Faris','token'=>'146869','role'=>'penumpang','nomor'=>'6281387146869'],
            ['name'=>'Regiantoro','token'=>'137497','role'=>'penumpang','nomor'=>'62895375137497'],
            ['name'=>'Wendi','token'=>'577576','role'=>'penumpang','nomor'=>'6282117577576'],
            ['name'=>'Gradinal','token'=>'040269','role'=>'penumpang','nomor'=>'6282115040269'],
            ['name'=>'Rizky','token'=>'381653','role'=>'penumpang','nomor'=>'6282230381653'],
            ['name'=>'Nurdin','token'=>'211954','role'=>'penumpang','nomor'=>'6281295211954'],
            ['name'=>'Ramadoni','token'=>'123940','role'=>'penumpang','nomor'=>'6282211123940'],
            ['name'=>'Achmad','token'=>'006967','role'=>'penumpang','nomor'=>'6282325006967'],
            ['name'=>'Geri','token'=>'409919','role'=>'penumpang','nomor'=>'6287772409919'],
            ['name'=>'Rifqi','token'=>'538090','role'=>'penumpang','nomor'=>'6281336538090'],
            ['name'=>'Syafiera','token'=>'210250','role'=>'penumpang','nomor'=>'6287784210250'],
            ['name'=>'Agung','token'=>'849329','role'=>'penumpang','nomor'=>'6285745849329'],
            ['name'=>'Maria','token'=>'000518','role'=>'penumpang','nomor'=>'6282224000518'],
            ['name'=>'Rezaldy','token'=>'729066','role'=>'penumpang','nomor'=>'6281224729066'],
        ];

        foreach ($daftarPengguna as $p) {
            DB::table('pengguna')->insert([
                'name'       => $p['name'],
                'token'      => $p['token'],
                'role'       => $p['role'],
                'nomor'      => $p['nomor'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}