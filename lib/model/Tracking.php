<?
class Tracking extends WaxModel{

  public function setup(){
    $this->define("source", "CharField", array('scaffold'=>true));
    $this->define("code", "TextField");
    $this->define("pages", "ManyToManyField", array('target_model'=>'MGContent', 'group'=>'relationships'));
  }

  public function before_save(){
    if(!$this->source) $this->source = "SOURCE";
  }
}
?>