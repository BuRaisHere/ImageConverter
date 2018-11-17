<?php
namespace korado531m7\ImageConverter;

class ImageAPI{
    public static function rgb2lab($rgb){
        $eps = 216/24389; $k = 24389/27;
        // reference white D50
        $xr = 0.964221; $yr = 1.0; $zr = 0.825211;
        // reference white D65
        #$xr = 0.95047; $yr = 1.0; $zr = 1.08883;
        
        // RGB to XYZ
        $rgb[0] = $rgb[0]/255; //R 0..1
        $rgb[1] = $rgb[1]/255; //G 0..1
        $rgb[2] = $rgb[2]/255; //B 0..1
        
        // assuming sRGB (D65)
        $rgb[0] = ($rgb[0] <= 0.04045)?($rgb[0]/12.92):pow(($rgb[0]+0.055)/1.055,2.4);
        $rgb[1] = ($rgb[1] <= 0.04045)?($rgb[1]/12.92):pow(($rgb[1]+0.055)/1.055,2.4);
        $rgb[2] = ($rgb[2] <= 0.04045)?($rgb[2]/12.92):pow(($rgb[2]+0.055)/1.055,2.4);
        
        // sRGB D50
        $x =  0.4360747*$rgb[0] + 0.3850649*$rgb[1] + 0.1430804*$rgb[2];
        $y =  0.2225045*$rgb[0] + 0.7168786*$rgb[1] + 0.0606169*$rgb[2];
        $z =  0.0139322*$rgb[0] + 0.0971045*$rgb[1] + 0.7141733*$rgb[2];
        // sRGB D65
        /*$x =  0.412453*$rgb[0] + 0.357580*$rgb[1] + 0.180423*$rgb[2];
         $y =  0.212671*$rgb[0] + 0.715160*$rgb[1] + 0.072169*$rgb[2];
         $z =  0.019334*$rgb[0] + 0.119193*$rgb[1] + 0.950227*$rgb[2];*/
        
        // XYZ to Lab
        $xr = $x/$xr; $yr = $y/$yr; $zr = $z/$zr;
        $fx = ($xr > $eps)?pow($xr, 1/3):($fx = ($k * $xr + 16) / 116); $fy = ($yr > $eps)?pow($yr, 1/3):($fy = ($k * $yr + 16) / 116); $fz = ($zr > $eps)?pow($zr, 1/3):($fz = ($k * $zr + 16) / 116);
        
        $lab = array();
        $lab[] = round(( 116 * $fy ) - 16); $lab[] = round(500*($fx-$fy)); $lab[] = round(200*($fy-$fz));
        return $lab;
    }
    
    public static function colorDistance($lab1,$lab2){
        return sqrt(($lab1[0]-$lab2[0])*($lab1[0]-$lab2[0])+($lab1[1]-$lab2[1])*($lab1[1]-$lab2[1])+($lab1[2]-$lab2[2])*($lab1[2]-$lab2[2]));
    }
    
    public static function str2rgb($str){
        $str = preg_replace('~[^0-9a-f]~','',$str);
        $rgb = str_split($str,2);
        for($i=0;$i<3;$i++){
            $rgb[$i] = intval($rgb[$i],16);
        }
        return $rgb;
    }
    
    public static function getNearestColor(array $givenColor,array $palette){
        $givenColorRGB = is_array($givenColor) ? $givenColor:self::str2rgb($givenColor);
        $min = 0xffff;
        $return = null;
        
        foreach($palette as $key => $color){
            $colors = is_array($key)?$color:self::str2rgb($key);
            /* deltaE
            if($min >= ($deltaE = deltaE(self::rgb2lab($key),self::rgb2lab($givenColorRGB))))*/
            //euclidean distance
            if($min >= ($deltaE = self::colorDistance(self::rgb2lab($colors),self::rgb2lab($givenColorRGB)))){
                $min = $deltaE;
                $return = $color;
            }
        }
        return $return;
    }
    
    public static function getExtension(string $filename) : string{
        $var = explode('.', strtolower($filename));
        return array_pop($var);
    }
    
    public static function getResource(string $path){
        switch(self::getExtension($path)){
            case 'jpg':
            case 'jpeg':
                $resource = @imagecreatefromjpeg($path);
                break;
            
            case 'png':
                $resource = @imagecreatefrompng($path);
            break;
            
            default:
                throw new \InvalidArgumentException('Unsupported file type '.$atask->getFilename());
            break;
        }
        return $resource;
    }
    
    public static function convertImage(string $path,ExtractImageTask $atask){
        $colors = [];
        $extension = self::getExtension($path);
        $image = self::getResource($path);
        if($image !== false){
            $width = imagesx($image);
            $height = imagesy($image);
            $pixel = $width * $height;
            $i = 0;
            $image = imagescale($image, $width, $height, \IMG_NEAREST_NEIGHBOUR);
            for ($y = 0; $y < $height; ++$y){
                for ($x = 0; $x < $width; ++$x){
                    $atask->setTaskProgress(($i / $pixel) * 100);
                    $color = imagecolorsforindex($image, imagecolorat($image, $x, $y));
                    $colorA = new Color($color['red'], $color['green'], $color['blue']);
                    $colors[$y][$x] = self::getNearestColor($colorA->toArray(), BlockIdList::getBedrockBlockId());
                    $i++;
                }
            }
            imagedestroy($image);
            return $colors;
        }
    }
}