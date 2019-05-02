<?php
class Marker{
	protected $size = 16;
	protected $markersize = 204;
	protected $bordersize = 102;
	public $msg;
	public $status;
	public $stringpatt;
	protected $stringpattR;
	protected $stringpattG;
	protected $stringpattB;

	public function __construct($file) {
		if (file_exists($file)) {
			$this->file = $file;

			$info = getimagesize($file);

			if (!$info){
				$this->status = 'error';
				$this->msg = 'File is not an image';
				return;
			}

			$this->width  = $info[0];
			$this->height = $info[1];
			$this->bits = isset($info['bits']) ? $info['bits'] : '';
			$this->mime = isset($info['mime']) ? $info['mime'] : '';

			if (!in_array($this->mime, array(
				'image/gif',
				'image/png',
				'image/jpeg'
			))){
				$this->status = 'error';
				$this->msg = 'File is not a valid image';
				return;
			}

			if ($this->mime == 'image/gif') {
				$this->image = imagecreatefromgif($file);
			} elseif ($this->mime == 'image/png') {
				$this->image = imagecreatefrompng($file);
			} elseif ($this->mime == 'image/jpeg') {
				$this->image = imagecreatefromjpeg($file);
			}
		} else {
			$this->status = 'error';
			$this->msg = 'Image file does not exist';
			return;
		}
	}

	/***********************************/
	/*    Pattern related functions    */
	/***********************************/

	public function resize(){
		$this->newimage = imagecreatetruecolor($this->size, $this->size);
		imagecopyresampled($this->newimage, $this->image, 0, 0, 0, 0, $this->size, $this->size, $this->width, $this->height);
		return $this;
	}

	public function rotate($g){
		$this->newimage = imagerotate($this->newimage, $g, 0);
		return $this;
	}

	public function fill(){
		$this->stringpattR = '';
		$this->stringpattG = '';
		$this->stringpattB = '';
		for ($linha = 0; $linha < $this->size; $linha++) {
			for ($coluna = 0; $coluna < $this->size; $coluna++) {
				$rgb = imagecolorat($this->newimage, $coluna, $linha);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				$this->stringpattR .= (($coluna>0)?' ':NULL).((strlen($r)==1)?'  ':NULL).((strlen($r)==2)?' ':NULL).$r;
				$this->stringpattG .= (($coluna>0)?' ':NULL).((strlen($g)==1)?'  ':NULL).((strlen($g)==2)?' ':NULL).$g;
				$this->stringpattB .= (($coluna>0)?' ':NULL).((strlen($b)==1)?'  ':NULL).((strlen($b)==2)?' ':NULL).$b;
			}
			$this->stringpattR.="\n";
			$this->stringpattG.="\n";
			$this->stringpattB.="\n";
		}
		$this->stringpatt .= $this->stringpattB.$this->stringpattG.$this->stringpattR."\n";
		return $this;
	}

	public function map(){
		$this->fill();
		$this->rotate(90);
		$this->fill();
		$this->rotate(90);
		$this->fill();
		$this->rotate(90);
		$this->fill();

		$this->stringpatt = substr($this->stringpatt, 0, strlen($this->stringpatt)-1);

		return $this;
	}

	public function getPatt(){
		$this->resize();
		$this->map();
		return $this->stringpatt;
	}
	public function savePatt($filepath){
		$this->getPatt();
		file_put_contents($filepath, $this->stringpatt);
		return $this;
	}

	/***********************************/
	/*     Marker related functions    */
	/***********************************/

	public function addBorder($r,$g,$b){
		$im = $this->newimage;
		$width=ImageSx($im);
		$height=ImageSy($im);
		$border=$this->bordersize;

		$img_adj_width=$width+(2*$border);
		$img_adj_height=$height+(2*$border);
		$newimage=imagecreatetruecolor($img_adj_width,$img_adj_height);

		$border_color = imagecolorallocate($newimage, $r,$g,$b);
		imagefilledrectangle($newimage,0,0,$img_adj_width,$img_adj_height,$border_color);

		imageCopyResized($newimage,$im,$border,$border,0,0,$width,$height,$width,$height);

		$this->newimage = $newimage;
		return $this;
	}

	public function getMarker(){
		$this->newimage = imagecreatetruecolor($this->markersize, $this->markersize);
		imagecopyresampled($this->newimage, $this->image, 0, 0, 0, 0, $this->markersize, $this->markersize, $this->width, $this->height);

		$this->addBorder(0,0,0);
		$this->addBorder(255,255,255);
		return $this->newimage;
	}
	public function saveMarker($filepath){
		$this->getMarker();
		imagejpeg($this->newimage,$filepath);
	}
}
