<?php
require_once(dirname(__FILE__).'/ANYPOPUP.php');
class ANYPOPUPVideoPopup extends ANYPOPUP {
	public $video;
	protected $width;
	protected $height;
	public $videoOptions;
	
	public function setUrl($video) {
		$this->video = $video;
	}
	
	
	public function getRealUrl() {
		return $this->video;
	}
	
	public function setWidth($width) {
		$this->width = $width;
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function setHeight($height) {
		$this->height = $height;
	}
	
	public function getHeight() {
		return $this->height;
	}
	
	
	public function setVideoOptions($options)
	{
		$this->videoOptions = $options;
	}

	public function getVideoOptions()
	{
		return $this->videoOptions;
	}
	
	
	public static function create($data, $obj = null) {
		$obj = new self();
		
		$options = json_decode($data['options'], true);
		$videoOptions = $options['videoOptions'];
		
		$obj->setVideoOptions($videoOptions);
		
		$obj->setUrl($data['video']);
		$obj->setWidth($data['width']);
		$obj->setHeight($data['height']);

		parent::create($data, $obj);

	}
	public function save($data = array()) {

		$editMode = $this->getId()?true:false;

		$res = parent::save($data);

		if ($res===false) return false;
		
		$url = $this->getRealUrl();
		$videoOptions = $this->getVideoOptions();
		
		global $wpdb;
		if ($editMode) {
			
			$sqlUp = $wpdb->prepare("UPDATE ". $wpdb->prefix ."anypopup_video_popup SET url=%s, options=%s WHERE id=%d",$url,$videoOptions,$this->getId());
			$res = $wpdb->query($sqlUp);
		}
		else {
			$sql = $wpdb->prepare( "INSERT INTO ". $wpdb->prefix ."anypopup_video_popup (id, url, options) VALUES (%d, %s, %s)",$this->getId(),$url,$videoOptions);
			$res = $wpdb->query($sql);
		}
		return $res;
	}
	protected function setCustomOptions($id) {
		global $wpdb;

		$st = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."anypopup_video_popup WHERE id = %d",$id);
		
		$arr = $wpdb->get_row($st,ARRAY_A);
		$this->setUrl($arr['url']);
		$this->setVideoOptions($arr['options']);

	}

	protected function getExtraRenderOptions() {
		$popupId = (int)$this->getId();
		$options = json_decode($this->getVideoOptions(), true);
		$vidType = $options['video-type'];
		$autoplay = $options['video-autoplay'];
		$fullscreen = $options['video-fullscreen'];
		
		$allowfullscreen = '';
		if($fullscreen == 'on'){
			echo $allowfullscreen = 'allowfullscreen';
		}
		
		$allowautoplay = '';
		if($autoplay == 'on'){
			echo $allowautoplay = 'autoplay';
		}
		$Vwidth = $this->getWidth();
		$Vheight = $this->getHeight();
		$Vwidth = str_replace("px","",$Vwidth);
		$Vheight = str_replace("px","",$Vheight);
		$url = $this->getRealUrl();
		parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars ); 
	
		if($vidType == 'youtube'){
			
			$content = '<iframe width="'.$Vwidth.'" height="'.$Vheight.'" src="https://www.youtube.com/embed/'.$my_array_of_vars['v'].'" frameborder="0" allow="autoplay; encrypted-media" '.$allowfullscreen.'></iframe>';
		
		}
		else{
			
			$content = '<video width="'.$Vwidth.'" height="'.$Vheight.'" '.$allowautoplay.' controls>
			  <source src="'.$url.'" type="video/mp4">
			  Your browser does not support the video tag.
			</video>';
		}
		
		$this->anypopupAddPopupContentToFooter($content, $popupId);

		return  array('html'=> $content);
	}

	public  function render() {
		return parent::render();
	}
}