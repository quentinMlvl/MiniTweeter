<?php

namespace mf\utils;

class ClassLoader extends AbstractClassLoader {

    public function loadClass(string $classname)
    {
        $filename = $this->getFileName($classname);
        $path = $this->makePath($filename);

        if(file_exists($path)){
            require_once($path);
        }
    }

    protected function makePath(string $filename):string
    {
        $path = $this->prefix . DIRECTORY_SEPARATOR . $filename;
        return $path;
    }

    protected function getFileName(string $classname):string
    {
        $filename = str_replace('\\', DIRECTORY_SEPARATOR , $classname) . '.php';
        return $filename;
    }

}