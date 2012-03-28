<?
class Domain extends WaxModel{

  public function setup(){
    $this->define("webaddress", "CharField");
    $this->define("status", "BooleanField", array('label'=>'Live'));
    $this->define("dealers", "ManyToManyField", array('target_model'=>'Dealer'));
  }

  public function before_save(){
    if(!$this->webaddress) $this->webaddress = "example.com";
  }
}
?>