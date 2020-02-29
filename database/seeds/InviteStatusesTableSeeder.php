<?php

use Illuminate\Database\Seeder;

class InviteStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('invite_statuses')->insert(array(
            array(
                'label' => 'new',
            ),
            array(
                'label' => 'accepted',
            ),
            array(
                'label' => 'rejected',
            ),
        ));
    }
}
