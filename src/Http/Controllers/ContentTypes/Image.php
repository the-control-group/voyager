<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image as InterventionImage;

class Image extends BaseType
{
    public function handle()
    {
        if ($this->request->hasFile($this->row->field)) {
            $file = $this->request->file($this->row->field);

            $path = $this->slug.DIRECTORY_SEPARATOR.date('FY').DIRECTORY_SEPARATOR;

            $filename = $this->generateFileName($file, $path);

            $image = InterventionImage::make($file)->orientate();

            $fullPath = $path.$filename.'.'.$file->getClientOriginalExtension();

            $resize_width = null;
            $resize_height = null;

            if (isset($this->options->fit)) {
                if (isset($this->options->fit) && (
                    isset($this->options->fit->width) || isset($this->options->fit->height)
                )) {
                    if (isset($this->options->fit->width)) {
                        $resize_width = $this->options->fit->width;
                    }
                    if (isset($this->options->fit->height)) {
                        $resize_height = $this->options->fit->height;
                    }
                } else {
                    $resize_width = $image->width();
                    $resize_height = $image->height();
                }

                $resize_quality = isset($this->options->quality) ? intval($this->options->quality) : 75;

                $image = $image->fit(
                    $resize_width,
                    $resize_height,
                    function (Constraint $constraint) {
                        $constraint->aspectRatio();
                        if (isset($this->options->upsize) && !$this->options->upsize) {
                            $constraint->upsize();
                        }
                    }
                )->encode($file->getClientOriginalExtension(), $resize_quality);
            }

            if (isset($this->options->resize) && (
                    isset($this->options->resize->width) || isset($this->options->resize->height)
                )) {
                if (isset($this->options->resize->width)) {
                    $resize_width = $this->options->resize->width;
                }
                if (isset($this->options->resize->height)) {
                    $resize_height = $this->options->resize->height;
                }
            } else {
                $resize_width = $image->width();
                $resize_height = $image->height();
            }

            $resize_quality = isset($this->options->quality) ? intval($this->options->quality) : 75;

            $image = $image->resize(
                $resize_width,
                $resize_height,
                function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    if (isset($this->options->upsize) && !$this->options->upsize) {
                        $constraint->upsize();
                    }
                }
            )->encode($file->getClientOriginalExtension(), $resize_quality);

            if ($this->is_animated_gif($file)) {
                Storage::disk(config('voyager.storage.disk'))->put($fullPath, file_get_contents($file), 'public');
                $fullPathStatic = $path.$filename.'-static.'.$file->getClientOriginalExtension();
                Storage::disk(config('voyager.storage.disk'))->put($fullPathStatic, (string) $image, 'public');
            } else {
                Storage::disk(config('voyager.storage.disk'))->put($fullPath, (string) $image, 'public');
            }

            if (isset($this->options->thumbnails)) {
                foreach ($this->options->thumbnails as $thumbnails) {
                    if (isset($thumbnails->name) && isset($thumbnails->scale)) {
                        $scale = intval($thumbnails->scale) / 100;
                        $thumb_resize_width = $resize_width;
                        $thumb_resize_height = $resize_height;

                        if ($thumb_resize_width != null && $thumb_resize_width != 'null') {
                            $thumb_resize_width = intval($thumb_resize_width * $scale);
                        }

                        if ($thumb_resize_height != null && $thumb_resize_height != 'null') {
                            $thumb_resize_height = intval($thumb_resize_height * $scale);
                        }

                        $image = InterventionImage::make($file)
                            ->orientate()
                            ->resize(
                            $thumb_resize_width,
                            $thumb_resize_height,
                            function (Constraint $constraint) {
                                $constraint->aspectRatio();
                                if (isset($this->options->upsize) && !$this->options->upsize) {
                                    $constraint->upsize();
                                }
                            }
                        )->encode($file->getClientOriginalExtension(), $resize_quality);
                    } elseif (isset($thumbnails->crop->width) && isset($thumbnails->crop->height)) {
                        $crop_width = $thumbnails->crop->width;
                        $crop_height = $thumbnails->crop->height;
                        $image = InterventionImage::make($file)
                            ->orientate()
                            ->fit($crop_width, $crop_height)
                            ->encode($file->getClientOriginalExtension(), $resize_quality);
                    }

                    Storage::disk(config('voyager.storage.disk'))->put(
                        $path.$filename.'-'.$thumbnails->name.'.'.$file->getClientOriginalExtension(),
                        (string) $image,
                        'public'
                    );
                }
            }

            return $fullPath;
        }
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @param $path
     *
     * @return string
     */
    protected function generateFileName($file, $path)
    {
        if (isset($this->options->preserveFileUploadName) && $this->options->preserveFileUploadName) {
            $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
            $filename_counter = 1;

            // Make sure the filename does not exist, if it does make sure to add a number to the end 1, 2, 3, etc...
            while (Storage::disk(config('voyager.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
                $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension()).(string) ($filename_counter++);
            }
        } else {
            $filename = Str::random(20);

            // Make sure the filename does not exist, if it does, just regenerate
            while (Storage::disk(config('voyager.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
                $filename = Str::random(20);
            }
        }

        return $filename;
    }

    private function is_animated_gif($filename)
    {
        $raw = file_get_contents($filename);

        $offset = 0;
        $frames = 0;
        while ($frames < 2) {
            $where1 = strpos($raw, "\x00\x21\xF9\x04", $offset);
            if ($where1 === false) {
                break;
            } else {
                $offset = $where1 + 1;
                $where2 = strpos($raw, "\x00\x2C", $offset);
                if ($where2 === false) {
                    break;
                } else {
                    if ($where1 + 8 == $where2) {
                        $frames++;
                    }
                    $offset = $where2 + 1;
                }
            }
        }

        return $frames > 1;
    }
}
