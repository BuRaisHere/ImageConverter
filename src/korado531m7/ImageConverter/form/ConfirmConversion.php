<?php
namespace korado531m7\ImageConverter\form;

use korado531m7\ImageConverter\Image;
use korado531m7\ImageConverter\task\ExtractImageTask;
use korado531m7\ImageConverter\utils\ImageUtility;

use pocketmine\Player;
use pocketmine\form\Form;
use pocketmine\Server;

class ConfirmConversion implements Form{
    public function __construct(Image $image, bool $confirm){
        $this->image = $image;
        $this->confirm = $confirm;
    }
    
    public function handleResponse(Player $player, $data) : void{
        if($data === true){
            $player->sendMessage('§bConversion has been started');
            Server::getInstance()->getAsyncPool()->submitTask(new ExtractImageTask($this->image));
        }else{
            $player->sendMessage('§bRestoring Blocks...');
            $result = ImageUtility::restoreBlocks($this->image);
            if($result){
                $player->sendMessage('§aRestored');
            }else{
                $player->sendMessage('§cAn Error has Occurred while Restoring');
            }
        }
    }
    
    public function jsonSerialize(){
        return ['type' => 'modal', 'title' => '§aConfirm Conversion', 'content' => ($this->confirm ? 'Glassess area will be used. ' : '').'Do you want to continue?', 'button1' => 'Convert', 'button2' => 'Cancel'];
    }
}