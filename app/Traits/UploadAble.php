<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait UploadAble
{
    /**
     * Upload a single file in the server
     *
     * @param UploadedFile $file
     * @param null $folder
     * @param string $disk
     * @param null $filename
     * @return false|string
     */
    public function uploadOne(UploadedFile $file, $folder = null, $disk = 'public', $filename = null){

        $name = !is_null($filename) ? $filename : Str::random(25);

        return $file->storeAs(
            public_path(),
            now() . "_" .$name . "." . $file->getClientOriginalExtension(),
            $disk
        );
    }

    public function upload(UploadedFile $file, $folder = null, $disk = 'public'){

        $extension = $file->getClientOriginalExtension();
        $name =Str::random(25);
        $name = Str::slug(now() . "_" . $name) . '.' .$extension;

        return $file->move($folder,$name);
    }

    /**
     * @param UploadedFile $file
     *
     * @param string $folder
     * @param string $disk
     *
     * @return false|string
     */
    public function storeFile(UploadedFile $file, $folder = 'products', $disk = 'public')
    {
        return $file->store($folder, ['disk' => $disk]);
    }


    /**
     * @param Collection $collection
     *
     * @return void
     */
    public function saveProductImages(Collection $collection)
    {
        $collection->each(function (UploadedFile $file) {
            $filename = $this->storeFile($file);
            $productImage = new Attachment([
                'product_id' => $this->model->id,
                'src' => $filename,
                'attachmentType_id' => 1,
            ]);
            $this->model->images()->save($productImage);
        });
    }
}
