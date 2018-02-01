<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;

class MultipleImage extends BaseType
{
    /**
     * @return string
     */
    public function handle()
    {
        $filesPath = [];
        $files = $this->request->file($this->row->field);

        $resize_width = 1800;
        $resize_height = null;

        if (isset($this->options->resize) &&
            isset($this->options->resize->width) &&
            isset($this->options->resize->height)) {
            $resize_width = $this->options->resize->width;
            $resize_height = $this->options->resize->height;
        }

        foreach ($files as $file) {
            $filename = Str::random(20);
            $path = getUploadPath($this->slug, config('voyager.storage.path', '%slug%/%date:F%/%date:Y%/'));
            array_push($filesPath, $path.$filename.'.'.$file->getClientOriginalExtension());
            $filePath = $path.$filename.'.'.$file->getClientOriginalExtension();

            $image = Image::make($file)->resize(
                $resize_width,
                $resize_height,
                function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            )->encode($file->getClientOriginalExtension(), 75);

            Storage::disk(config('voyager.storage.disk'))->put($filePath, (string) $image, 'public');

            if (isset($this->options->thumbnails)) {
                foreach ($this->options->thumbnails as $thumbnails) {
                    if (isset($thumbnails->name) && isset($thumbnails->scale)) {
                        $scale = intval($thumbnails->scale) / 100;
                        $thumb_resize_width = $resize_width;
                        $thumb_resize_height = $resize_height;

                        if ($thumb_resize_width != null) {
                            $thumb_resize_width = $thumb_resize_width * $scale;
                        }

                        if ($thumb_resize_height != null) {
                            $thumb_resize_height = $thumb_resize_height * $scale;
                        }

                        $image = Image::make($file)->resize(
                            $thumb_resize_width,
                            $thumb_resize_height,
                            function (Constraint $constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            }
                        )->encode($file->getClientOriginalExtension(), 75);
                    } elseif (isset($this->options->thumbnails) && isset($thumbnails->crop->width) && isset($thumbnails->crop->height)) {
                        $crop_width = $thumbnails->crop->width;
                        $crop_height = $thumbnails->crop->height;
                        $image = Image::make($file)
                            ->fit($crop_width, $crop_height)
                            ->encode($file->getClientOriginalExtension(), 75);
                    }

                    Storage::disk(config('voyager.storage.disk'))->put(
                        $path.$filename.'-'.$thumbnails->name.'.'.$file->getClientOriginalExtension(),
                        (string) $image,
                        'public'
                    );
                }
            }
        }

        return json_encode($filesPath);
    }
}
