<?php
namespace korado531m7\ImageConverter\form;

use korado531m7\ImageConverter\ImageConverter as IC;
use korado531m7\ImageConverter\utils\ImageUtility;

use pocketmine\form\Form;
use pocketmine\Player;

class ImageInformation implements Form{
    private $filename;
    
    public function __construct(string $filename){
        $this->buttons = [['text' => 'Convert into Block'],['text' => 'Cancel']];
        $this->filename = $filename;
    }
    
    public function handleResponse(Player $player, $data) : void{
        $result = $this->getResult($data);
        if($result === null){
            $player->sendForm(new SelectImage());
        }elseif($result === 'Convert into Block'){
            $player->sendForm(new PreparingPlace($this->filename));
        }
    }
    
    private function getInfo() : string{
        $path = IC::getPath() . $this->filename;
        $size = getimagesize($path);
        $text = '';
        $text .= 'Filename: '.$this->filename.PHP_EOL;
        $text .= 'Filesize: '.ImageUtility::formatSizeUnits(filesize($path)).PHP_EOL;
        $text .= 'Size: '.$size[0].'x'.$size[1].PHP_EOL;
        $text .= PHP_EOL;
        return $text;
    }
    
    private function getResult($data) : ?string{
        return $data === null ? null : $this->buttons[$data]['text'];
    }
    
    public function jsonSerialize(){
        return ['type' => 'form', 'title' => '§eImage Information', 'content' => $this->getInfo(), 'buttons' => $this->buttons];
    }
}