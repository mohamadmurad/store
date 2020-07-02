<?php

use App\AttachmentType;
use Illuminate\Database\Seeder;

class FixedAttachment extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AttachmentType::create([
            'type'=>'image/jpeg'
        ]);

        AttachmentType::create([
            'type'=>'image/png'
        ]);

        AttachmentType::create([
            'type'=>'video/mp4'
        ]);

        AttachmentType::create([
            'type'=>'video/3gp'
        ]);
    }
}
