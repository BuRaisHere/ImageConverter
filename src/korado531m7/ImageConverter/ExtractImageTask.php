<?php
namespace korado531m7\ImageConverter;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncTask;

class ExtractImageTask extends AsyncTask{
    private $path;
    
    public function __construct(string $path,string $sender, ?string $type = null){
        $this->path = $path;
        $this->sender = $sender;
        $this->type = $type;
        $this->task = 0;
    }
    
    public function onRun(){
        $result = ImageAPI::convertImage($this->path,$this);
        $this->setResult($result);
    }
    
    public function onCompletion(Server $server){
        $sender = $server->getPlayer($this->sender);
        ImageConverter::removeTask($this);
        $bp = new BlockPlaceClass($this->path, $sender, $this->getType(), $this->getResult());
        $bp->doPlace();
    }
    
    public function getFilename() : string{
        return basename($this->path);
    }
    
    public function getTaskProgress() : float{
        return round($this->task,2);
    }
    
    public function setTaskProgress(float $float){
        $this->task = $float;
    }
    
    public function getType() : string{
        return $this->type ?? 'unknown';
    }
}