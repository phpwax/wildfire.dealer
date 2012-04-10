<?
class DealerContent extends WildfireContent{
  public $table = "wildfire_content";
  public static $page_types = array('test');
  public function setup(){
    parent::setup();
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


  public function domain_permalink(){
    if(($dealers = $this->dealers) && ($dealer = $dealers->first()) && ($domains = $dealer->domains) && ($domain = $domains->filter("status",1)->first())) return "//".$domain->webaddress;
    return false;
  }
  public function permalink($dealer=false){
    if($dealer){
      if($dealer->domain_permalink()) return "/".trim(str_replace($dealer->permalink, "", $this->permalink), "/")."/";
      return $dealer->permalink . trim(str_replace($dealer->permalink, "", $this->permalink), "/")."/";
    }
    else return $this->permalink;
  }

  public function has_children($dealer = false){
    if(($this->dealer_content_id && ($pg = $this->national_content) && $pg->has_children()) || parent::has_children()) return true;
    return false;
  }
}
