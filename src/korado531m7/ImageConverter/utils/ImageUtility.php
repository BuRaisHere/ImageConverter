<?php
namespace korado531m7\ImageConverter\utils;

use korado531m7\ImageConverter\Image;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

class ImageUtility{
    public static function checkArea(Image $image){
        $player = Server::getInstance()->getPlayer($image->getPlacer());
        $level = $player->level;
        $img = ImageTool::getResource($image);
        switch($image->getType()){
            case 'Horizontal':
                $baseX = (int) ($image->getPosition()->x - imagesx($img) / 2);
                $baseK = (int) $image->getPosition()->y - 1;
                $baseY = (int) ($image->getPosition()->z - imagesy($img) / 2);
                for($y = 0; $y < imagesy($img); ++$y){
                    for($x = 0; $x < imagesx($img); ++$x){
                        if(!$level->isChunkLoaded($baseX + $x,$baseK,$baseY + $y)) $level->loadChunk($baseX + $x,$baseK,$baseY + $y);
                        $level->setBlock(new Vector3($baseX + $x,$baseK,$baseY + $y),Block::get(BlockIds::GLASS), true, false);
                    }
                }
            break;
            
            case 'Vertical':
                $baseX = (int) ($image->getPosition()->x - imagesx($img) / 2);
                $baseY = (int) ($image->getPosition()->y + imagesy($img) / 2);
                $baseZ = (int) $image->getPosition()->z + 5;
                for($y = 0; $y < imagesy($img); ++$y){
                    for($x = 0; $x < imagesx($img); ++$x){
                        if(!$level->isChunkLoaded($baseX + $x, $baseZ + $y)) $level->loadChunk($baseX + $x, $baseZ + $y);
                        $level->setBlock(new Vector3($baseX + $x,$baseY - $y,$baseZ),Block::get(BlockIds::GLASS),true,false);
                    }
                }
            break;
        }
    }
    
    public static function formatSizeUnits(int $bytes) : string{
        if ($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }elseif ($bytes >= 1048576){
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }elseif ($bytes >= 1024){
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }elseif ($bytes > 1){
            $bytes = $bytes . ' bytes';
        }elseif ($bytes == 1){
            $bytes = $bytes . ' byte';
        }else{
            $bytes = '0 bytes';
        }
        return $bytes;
    }
    
    public static function getExtension(string $filename) : string{
        $var = explode('.', strtolower($filename));
        return array_pop($var);
    }
}