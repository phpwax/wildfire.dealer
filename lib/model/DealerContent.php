<?
class DealerContent extends WildfireContent{
  public $table = "wildfire_content";
  public static $page_types = array('test');
  public function setup(){
    parent::setup();

    $this->define("homepage_item", "BooleanField", array('group'=>'homepage banner'));
    $this->define("homepage_title", "CharField", array('group'=>'homepage banner'));
    $this->define("homepage_copy", "TextField", array('widget'=>"TinymceTextareaInput", 'group'=>'homepage banner'));
    $this->define("homepage_text_1", "CharField", array('group'=>'homepage banner'));
    $this->define("homepage_link_1", "CharField", array('group'=>'homepage banner'));
    $this->define("homepage_text_2", "CharField", array('group'=>'homepage banner'));
    $this->define("homepage_link_2", "CharField", array('group'=>'homepage banner'));
    $this->define("homepage_text_3", "CharField", array('group'=>'homepage banner'));
    $this->define("homepage_link_3", "CharField", array('group'=>'homepage banner'));

    $this->define("map", "CharField", array('group'=>'google map','widget'=>'SelectInput', 'choices'=>array(''=>'-- Select --', 'small'=>'Small', 'large'=>'Large')));
    $this->define("postcode", "CharField", array('group'=>'google map'));
    $this->define("lat", "CharField", array('group'=>'google map'));
    $this->define("lng", "CharField", array('group'=>'google map'));

    $this->define("page_type", "CharField", array('group'=>'advanced', 'widget'=>'SelectInput', 'choices'=>self::page_types() ));

    $this->define("brand", "ForeignKey", array('target_model'=>'Brand', 'group'=>'relationships'));
    $this->define("model", "ForeignKey", array('target_model'=>'Model', 'group'=>'relationships'));
    $this->define("derivative", "ForeignKey", array('target_model'=>'Derivative', 'group'=>'relationships'));
    $this->define("national_content", "ForeignKey", array('group'=>'advanced', "target_model" => get_parent_class($this) ));
    $this->define("for_dealer", "BooleanField", array('group'=>'advanced', 'default'=>1)); //turn it on by default

    $this->define("tracking", "ManyToManyField", array('group'=>'relationships', 'target_model'=>'Tracking'));
    $this->define("conversion_code", "TextField", array('group'=>'advanced'));
  }

  public function tree_setup(){
		parent::tree_setup();
		$this->define("children", "ChildContent", array("target_model" => get_class($this), "join_field" => $this->parent_join_field));
	}

  public function before_save(){
    parent::before_save();
    if($this->postcode && !$this->lat) $this->coords();
  }

  public function coords(){
     $coords = geo_locate($this->postcode, Config::get("map_key"));
     $this->lat = $coords['lat'];
     $this->lng = $coords['lng'];
    return $this;
  }

  public function css_selector(){
    if($this->model) return $this->model->css_selector();
    return str_replace("/", "-", trim($this->permalink, "/"));
  }
  /**
   * anything in the page directory with __ at the start will be added as an optional display type
   */
  public static function page_types(){
    $pattern = VIEW_DIR."page/__*.html";
    $options = array(""=>"-- select --");
    foreach(glob($pattern) as $file) $options[ltrim(str_replace(".html", "", str_replace(VIEW_DIR."page", "", $file)),"/")] = ucwords(str_replace("_", " ", str_replace("/", "", basename($file, ".html"))));
    return $options;
  }

  public function domain_permalink(){
    if(($dealers = $this->dealers) && ($dealer = $dealers->first()) && ($domains = $dealer->domains) && ($domain = $domains->filter("status",1)->first())) return "//".$domain->webaddress;
    return false;
  }
  public function permalink($dealer=false){
    if($dealer){
      if($dealer->domain_permalink()) return "/".trim(str_replace($dealer->permalink, "", $this->permalink), "/")."/";
      return str_replace("//", "/", $dealer->permalink . trim(str_replace($dealer->permalink, "", $this->permalink), "/")."/");
    }
    else return $this->permalink;
  }

  public function has_children($dealer = false){
    if(($this->dealer_content_id && ($pg = $this->national_content) && $pg->has_children()) || parent::has_children()) return true;
    return false;
  }
}
