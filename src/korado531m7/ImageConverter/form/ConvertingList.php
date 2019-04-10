<?php
namespace korado531m7\ImageConverter\form;

use korado531m7\ImageConverter\Image;
use korado531m7\ImageConverter\ImageConverter as IC;

use pocketmine\Player;
use pocketmine\form\Form;

class ConvertingList implements Form{
    public function __construct(){
        $this->prepare();
    }
    
    public function handleResponse(Player $player, $data) : void{
        $result = $this->getResult($data);
        if($result === null){
            $player->sendForm(new ConverterTop());
        }else{
            $player->sendForm(new ConvertInfo($result));
        }
    }
    
    private function prepare(){
        $res = [];
        $tmp = [];
        $lists = IC::getImages();
        foreach($lists as $list){
            if($list->getStatus() === Image::STATUS_CONVERTING){
                $res[] = ['text' => $list->getFilename()];
                $tmp[] = $list;
            }
        }
        $this->temporarily = $tmp;
        $this->buttons = $res;
    }
    
    private function getResult($data) : ?Image{
        return $data === null ? null : $this->temporarily[$data];
    }
    
    public function jsonSerialize(){
        $c = count($this->buttons);
        return ['type' => 'form', 'title' => '§aList of Converting Image', 'content' => $c === 0 ? 'No tasks are working' : ('§e'.$c.' §btask'.($c === 1 ? ' is' : 's are').' working'), 'buttons' => $this->buttons];
    }
}