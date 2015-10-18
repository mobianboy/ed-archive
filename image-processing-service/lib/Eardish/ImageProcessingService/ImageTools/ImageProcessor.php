<?php
namespace Eardish\ImageProcessingService\ImageTools;

use Monolog\Logger;

class ImageProcessor
{
    /**
     * Information about the image.
     *
     * @var array
     */
    protected $params = array();

    /**
     * @var array
     */
    protected $resized = array();

    /**
     * Resource representation of the image. Blank until createResource() function is called.
     *
     * @var
     */
    protected $imageResource;

    /**
     * Binary data of the image. This becomes empty once a resource is created.
     *
     * @var string
     */
    protected $imageBinary;

    /**
     * Logger to write information to.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Set to true in case of a failure.
     *
     * @var bool
     */
    protected $sourceFail = false;

    /**
     * The original location or data of the image.
     *
     * @var array
     */
    protected $source = array();

    /**
     * Used to store locations of temporary files.
     *
     * @var array
     */
    protected $tmpFiles = array();

    /**
     * Does a standard image conversion to the default thumb sizes. User Id or other unique ID is needed to ensure correct files are deleted all the time.
     *
     * @param string $source
     * @param string $type
     * @param string $uniqueId
     * @param array $sizes
     * @return \Eardish\ImageProcessingService\ImageTools\ImageProcessor
     */
    public function doImageConv($source, $type, $uniqueId, $sizes = array())
    {
        $this->imageBinary = "";
        $this->imageResource = "";
        $image = $this->openImage($source, $type)->createResource();

        if (count($sizes)) {
            $image->generateVersions($sizes);
        }

        $resources = $this->resized;

        foreach ($resources as $name => $gd) {
            $this->tmpFiles[] = $this->saveGDToTemp($gd, $this->source['type'], $uniqueId."_".$name);
        }

        return $this;
    }

    /**
     * Creates a GD resource in $this->imageResource out of $this->imageString
     *
     * @return \Eardish\ImageProcessingService\ImageTools\ImageProcessor
     */
    public function createResource()
    {
        if ($this->sourceFail || !$this->imageBinary || empty($this->imageBinary)) {
            return $this;
        }

        $resource = imagecreatefromstring($this->imageBinary);

        if ($resource) {
            $this->imageBinary = "";
            $this->imageResource = $resource;
        } else {
            //$this->logger->err("The image resource couldn't be created");
            $this->sourceFail = true;

            return $this;
        }

        $this->setParams();

        return $this;
    }

    /**
     * @param $size
     * @param string $mode
     * @return \Eardish\ImageProcessingService\ImageTools\ImageProcessor
     */
    public function resizeImage($size, $mode = "aspect")
    {
        $this->ratioCrop(1, 1);

        switch ($mode) {
            case "stretch":
                // Simply stretch the image to the given dimensions
                $newx = $size[0];
                $newy = $size[1];
                break;
            case "max":
                // Determine which side will hit the new size last and use that ratio
                // to scale the second side with white for the extra margins
                $ratiox = $this->params['x']/$size[0];
                $ratioy = $this->params['y']/$size[1];

                $ratio = $ratiox;

                if ($ratioy < $ratiox) {
                    $ratio = $ratioy;
                }

                list($newx, $newy) = $this->applyRatio(array($this->params['x'], $this->params['y']), array($ratio, $ratio));
                break;
            case "aspect":
            default:
                // Determine which side will hit the new size first and use that ratio
                // to scale the second side
                $ratiox = $this->params['x']/$size[0];
                $ratioy = $this->params['y']/$size[1];

                $ratio = $ratiox;

                if ($ratioy > $ratiox) {
                    $ratio = $ratioy;
                }

                list($newx, $newy) = $this->applyRatio(array($this->params['x'], $this->params['y']), array($ratio, $ratio));
                break;
        }

        $canvas = imagecreatetruecolor($newx, $newy);

        imagecopyresampled($canvas, $this->imageResource, 0, 0, 0, 0, $newx, $newy, $this->params['x'], $this->params['y']);

        $this->imageResource = $canvas;

        $this->setParams();

        return $this;
    }

    /**
     * Performs a crop where the image is first resized the largest available size
     * that will still crop to the correct dimensions without any whitespace
     *
     * @param int $x
     * @param int $y
     * @return \Eardish\ImageProcessingService\ImageTools\ImageProcessor
     */
    public function ratioCrop($x, $y)
    {

        // Determine the direction of the crop...
        // dir will be larger than x if the y was the larger change
        // dir will be smaller than x if the y was the smaller change
        $dir = ($this->params['x']*$y)/$this->params['y'];

        if ($dir == $x) {
            // The size hasn't changed, so take no action
            return $this;
        } elseif ($dir > $x) {
            // The y was the larger change, so grab the new x dimension
            $newx = round(($this->params['y']*$x)/$y);

            // Take half the new x to determine where the clipping offset should be
            $half_diff = round(($this->params['x']-$newx)/2);

            // Crop the image
            return $this->cropImage(array($newx, $this->params['y']), array($half_diff, 0));
        } elseif ($dir < $x) {
            // The y was the smaller change, so grab the new y
            $newy = round(($this->params['x']*$y)/$x);

            $half_diff = round(($this->params['y']-$newy)/2);

            return $this->cropImage(array($this->params['x'], $newy), array(0, $half_diff));
        }

        return false;
    }

    /**
     * Generates set sizes as defined and requested by the clientside team.
     *
     * @param array $sizes
     * @return \Eardish\ImageProcessingService\ImageTools\ImageProcessor
     */
    public function generateVersions($sizes)
    {
        if ($this->sourceFail || !$this->imageResource) {
            return $this;
        }

        // Update this array with required sized (uses the defined size as width and height)

        $state = $this->storeState();

        $this->storeResized("profile_art_original");

        $this->ratioCrop(1, 1);

        // TODO: Check to see if val is an array. if it is, do things a little differently to support non 1:1 conversions.
        foreach ($sizes as $name => $val) {
            if (is_array($val)) {
                $this->ratioCrop($val[0], $val[1])->resizeImage(array($val[0],$val[1]), "stretch")->storeResized($name);
            } else {
                $this->resizeImage(array($val,$val), "stretch")->storeResized($name);
            }
            $this->restoreState($state);
        }

        $this->restoreState($state);

        return $this;
    }

    /**
     * Generates a default set of thumbs in the $this->resized variable
     *
     * @return \Eardish\ImageProcessingService\ImageTools\ImageProcessor
     */
    public function generateThumbs()
    {
        if ($this->sourceFail || !$this->imageResource) {
            return $this;
        }

        $sizes = array("188","144","45","42","40","38","28","26","20","18");

        $state = $this->storeState();

        $this->ratioCrop(1, 1);

        foreach ($sizes as $val) {
            $this->resizeImage(array($val,$val), "stretch")->storeResized("thumb".$val."x".$val);
        }

        $this->restoreState($state);
        $w = 100;
        $h = 44;
        $this->ratioCrop($w, $h)->resizeImage(array($w,$h), "stretch")->storeResized("thumb".$w."x".$h);

        $this->restoreState($state);
        $w = 159;
        $h = 41;
        $this->ratioCrop($w, $h)->resizeImage(array($w,$h), "stretch")->storeResized("thumb".$w."x".$h);

        $this->restoreState($state);
        $w = 131;
        $h = 90;
        $this->ratioCrop($w, $h)->resizeImage(array($w,$h), "stretch")->storeResized("thumb".$w."x".$h);

        $this->restoreState($state);
        $w = 200;
        $h = 124;
        $this->ratioCrop($w, $h)->resizeImage(array($w,$h), "stretch")->storeResized("thumb".$w."x".$h);

        $this->restoreState($state);
        $w = 200;
        $h = 166;
        $this->ratioCrop($w, $h)->resizeImage(array($w,$h), "stretch")->storeResized("thumb".$w."x".$h);

        $this->restoreState($state);
        $w = 374;
        $h = 250;
        $this->ratioCrop($w, $h)->resizeImage(array($w,$h), "stretch")->storeResized("thumb".$w."x".$h);

        $this->restoreState($state);
        $w = 414;
        $h = 166;
        $this->ratioCrop($w, $h)->resizeImage(array($w,$h), "stretch")->storeResized("thumb".$w."x".$h);

        $this->restoreState($state);
        $w = 441;
        $h = 166;
        $this->ratioCrop($w, $h)->resizeImage(array($w,$h), "stretch")->storeResized("thumb".$w."x".$h);

        $this->restoreState($state);
        $w = 504;
        $h = 306;
        $this->ratioCrop($w, $h)->resizeImage(array($w,$h), "stretch")->storeResized("thumb".$w."x".$h);

        $this->restoreState($state);
        $w = 551;
        $h = 306;
        $this->ratioCrop($w, $h)->resizeImage(array($w,$h), "stretch")->storeResized("thumb".$w."x".$h);

        $this->restoreState($state);
        $w = 570;
        $h = 378;
        $this->ratioCrop($w, $h)->resizeImage(array($w,$h), "stretch")->storeResized("thumb".$w."x".$h);

        $this->restoreState($state);

        return $this;
    }

    /**
     * Determines whether the conversion succeeded
     *
     * @return boolean
     */
    public function conversionSuccess()
    {
        if (count($this->resized)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Stores the current working resource in $this->resized[$name]
     *
     * @param string $name
     * @return \Eardish\ImageProcessingService\ImageTools\ImageProcessor
     */
    public function storeResized($name)
    {
        $this->resized[$name] = $this->imageResource;

        return $this;
    }

    /**
     * Generates a save state of the current working resource
     *
     * @return type
     */
    public function storeState()
    {
        $state['imageResource'] = $this->imageResource;
        $state['params'] = $this->params;
        $state['source'] = $this->source;

        return $state;
    }

    /**
     * Restores from a state array generated by $this->storeState()
     *
     * @param array $state
     * @return \Eardish\ImageProcessingService\ImageTools\ImageProcessor
     */
    public function restoreState($state)
    {
        $this->imageResource = $state['imageResource'];
        $this->params = $state['params'];
        $this->source = $state['source'];

        return $this;
    }

    /**
     * Returns the $this->resized array of thumb resources
     *
     * @return array
     */
    public function getResizedResources()
    {
        return $this->resized;
    }

    /**
     * Sets the Monolog logger
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function getSource()
    {
        return $this->source;
    }

    ////////////////////////////////////////////////
    // non-public functions
    ///////////////////////////////////////////////

    /**
     * Opens an image with the given source and type.
     *
     * @param $source
     * @param $type
     * @return \Eardish\ImageProcessingService\ImageTools\ImageProcessor
     */
    protected function openImage($source, $type)
    {
        switch ($type) {
            case "url":
                $parts = explode(".", $source);
                $ext = array_pop($parts);
            case "upload":
                $contents = file_get_contents($source);
                $contents = trim($contents);
                if ($contents === false || empty($contents)) {
                    $this->sourceFail = true;

                    return $this;
                } else {
                    $this->sourceFail = false;
                }
                $mime = "";
                if ($type == "upload") {
                    if (function_exists("finfo_open")) {
                        $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $source);
                    } else {
                        $mime = mime_content_type($source);
                    }
                }

                $source_type = ($this->mimeToType($mime)) ? $this->mimeToType($mime) : $ext;
                break;
            case "cloud":

                break;

        }
        $this->imageBinary = $contents;
        $this->source['mime'] = $mime;
        if ($type == "url") {
            $this->source['type'] = $ext;
        } else {
            $this->source['type'] = $source_type;
        }

        return $this;
    }

    /**
     * @param $gd
     * @param $type
     * @param $name
     * @return array
     */
    private function saveGDToTemp($gd, $type, $name)
    {
        // TODO Change this to something more appropriate
        $tmp = __DIR__.'/pictures/';

        switch ($type) {
            case "gif":
                $path = $tmp.$name.".gif";
                $file = $name.".gif";
                imagegif($gd, $path);
                break;

            case "jpg":
                $path = $tmp.$name.".jpg";
                $file = $name.".jpg";
                imagejpeg($gd, $path);
                break;

            case "png":
            default:
                $path = $tmp.$name.".png";
                $file = $name.".png";
                imagepng($gd, $path);
                break;
        }

        return array("path" => $path, "file" => $file);
    }

    /*
     * Crops an image to the provided dimensions in $size, using $offset as the (x,y) offset
     * and sets the new GD resource to $this->imageResource
     *
     * @param array $size An array containing the desired crop size (x, y)
     * @param array $offset Offset for image crop
     * @return \Eardish\ImageProcessingService\ImageTools\ImageProcessor
     */
    private function cropImage($size, $offset = array(0, 0))
    {
        // Create new GD resource
        $canvas = imagecreatetruecolor($size[0], $size[1]);

        // Copy working resource into the new resource
        imagecopy($canvas, $this->imageResource, 0, 0, $offset[0], $offset[1], $this->params['x'], $this->params['y']);

        // Set the working resource to the new resource
        $this->imageResource = $canvas;

        // Set parameters for the new working resource
        $this->setParams();

        return $this;
    }

    private function mimeToType($mime)
    {
        switch ($mime) {

            case "image/gif":
                return "gif";
                break;
            case "image/jpeg":
                return "jpg";
                break;
            case "image/png":
                return "png";
                break;
            default:
                return false;
                break;

        }
    }

    private function applyRatio($dimensions, $ratios)
    {
        $new['x'] = round($dimensions[0]/$ratios[0]);
        $new['y'] = round($dimensions[1]/$ratios[1]);

        return $new;
    }

    /*
     * Sets the image parameters for the given resource
     *
     * @param A GD image resource; if none is provided assumes $this->imageResource
     */
    private function setParams($resource = null)
    {
        if (is_null($resource)) {
            $this->params = $this->generateParams($this->imageResource);
        } else {
            $this->params = $this->generateParams($resource);
        }

        return $this;
    }

    /**
     * Returns the tmpFiles array that has information about where converted images were stored and what they are called.
     *
     * @return array
     */
    public function getTmpFiles()
    {
        return $this->tmpFiles;
    }

    /*
     * Generates image paramters from a resource
     *
     * @param $resource A GD image resource
     * @return Array
     */
    private function generateParams($resource)
    {
        $params['x'] = imagesx($resource);
        $params['y'] = imagesy($resource);
        $params['truecolor'] = imageistruecolor($resource);

        return $params;
    }
}
