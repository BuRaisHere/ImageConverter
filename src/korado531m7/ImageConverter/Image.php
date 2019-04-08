<?php
namespace korado531m7\ImageConverter;

use korado531m7\ImageConverter\task\ExtractImageTask;

use pocketmine\math\Vector3;

class Image{
    const STATUS_WAITING = 0;
    const STATUS_CONVERTING = 1;
    const STATUS_COMPLETE = 2;
    
    const TYPE_BEDROCK_EDITION = 0;
    const TYPE_JAVA_EDITION = 1;
    
    private $filename, $type, $pos, $place, $placer, $task = null, $status = self::STATUS_WAITING, $path, $id, $backup = null, $rotate, $blockType, $level, $count;
    
    public function __construct(string $filename, int $place, string $type, Vector3 $pos, string $placer, int $rotate, int $blockType, int $id, string $level){
        $this->filename = $filename;
        $this->place = $place;
        $this->type = $type;
        $this->pos = clone $pos;
        $this->placer = $placer;
        $this->rotate = $rotate;
        $this->path = ImageConverter::getPath();
        $this->id = $id;
        $this->blockType = $blockType;
        $this->level = $level;
    }
    
    public function getLevelName() : string{
        return $this->level;
    }
    
    public function getPlace() : int{
        return $this->place;
    }
    
    public function getBlockType() : int{
        return $this->blockType;
    }
    
    public function getRotation() : int{
        return $this->rotate;
    }
    
    public function getId() : int{
        return $this->id;
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
    
    public function getCount() : int{
        return $this->count;
    }
    
    public function setCount(int $count){
        $this->count = $count;
    }
}