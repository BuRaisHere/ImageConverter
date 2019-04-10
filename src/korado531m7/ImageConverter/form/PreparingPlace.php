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
            if($data[0] === 1){
                $type = 'Vertical';
            }elseif($data[0] === 2){
                $type = 'Stair';
            }elseif($data[0] === 3){
                $type = 'Parallelogram';
            }
            $place = $data[1] + 1;
            $x = (int) empty($data[2]) ? $player->x : (is_numeric($data[2]) ? $data[2] : $player->x);
            $y = (int) empty($data[3]) ? $player->y : (is_numeric($data[3]) ? $data[2] : $player->y);
            $z = (int) empty($data[4]) ? $player->z : (is_numeric($data[4]) ? $data[2] : $player->z);
            if($data[9] === 0){
                $blockType = Image::TYPE_BEDROCK_EDITION;
            }elseif($data[9] === 1){
                $blockType = Image::TYPE_JAVA_EDITION;
            }
            $pos = new Vector3($x, $y, $z);
            $image = new Image($this->filename, $place, $type ?? 'Horizontal', $pos, $player->getName(), $blockType, $player->level->getFolderName());
            $image->setRotation($data[5]);
            if($data[6] === 1){
                $image->setFilter(Image::FILTER_NEGATE);
            }
            if($data[7] === 1){
                $image->setFlip(Image::FLIP_HORIZONTAL);
            }elseif($data[7] === 2){
                $image->setFlip(Image::FLIP_VERTICAL);
            }elseif($data[7] === 3){
                $image->setFlip(Image::FLIP_BOTH);
            }
            ImageConverter::addImage($image);
            $confirm = $data[8] === 0;
            if($confirm){
                $player->sendMessage('§7Please wait...');
                ImageUtility::checkArea($image);
            }
            $player->sendForm(new ConfirmConversion($image, $confirm));
        }
    }
    
    private function prepare(){
        $contents = [];
        $contents[] = ['type' => 'dropdown','text' => 'Select Type','options' => ['Horizontal', 'Vertical', 'Stair', 'Parallelogram']];
        $contents[] = ['type' => 'dropdown','text' => 'Placement Type (For Stair, Parallelogram)','options' => ['1','2','3','4']];
        $contents[] = ['type' => 'input', 'text' => 'X Coordinates (Default: Your position)', 'placeholder' => 'Your X Coords'];
        $contents[] = ['type' => 'input', 'text' => 'Y Coordinates (Default: Your position)', 'placeholder' => 'Your Y Coords'];
        $contents[] = ['type' => 'input', 'text' => 'Z Coordinates (Default: Your position)', 'placeholder' => 'Your Z Coords'];
        $contents[] = ['type' => 'slider','text' => 'Image Rotation (Degree)','min' => 0, 'max' => 359];
        $contents[] = ['type' => 'dropdown','text' => 'Image Filter','options' => ['None', 'Reverses all colors']];
        $contents[] = ['type' => 'dropdown','text' => 'Image Flip','options' => ['None', 'Horizontal', 'Vertical', 'Horizontal and Vertical']];
        $contents[] = ['type' => 'dropdown','text' => 'Confirm Area','options' => ['Yes', 'No']];
        $contents[] = ['type' => 'dropdown','text' => 'Choose Block Id Type','options' => ['Bedrock Edition', 'Java Edition']];
        $this->contents = $contents;
    }
    
    public function jsonSerialize(){
        return ['type' => 'custom_form', 'title' => '§aPreparation of Placing', 'content' => $this->contents];
    }
}