<?php
namespace korado531m7\ImageConverter;

use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\Server;

class BlockPlaceClass{
    public function __construct(string $path, ?Player $player, ?string $type, array $images){
        $this->path = $path;
        $this->player = $player;
        $this->type = $type;
        $this->images = $images;
    }
    
    public function doPlace() : void{
        $player = $this->player;
        $image = $this->images;
        $path = $this->path;
        $type = $this->type;
        $extension = ImageAPI::getExtension($path);
        
        if($player instanceof Player){
            $player->sendMessage('Image Extracted. Placing Blocks in main thread...');
            $count = 0;
            $level = $player->getLevel();
            $img = ImageAPI::getResource($path);
            switch($type){
                case 'horizontal':
                    $baseX = (int) ($player->x - imagesx($img) / 2);
                    $baseK = (int) $player->y - 1;
                    $baseY = (int) ($player->z - imagesy($img) / 2);
                    foreach($image as $y => $ally){
                        foreach($ally as $x => $allx){
                            $block = $image[$y][$x];
                            $level->setBlock(new Vector3($baseX + $x,$baseK,$baseY + $y),Block::get($block[0],$block[1]),true,false);
                            $count++;
                        }
                    }
                break;
                
                case 'vertical':
                    $baseX = (int) ($player->x - imagesx($img) / 2);
                    $baseY = (int) ($player->y + imagesy($img) / 2);
                    $baseZ = (int) $player->z + 5;
                    foreach($image as $y => $ally){
                        foreach($ally as $x => $allx){
                            $block = $image[$y][$x];
                            $level->setBlock(new Vector3($baseX + $x,$baseY - $y,$baseZ),Block::get($block[0],$block[1]),true,false);
                            $count++;
                        }
                    }
                break;
                
                default:
                    @imagedestroy($img);
                    throw new \InvalidArgumentException('Not supported type: '.$type);
                break;
            }
            $player->sendMessage("Placed {$count} Blocks");
            @imagedestroy($img);
        }else{
            Server::getInstance()->getLogger()->notice("Not supported on the console");
        }
    }
}