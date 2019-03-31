<?php
namespace korado531m7\ImageConverter\form;

use korado531m7\ImageConverter\Image;
use korado531m7\ImageConverter\ImageConverter as IC;

use pocketmine\Player;
use pocketmine\form\Form;

class CompleteList implements Form{
    public function __construct(){
        $this->prepare();
    }
    
    public function handleResponse(Player $player, $data) : void{
        $result = $this->getResult($data);
        if($result instanceof Image){
            $player->sendForm(new CompleteInfo($result));
        }
    }
    
    private function prepare(){
        $res = [];
        $tmp = [];
        $lists = IC::getImages();
        foreach($lists as $list){
            if($list->getStatus() === Image::STATUS_COMPLETE){
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
        return ['type' => 'form', 'title' => '§aList of Converted Image', 'content' => $c === 0 ? 'No images are converted' : ('§e'.$c.' §bimage'.($c === 1 ? ' is' : 's are').' converted'), 'buttons' => $this->buttons];
    }
}