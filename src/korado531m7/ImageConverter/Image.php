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
    
    const FLIP_NONE = 0;
    const FLIP_HORIZONTAL = 1;
    const FLIP_VERTICAL = 2;
    const FLIP_BOTH = 3;
    
    const FILTER_NONE = 0;
    const FILTER_NEGATE = 1;
    
    private $filename, $type, $pos, $place, $placer, $task = null, $status = self::STATUS_WAITING, $path, $id, $backup = null, $rotate = 0, $blockType, $level, $count, $flip = self::FLIP_NONE, $filter = self::FILTER_NONE;
    
    public function __construct(string $filename, int $place, string $type, Vector3 $pos, string $placer, int $blockType, string $level){
        $this->filename = $filename;
        $this->place = $place;
        $this->type = $type;
        $this->pos = clone $pos;
        $this->placer = $placer;
        $this->path = ImageConverter::getPath();
        $this->id = ImageConverter::$count++;
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
    
    public function setFilter(int $value){
        $this->filter = $value;
    }
    
    public function getFilter() : int{
        return $this->filter;
    }
    
    public function setFlip(int $value){
        $this->flip = $value;
    }
    
    public function getFlip() : int{
        return $this->flip;
    }
    
    public function setRotation(int $value){
        $this->rotate = $value;
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