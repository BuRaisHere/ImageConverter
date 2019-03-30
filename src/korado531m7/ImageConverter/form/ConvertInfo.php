<?php
namespace korado531m7\ImageConverter\form;

use korado531m7\ImageConverter\ImageConverter as IC;
use korado531m7\ImageConverter\utils\ImageUtility;

use pocketmine\Player;
use pocketmine\form\Form;

class ConvertInfo implements Form{
    private $filename;
    
    public function __construct(string $filename){
        $this->filename = $filename;
    }
    
    public function handleResponse(Player $player, $data) : void{
        
    }
    
    private function getInfo() : string{
        $path = IC::getPath() . $this->filename;
        $image = IC::getImage($this->filename);
        $size = getimagesize($path);
        $text = '';
        $text .= 'Filename: '.$this->filename.PHP_EOL;
        $text .= 'Filesize: '.ImageUtility::formatSizeUnits(filesize($path)).PHP_EOL;
        $text .= 'Size: '.$size[0].'x'.$size[1].PHP_EOL;
        $text .= 'Conversion Progress: '.$image->getTask()->getProgress().'%%'.PHP_EOL;
        $text .= 'Place Type: '.$image->getType().PHP_EOL;
        $text .= 'Estimated time: '.'§7---';
        $text .= PHP_EOL;
        return $text;
    }
    
    public function jsonSerialize(){
        return ['type' => 'form', 'title' => '§eImage Information', 'content' => $this->getInfo(), 'buttons' => []];
    }
}