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
            'type'=>'jpeg'
        ]);

        AttachmentType::create([
            'type'=>'png'
        ]);

        AttachmentType::create([
            'type'=>'mp4'
        ]);

        AttachmentType::create([
            'type'=>'3gp'
        ]);
    }
}
