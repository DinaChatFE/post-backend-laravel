<?php

namespace App\Traits;

trait FactoryExploreFile
{
    public function getDefaultImage()
    {
        return '/uploads/files/original/default.png';
    }
    /**
     * Random to use in factory
     *
     * @param string $path
     * @return string
     */
    public function randomFileSpecificPath($path)
    {
        $arr = [];
        foreach (array_filter(glob(public_path() . $path), 'is_file') as $file) {
            $arr[] =  explode('public/storage', $file)[1] ?? $this->getDefaultImage();
        }
        return $arr[random_int(0, count($arr) - 1)];
    }
    /**
     * With multiped paths
     *
     * @param string $path
     * @param integer $count
     * @return array
     */
    public function randomMultiplePath($path, $count = 2)
    {
        foreach (range(1, $count) as  $val) {
            $arr[] = $this->randomFileSpecificPath($path);
        }
        return $arr ?? [];
    }
}
