<?php
namespace korado531m7\ImageConverter;

class Color extends \pocketmine\utils\Color{
    /**
     * Returns the logical distance between 2 colors, respecting weight
     * @param Color $c1
     * @param Color $c2
     * @return int
     */
    public static function getDistance(Color $c1, Color $c2){
        $rmean = ($c1->getR() + $c2->getR()) / 2.0;
        $r = $c1->getR() - $c2->getR();
        $g = $c1->getG() - $c2->getG();
        $b = $c1->getB() - $c2->getB();
        $weightR = 2 + $rmean / 256;
        $weightG = 4;
        $weightB = 2 + (255 - $rmean) / 256;
        return $weightR * $r * $r + $weightG * $g * $g + $weightB * $b * $b;
    }

    /**
     * Returns a HSV color array
     * @return array['h','s','v']
     */
    public function toHSV(){
        $r = $this->getR();
        $g = $this->getG();
        $b = $this->getB();
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        $hsv = array('v' => $max / 2.55, 's' => (!$max) ? 0 : (1 - ($min / $max)) * 100, 'h' => 0);
        $dmax = $max - $min;

        if (!$dmax) return $hsv;

        if ($max == $r){
            if ($g < $b){
                $hsv['h'] = ($g - $b) * 60;

            } elseif ($g == $b){
                $hsv['h'] = 360;
            } else{
                $hsv['h'] = ((($g - $b) / $dmax) * 60) + 360;
            }

        } elseif ($max == $g){
            $hsv['h'] = ((($b - $r) / $dmax) * 60) + 120;
        } else{
            $hsv['h'] = ((($r - $g) / $dmax) * 60) + 240;
        }

        return $hsv;
    }

    public function toArray(){
        return [$this->getR(), $this->getG(), $this->getB(), $this->getA()];
    }
}