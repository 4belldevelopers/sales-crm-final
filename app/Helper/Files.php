<?php

namespace App\Helper;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * Class Reply
 * @package App\Classes
 */
class Files
{

    /**
     * @param $image
     * @param $dir
     * @param null $width
     * @param int $height
     * @param $crop
     * @return string
     * @throws \Exception
     */
    public static function upload($image, $dir, $width = null, $height = 800, $crop = false)
    {

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $image;
        $folder = $dir . '/';

        if (!$uploadedFile->isValid()) {
            throw new \Exception('File was not uploaded correctly');
        }

        $newName = self::generateNewFileName($uploadedFile->getClientOriginalName());

        $tempPath = public_path('user-uploads/temp/' . $newName);
        /** Check if folder exits or not. If not then create the folder */
        if (!\File::exists(public_path('user-uploads/' . $folder))) {
            $result = \File::makeDirectory(public_path('user-uploads/' . $folder), 0775, true);
        }

        $newPath = $folder . '/' . $newName;

        /** @var UploadedFile $uploadedFile */
        $uploadedFile->move(public_path('user-uploads/temp'), $newName);

        if (!empty($crop)) {
            // Crop image
            if (isset($crop[0])) {
                // To store the multiple images for the cropped ones
                foreach ($crop as $cropped) {
                    if (isset($cropped['resize']['width']) && isset($cropped['resize']['height'])) {
                        $fileName = str_replace('.', '_' . $cropped['resize']['width'] . 'x' . $cropped['resize']['height'] . '.', $newName);
                        $tempPathCropped = public_path('user-uploads/temp') . '/' . $fileName;
                        $newPathCropped = $folder . '/' . $fileName;

                        if (class_exists('Intervention\Image\ImageManagerStatic')) {
                            $image = Image::make($tempPath);
                            $image->crop(floor($cropped['width']), floor($cropped['height']), floor($cropped['x']), floor($cropped['y']));
                            $image->resize($cropped['resize']['width'], $cropped['resize']['height'], function ($constraint) {
                                //$constraint->aspectRatio();
                                // $constraint->upsize();
                            });
                            $image->save($tempPathCropped);
                            \Storage::put($newPathCropped, \File::get($tempPathCropped), ['public']);
                            \File::delete($tempPathCropped);
                        } elseif (class_exists('\Intervention\Image\ImageManager') && class_exists('\Intervention\Image\Drivers\Gd\Driver')) {
                            try {
                                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                                $image = $manager->read($tempPath);
                                $image->crop((int)floor($cropped['width']), (int)floor($cropped['height']), (int)floor($cropped['x']), (int)floor($cropped['y']));
                                $image->scaleDown($cropped['resize']['width'], $cropped['resize']['height']);
                                $image->save($tempPathCropped);
                                \Storage::put($newPathCropped, \File::get($tempPathCropped), ['public']);
                                \File::delete($tempPathCropped);
                            } catch (\Throwable $e) {}
                        }
                    }
                }
            } else {
                if (class_exists('Intervention\Image\ImageManagerStatic')) {
                    $image = Image::make($tempPath);
                    $image->crop(floor($crop['width']), floor($crop['height']), floor($crop['x']), floor($crop['y']));
                    $image->save();
                } elseif (class_exists('\Intervention\Image\ImageManager') && class_exists('\Intervention\Image\Drivers\Gd\Driver')) {
                    try {
                        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                        $image = $manager->read($tempPath);
                        $image->crop((int)floor($crop['width']), (int)floor($crop['height']), (int)floor($crop['x']), (int)floor($crop['y']));
                        $image->save();
                    } catch (\Throwable $e) {}
                }
            }
        }

        if (($width || $height)) {
            // Crop / resize image
            if (class_exists('Intervention\Image\ImageManagerStatic')) {
                $image = Image::make($tempPath);
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $image->save();
            } elseif (class_exists('\Intervention\Image\ImageManager') && class_exists('\Intervention\Image\Drivers\Gd\Driver')) {
                try {
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $image = $manager->read($tempPath);
                    $image->scaleDown($width, $height);
                    $image->save();
                } catch (\Throwable $e) {}
            }
        }

        \Storage::put($newPath, \File::get($tempPath), ['public']);

        // Deleting temp file
        \File::delete($tempPath);

        return $newName;
    }

    public static function generateNewFileName($currentFileName)
    {
        $ext = strtolower(\File::extension($currentFileName));
        $newName = md5(microtime());

        if ($ext === '') {
            return $newName;
        }

        return $newName . '.' . $ext;
    }

    public static function deleteFile($image, $folder)
    {
        $dir = trim($folder, '/');
        $path = $dir . '/' . $image;

        if (!\File::exists(public_path($path))) {
            \Storage::delete($path);
        }

        return true;
    }

    public static function deleteDirectory($folder)
    {
        $dir = trim($folder, '/');

        \Storage::deleteDirectory($dir);

        return true;
    }

}