<?php
namespace korado531m7\ImageConverter\form;

use korado531m7\ImageConverter\ImageConverter;

use pocketmine\form\Form;
use pocketmine\Player;

class SelectImage implements Form{
    public function __construct(){
        $this->prepare();
    }
    
    public function handleResponse(Player $player, $data) : void{
        $result = $this->getResult($data);
        if($result !== null){
            $player->sendForm(new ImageInformation($result));
        }
    }
    
    private function prepare(){
        $iterator = new \RecursiveDirectoryIterator(ImageConverter::getPath());
        $files = [];
        foreach($iterator as $file){
            if(in_array($file->getExtension(), ImageConverter::SUPPORTED_EXTENSION)){
                $files[] = ['text' => $file->getBasename()];
            }
        }
        sort($files);
        $this->buttons = $files;
    }
    
    private function getResult($data) : ?string{
        return $data === null ? null : $this->buttons[$data]['text'];
    }
    
    public function jsonSerialize(){
        $c = count($this->buttons);
        return ['type' => 'form', 'title' => '§aChoose a image', 'content' => ($c === 0 ? '§cNo Images' : ('§e'.$c.' §bimage'.($c === 1 ? ' is' : 's are').' available')), 'buttons' => $this->buttons];
    }
}