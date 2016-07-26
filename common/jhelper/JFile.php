<?php
namespace jhelper;

class JFile
{
    public static function createDirectory($path, $mode = 0775, $recursive = true)
    {
        if (is_dir($path)) {
            return true;
        }
        $parentDir = dirname($path);
        if ($recursive && !is_dir($parentDir)) {
            self::createDirectory($parentDir, $mode, true);
        }
        try {
            $result = mkdir($path, $mode);
            chmod($path, $mode);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create directory '$path': " . $e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }
    
    public static function touchFile($filePath, $mode = 0775, $recursive = true) 
    {
        if (file_exists($filePath)) {
            return true;
        }
        $fileArray = explode(DIRECTORY_SEPARATOR, $filePath); 
        array_pop($fileArray);
        $fileDir = implode(DIRECTORY_SEPARATOR, $fileArray);
        self::createDirectory($fileDir, $mode, $recursive);
        unset($fileArray);
        unset($fileDir);
        try { 
            $result = touch($filePath);
            chmod($filePath, $mode);
        } catch (\Exception $e) {
            throw new \Exception("Failed to touch file '$filePath': " . $e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }
    
}