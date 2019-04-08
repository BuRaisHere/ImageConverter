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
        $blocks = [];
        switch($image->getType()){
            case 'Stair':
            case 'Parallelogram':
            case 'Horizontal':
                $width = imagesx($img);
                $height = imagesy($img);
                $baseX = (int) ($image->getPosition()->x - $width / 2);
                $baseK = (int) $image->getPosition()->y - 1;
                $baseY = (int) ($image->getPosition()->z - $height / 2);
                for($y = 0; $y < $height; ++$y){
                    for($x = 0; $x < $width; ++$x){
                        $pos = new Vector3($baseX + $x,$baseK,$baseY + $y);
                        if(!$level->isChunkLoaded($baseX + $x,$baseK,$baseY + $y)){
                            $level->loadChunk($baseX + $x,$baseK,$baseY + $y);
                        }
                        $b = $level->getBlockAt($baseX + $x, $baseK, $baseY + $y,false,false);
                        $blocks[] = [$pos, $b->getId(), $b->getDamage()];
                        $level->setBlock($pos, Block::get(BlockIds::GLASS), true, false);
                        if($x === ($width - 1)){
                            switch($image->getType()){
                                case 'Parallelogram':
                                    switch($image->getPlace()){
                                        case 1:
                                            $baseX += 1;
                                        break;
                                        
                                        case 2:
                                            $baseX -= 1;
                                        break;
                                        
                                        case 3:
                                            $baseY += 1;
                                        break;
                                        
                                        case 4:
                                            $baseY -= 1;
                                        break;
                                    }
                                break;
                                
                                case 'Stair':
                                    switch($image->getPlace()){
                                        case 1:
                                        case 2:
                                            $baseK += 1;
                                        break;
                                        
                                        case 3:
                                        case 4:
                                            $baseK -= 1;
                                        break;
                                    }
                                break;
                            }
                        }
                    }
                }
            break;
            
            case 'Vertical':
                $baseX = (int) ($image->getPosition()->x - imagesx($img) / 2);
                $baseY = (int) ($image->getPosition()->y + imagesy($img) / 2);
                $baseZ = (int) $image->getPosition()->z + 5;
                for($y = 0; $y < imagesy($img); ++$y){
                    for($x = 0; $x < imagesx($img); ++$x){
                        $pos = new Vector3($baseX + $x,$baseY - $y,$baseZ);
                        if(!$level->isChunkLoaded($baseX + $x, $baseZ + $y)){
                            $level->loadChunk($baseX + $x, $baseZ + $y);
                        }
                        $b = $level->getBlockAt($baseX + $x,$baseY - $y,$baseZ,false,false);
                        $blocks[] = [$pos, $b->getId(), $b->getDamage()];
                        $level->setBlock($pos, Block::get(BlockIds::GLASS),true,false);
                    }
                }
            break;
        }
        $image->setBackup($blocks);
    }
    
    public static function restoreBlocks(Image $image) : bool{
        $backup = $image->getBackup();
        if($backup === null){
            return false;
        }else{
            $player = Server::getInstance()->getPlayer($image->getPlacer());
            $level = $player->level;
            foreach($backup as $block){
                $level->setBlock($block[0], Block::get($block[1], $block[2]),true,false);
            }
            return true;
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