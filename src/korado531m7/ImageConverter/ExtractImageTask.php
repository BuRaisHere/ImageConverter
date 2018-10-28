<?php
namespace korado531m7\ImageConverter;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncTask;

class ExtractImageTask extends AsyncTask{
    private $path;
    
    public function __construct(string $path,string $sender){
        $this->path = $path;
        $this->sender = $sender;
        $this->task = 0;
    }
    
    public function onRun() : void{
        $result = ImageAPI::convertImage($this->path,$this);
        $this->setResult($result);
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
    
    public function onCompletion() : void{
        $server = Server::getInstance();
        $sender = $server->getPlayer($this->sender);
        ImageConverter::removeTask($this);
        if($sender instanceof Player){
            $sender->sendMessage("Image Extracted. Placing Blocks in main thread...");
            $image = $this->getResult();
            $var = explode(".",$this->path);
            $extension = strtolower(array_pop($var));
            switch($extension){
                case "jpg":
                case "jpeg":
                    $img = @imagecreatefromjpeg($this->path);
                break;
            
                case "png":
                    $img = @imagecreatefrompng($this->path);
                break;
            }
            $baseX = (int) ($sender->x - imagesx($img) / 2);
            $baseK = (int) $sender->y - 1;
            $baseY = (int) ($sender->z - imagesy($img) / 2);
            $count = 0;
            $level = $sender->level;
            foreach($image as $y => $ally){
                foreach($ally as $x => $allx){
                    $block = $image[$y][$x];
                    $level->setBlock(new Vector3($baseX + $x,$baseK,$baseY + $y),Block::get($block[0],$block[1]),true,false);
                    $count++;
                }
            }
            $sender->sendMessage("Placed {$count} Blocks");
            imagedestroy($img);
        }else{
            $server->getLogger()->notice("Not supported on the console");
        }
    }
}