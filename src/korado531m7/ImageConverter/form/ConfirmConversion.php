<?php
namespace korado531m7\ImageConverter\form;

use korado531m7\ImageConverter\Image;
use korado531m7\ImageConverter\task\ExtractImageTask;

use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\Server;

class ConfirmConversion implements Form{
    public function __construct(Image $image){
        $this->image = $image;
    }
    
    public function handleResponse(Player $player, $data) : void{
        if($data === true){
            $player->sendMessage('Conversion has been started');
            Server::getInstance()->getAsyncPool()->submitTask(new ExtractImageTask($this->image));
        }
    }
    
    public function jsonSerialize(){
        return ['type' => 'modal', 'title' => '§aConfirm Conversion', 'content' => 'Placed glassess area will be used. Do you want to continue?'.PHP_EOL.PHP_EOL.'§dRestoring function is coming soon', 'button1' => 'Convert', 'button2' => 'Cancel'];
    }
}