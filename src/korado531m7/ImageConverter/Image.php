<?php
namespace korado531m7\ImageConverter;

use korado531m7\ImageConverter\task\ExtractImageTask;

use pocketmine\math\Vector3;

class Image{
    const STATUS_WAITING = 0;
    const STATUS_CONVERTING = 1;
    
    private $filename, $type, $pos, $placer, $task = null, $status = self::STATUS_WAITING, $path, $count, $backup = null, $rotate;
    
    /**
     * @param string   $filename    filename. path is not included
     * @param string   $type        horizontal, vertical
     * @param Vector3  $pos         a center of position to place
     * @param string   $placer      player who place
     */
    public function __construct(string $filename, string $type, Vector3 $pos, string $placer, int $rotate, int $count){
        $this->filename = $filename;
        $this->type = $type;
        $this->pos = clone $pos;
        $this->placer = $placer;
        $this->rotate = $rotate;
        $this->path = ImageConverter::getPath();
        $this->count = $count;
    }
    
    public function getRotation() : int{
        return $this->rotate;
    }
    
    public function getCount() : int{
        return $this->count;
    }
    
    public function getPath() : string{
        return $this->path;
    }
    
    public function setProgress(float $value){
        $this->progress = $value;
    }
    
    public function getProgress() : float{
        return round($this->progress, 2);
    }
    
    public function setTask(ExtractImageTask $task){
        $this->task = $task;
    }
    
    public function getTask(){
        return $this->task;
    }
    
    public function getFilename() : string{
        return $this->filename;
    }
    
    public function getType() : string{
        return $this->type;
    }
    
    public function getPosition() : Vector3{
        return $this->pos;
    }
    
    public function getPlacer() : string{
        return $this->placer;
    }
    
    public function setStatus(int $status){
        $this->status = $status;
        if($status === self::STATUS_CONVERTING){
            $this->backup = null;
        }
    }
    
    public function getStatus() : int{
        return $this->status;
    }
    
    public function setBackup(array $blocks){
        $this->backup = $blocks;
    }
    
    public function getBackup() : ?array{
        return $this->backup;
    }
}