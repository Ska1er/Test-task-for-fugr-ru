<?php
namespace App\Services;

use App\Services\Contracts\ImageSaver as ContractsImageSaver;

class ImageSaver implements ContractsImageSaver{
    private const path = __DIR__. "/../../images/";

    /**
     * Saving image
     * 
     * @param $image
     * @return string|bool
     */
    public function save($image): string|bool{
        if ((($image['type'] == "image/jpeg")
                || ($image['type'] == "image/jpg")
                || ($image['type'] == "image/png"))
            && ($image['size'] < 20000000)
        ) {
            $name = date('d_m_y_his') . '.' . basename($image['name']);
            $fullPath = $this->getFullPath($name);

            if (move_uploaded_file($image['tmp_name'], $fullPath)) {
                return $name;
            }
        }
        return false;
    }

    /**
     * Getting image
     * 
     * @param string $name
     * @return |bool
     */
    public function get(string $name){
        $fullPath = $this->getFullPath($name);
        if(file_exists($fullPath)){
            $image = file_get_contents($fullPath);
            return $image;
        }
        return false;
    }
    
    /**
     * Removing image
     * 
     * @param string $name;
     * @return bool
     */
    public function remove(string $name): bool{
        $fullPath = $this->getFullPath($name);
        if(file_exists($fullPath)){
            return unlink($fullPath);
        }
        return false;
    }

    private function getFullPath(string $name){
        return self::path . $name;
    }

}