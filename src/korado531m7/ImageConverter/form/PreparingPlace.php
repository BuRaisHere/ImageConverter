<?php
namespace korado531m7\ImageConverter\form;

use korado531m7\ImageConverter\ImageConverter;
use korado531m7\ImageConverter\Image;
use korado531m7\ImageConverter\utils\ImageUtility;

use pocketmine\Player;
use pocketmine\form\Form;
use pocketmine\math\Vector3;

class PreparingPlace implements Form{
    public function __construct(string $filename){
        $this->filename = $filename;
        $this->prepare();
    }
    
    public function handleResponse(Player $player, $data) : void{
        if($data !== null){
            $type = 'Horizontal';
            if($data[0] === 1){
                $type = 'Vertical';
            }
            $x = (int) (empty($data[1]) ? $player->x : (is_numeric($data[1]) ? $data[1] : $player->x));
            $y = (int) (empty($data[2]) ? $player->y : (is_numeric($data[2]) ? $data[2] : $player->y));
            $z = (int) (empty($data[3]) ? $player->z : (is_numeric($data[3]) ? $data[3] : $player->z));
            $pos = new Vector3($x, $y, $z);
            $image = new Image($this->filename, $type, $pos, $player->getName(), ImageConverter::$count++);
            ImageConverter::addImage($image);
            ImageUtility::checkArea($image);
            $player->sendForm(new ConfirmConversion($image));
        }
    }
    
    private function prepare(){
        $contents = [];
        $contents[] = ['type' => 'dropdown','text' => 'Select Type','options' => ['Horizontal', 'Vertical']];
        $contents[] = ['type' => 'input', 'text' => 'X Coordinates (If you want to place on your position, don\'t write)', 'placeholder' => 'Your X Coords'];
        $contents[] = ['type' => 'input', 'text' => 'Y Coordinates (If you want to place on your position, don\'t write)', 'placeholder' => 'Your Y Coords'];
        $contents[] = ['type' => 'input', 'text' => 'Z Coordinates (If you want to place on your position, don\'t write)', 'placeholder' => 'Your Z Coords'];
        $this->contents = $contents;
    }
    
    public function jsonSerialize(){
        return ['type' => 'custom_form', 'title' => '§aPreparation of Placing', 'content' => $this->contents];
    }
}