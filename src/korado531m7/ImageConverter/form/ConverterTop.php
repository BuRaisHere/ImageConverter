<?php
namespace korado531m7\ImageConverter\form;

use pocketmine\form\Form;
use pocketmine\Player;

class ConverterTop implements Form{
    public function __construct(){
        $this->buttons = [['text' => 'Show Converting List'],['text' => 'Convert a Image']];
    }
    
    public function handleResponse(Player $player, $data) : void{
        $result = $this->getResult($data);
        switch($result){
            case 'Show Converting List':
                $player->sendForm(new ConvertingList());
            break;
            
            case 'Convert a Image':
                $player->sendForm(new SelectImage());
            break;
        }
    }
    
    private function getResult($data) : ?string{
        return $data === null ? null : $this->buttons[$data]['text'];
    }
    
    public function jsonSerialize(){
        return ['type' => 'form', 'title' => '§aWhat do you want to?', 'content' => '', 'buttons' => $this->buttons];
    }
}