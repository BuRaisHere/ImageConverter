<?php
namespace korado531m7\ImageConverter\form;

use korado531m7\ImageConverter\Image;
use korado531m7\ImageConverter\ImageConverter as IC;
use korado531m7\ImageConverter\utils\ImageUtility;

use pocketmine\Player;
use pocketmine\form\Form;
use pocketmine\level\Position;
use pocketmine\Server;

class CompleteInfo implements Form{
    private $image;
    
    public function __construct(Image $image){
        $this->image = $image;
        $this->buttons = [['text' => 'OK'], ['text' => 'Teleport'], ['text' => 'Undo'], ['text' => 'Remove']];
    }
    
    public function handleResponse(Player $player, $data) : void{
        $result = $this->getResult($data);
        if($result === null){
            $player->sendForm(new CompleteList());
        }else{
            switch($result){
                case 'Undo':
                    $player->sendMessage('§bRestoring Blocks...');
                    $result = ImageUtility::restoreBlocks($this->image);
                    if($result){
                        $player->sendMessage('§aRestored');
                    }else{
                        $player->sendMessage('§cAn Error has Occurred while Restoring');
                    }
                    IC::removeImage($this->image);
                break;
                
                case 'Teleport':
                    $level = Server::getInstance()->getLevelByName($this->image->getLevelName());
                    if($level === null){
                        $player->sendMessage('§cCouldn\'t teleport because the level is unloaded');
                    }else{
                        $player->sendMessage('§aTeleporting you to §e'.$this->image->getFilename().'§7...');
                        $pos = $this->image->getPosition();
                        $player->teleport(new Position($pos->x, $pos->y, $pos->z, $level));
                    }
                break;
                
                case 'Remove':
                    IC::removeImage($this->image);
                break;
            }
            
        }
    }
    
    private function getInfo() : string{
        $path = IC::getPath() . $this->image->getFilename();
        $size = getimagesize($path);
        $text = '';
        $text .= 'Filename: '.$this->image->getFilename().PHP_EOL;
        $text .= 'Filesize: '.ImageUtility::formatSizeUnits(filesize($path)).PHP_EOL;
        $text .= 'Size: '.$size[0].'x'.$size[1].PHP_EOL;
        $text .= 'Place Type: '.$this->image->getType().PHP_EOL;
        $pos = $this->image->getPosition();
        $text .= 'Position: '.$pos->x.', '.$pos->y.', '.$pos->z.PHP_EOL;
        $text .= 'Block Amount: '.$this->image->getCount().PHP_EOL;
        switch($this->image->getBlockType()){
            case Image::TYPE_BEDROCK_EDITION:
                $bt = 'Bedrock Edition';
            break;
            case Image::TYPE_JAVA_EDITION:
                $bt = 'Java Edition';
            break;
        }
        $text .= 'Block Type: '.$bt.PHP_EOL;
        $text .= PHP_EOL;
        return $text;
    }
    
    private function getResult($data) : ?string{
        return $data === null ? null : $this->buttons[$data]['text'];
    }
    
    public function jsonSerialize(){
        return ['type' => 'form', 'title' => '§eConvert Information', 'content' => $this->getInfo(), 'buttons' => $this->buttons];
    }
}