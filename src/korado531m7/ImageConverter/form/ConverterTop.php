<?php
namespace korado531m7\ImageConverter\form;

use pocketmine\form\Form;
use pocketmine\Player;

class ConverterTop implements Form{
    public function __construct(){
        $this->buttons = [['text' => 'Show Converting List'],['text' => 'Completed Conversion'],['text' => 'Convert an Image']];
    }
    
    public function handleResponse(Player $player, $data) : void{
        $result = $this->getResult($data);
        switch($result){
            case 'Show Converting List':
                $player->sendForm(new ConvertingList());
            break;
            
            case 'Convert an Image':
                $player->sendForm(new SelectImage());
            break;
            
            case 'Completed Conversion':
                $player->sendForm(new CompleteList());
            break;
        }
    }
    
    private function getResult($data) : ?string{
        return $data === null ? null : $this->buttons[$data]['text'];
    }
    
    public function jsonSerialize(){
        return ['type' => 'form', 'title' => '§aWhat would you like to do?', 'content' => '', 'buttons' => $this->buttons];
    }
}