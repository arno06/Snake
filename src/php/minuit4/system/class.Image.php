<?php 
/**
 * Class Image
 * Permet de g�rer les traitements en rapport avec les fichiers de type Image
 *
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .3
 * @package CBi
 * @subpackage system
 */
abstract class Image
{

    /**
     * M�thode static de creation d'une copie d'une image avec redimensionnement
     * @param String $pSourceImage				Fichier source (se doit d'etre un type image)
     * @param String $pFinalImage				Fichier que l'on souhaite cr�er
     * @param float $pMaxWidth					Largeur du nouveau fichier
     * @param float $pMaxHeight				Hauteur du nouveau fichier
     * @return Boolean
     */
    static public function createCopy($pSourceImage, $pFinalImage, $pMaxWidth, $pMaxHeight) {
        if (!file_exists($pSourceImage))
            return;
        if (file_exists($pFinalImage))
            chmod($pFinalImage, 0666);
        if (!$type = self::isImage($pSourceImage))
            return;
        $size = self::getSize($pSourceImage);
        $currentWidth = $size[0];
        $currentHeight = $size[1];
        
        $TailleRedim = self::getProportionResize($currentWidth, $currentHeight, $pMaxWidth, $pMaxHeight);
        
        $ImageTampon = imagecreatetruecolor($TailleRedim["width"], $TailleRedim["height"]);
        switch ($type) {
            case "jpg":
            case "jpeg":
                $ImageTampon2 = imagecreatefromjpeg($pSourceImage);
                imagecopyresampled($ImageTampon, $ImageTampon2, 0, 0, 0, 0, $TailleRedim["width"], $TailleRedim["height"], $currentWidth, $currentHeight);
                imagejpeg($ImageTampon, $pFinalImage, 100);
                break;
            case "gif":
                $ImageTampon2 = imagecreatefromgif($pSourceImage);
                imagecopyresampled($ImageTampon, $ImageTampon2, 0, 0, 0, 0, $TailleRedim["width"], $TailleRedim["height"], $currentWidth, $currentHeight);
                imagegif($ImageTampon, $pFinalImage);
                break;
            case "png":
				$ImageTampon2 = imagecreatefrompng($pSourceImage);
		        imagealphablending($ImageTampon, false);
		        imagesavealpha($ImageTampon, true);
		        $transparent = imagecolorallocatealpha($ImageTampon, 255, 255, 255, 127);
		        imagefilledrectangle($ImageTampon, 0, 0, $TailleRedim["width"], $TailleRedim["height"], $transparent);
                imagecopyresampled($ImageTampon,$ImageTampon2,0,0,0,0,$TailleRedim["width"],$TailleRedim["height"],$currentWidth,$currentHeight);
                imagepng($ImageTampon, $pFinalImage);
                break;
            default:
                return false;
                break;
        }
        imagedestroy($ImageTampon);
        imagedestroy($ImageTampon2);
        chmod($pFinalImage, 0666);
        return true;
    }
    
    /**
     * M�thode de redimensionnement d'une image existante
     * @param String $pSourceImage				Chemin de l'image � redimensionner
     * @param float $pMaxWidth						Largeur maximale souhait�e
     * @param float $pMaxHeight					Hauteur maximale souhait�e
     * @return Boolean
     */
    static public function resize($pSourceImage, $pMaxWidth, $pMaxHeight) {
        $size = self::getSize($pSourceImage);
        $currentWidth = $size[0];
        $currentHeight = $size[1];
        if (($pMaxWidth > $currentWidth) && ($pMaxHeight > $currentHeight))
            return true;
        $TailleRedim = self::getProportionResize($currentWidth, $currentHeight, $pMaxWidth, $pMaxHeight);
        return self::createCopy($pSourceImage, $pSourceImage, $TailleRedim["width"], $TailleRedim["height"]);
    }
    
    /**
     * M�thode de calcul de dimension apr�s redimensionnement en concervant les proportions
     * @param Number $pWidth			Largeur actuelle
     * @param Number $pHeight			Hauteur actuelle
     * @param float $pMaxWidth			Largeur max
     * @param float $pMaxHeight		Hauteur max
     * @return Array
     */
    static public function getProportionResize($pWidth, $pHeight, $pMaxWidth, $pMaxHeight) {
        $TestW = round($pMaxHeight / $pHeight * $pWidth);
        $TestH = round($pMaxWidth / $pWidth * $pHeight);
        if ($TestW > $pMaxWidth) {
            $width = $pMaxWidth;
            $height = $TestH;
        } elseif ($TestH > $pMaxHeight) {
            $width = $TestW;
            $height = $pMaxHeight;
        } else {
            $width = $pMaxWidth;
            $height = $pMaxHeight;
        }
        return array("width"=>$width, "height"=>$height);
    }
    
    /**
     * R�cup�re la hauteur et la largeur d'un fichier
     * @param String $pSourceImage				Fichier source dont on souhaite r�cup�rer la taille
     * @return Array
     */
    static public function getSize($pSourceImage) {
        return getimagesize($pSourceImage);
    }

    
    /**
     * M�thode permettant de v�rifier si le fichier est bien une image (jpg, gif ou png)
     * @param String $pSourceImage				Fichier source
     * @return String
     */
    static public function isImage($pSourceImage) {
        $extract = array();
        if (preg_match('/^.*\.(jpg|jpeg|gif|png)$/', $pSourceImage, $extract))
            return $extract[1];
        else
            return;
    }
}
?>
