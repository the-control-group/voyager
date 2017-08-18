<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait ImagesCrop
{
    private $quality;
    private $request;
    private $slug;
    private $dataType;
    private $data;

    /**
     * Save cropped images.
     *
     * @param Illuminate\Http\Request                 $request
     * @param string                                  $slug
     * @param Illuminate\Database\Eloquent\Collection $dataType
     * @param Illuminate\Database\Eloquent\Model      $data
     *
     * @return bool
     */
    public function cropImages(Request $request, $slug, Collection $dataType, Model $data)
    {
        $this->quality = config('voyager.images.quality', 100);
        $this->request = $request;
        $this->slug = $slug;
        $this->dataType = $dataType;
        $this->data = $data;

        foreach ($this->getImagesWithDetails() as $dataRow) {
            $details = json_decode($dataRow->details);

            if (!isset($details->crop)) {
                return false;
            }
            if (!$request->{$dataRow->field}) {
                return false;
            }

            $this->cropImage($details->crop, $dataRow);
        }

        return true;
    }

    /**
     * Crop image by coordinates.
     *
     * @param array                                   $crop
     * @param Illuminate\Database\Eloquent\Collection $dataRow
     *
     * @return void
     */
    public function cropImage($crop, $dataRow)
    {
        $request = $this->request;
        $cropFolderWithoutSlug = config('voyager.images.crop_folder');
        $cropFolder = $cropFolderWithoutSlug.'/'.$this->slug;

        //If a folder is not exists, then make the folder

        if (!File::exists($cropFolder)) {
            File::makeDirectory($cropFolder, 0775, true, true);
        }

        $item_id = $this->data->id;

        foreach ($crop as $cropParam) {
            $inputName = $dataRow->field.'_'.$cropParam->name;
            $params = json_decode($request->get($inputName));

            if (!is_object($params)) {
                return false;
            }

            $img = Image::make(Storage::disk(config('voyager.storage.disk'))
                ->url($request->{$dataRow->field}));

            $img->crop(
                (int) $params->w,
                (int) $params->h,
                (int) $params->x,
                (int) $params->y
            );

            $img->resize($cropParam->size->width, $cropParam->size->height);
            $photo_name = $cropFolder.'/'.$cropParam->name.'_'.$item_id.'_'.$cropParam->size->name.'.jpg';
            $img->save($photo_name, $this->quality);

            if (!empty($cropParam->resize)) {
                foreach ($cropParam->resize as $cropParamResize) {
                    $photo_name = $cropFolder.'/'.$cropParam->name.'_'.$item_id.'_'.$cropParam->name.'.jpg';
                    $img->resize($cropParamResize->width, $cropParamResize->height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $img->save($photo_name, $this->quality);
                }
            }
        }
    }

    /**
     * Get the images with details.
     *
     * @return Illuminate\Database\Eloquent\Collection $dataType
     */
    public function getImagesWithDetails()
    {
        return $this->dataType
            ->where('type', '=', 'image')
            ->where('details', '!=', null);
    }
}
