<?
class Domain extends WaxModel{

  public function setup(){
    $this->define("webaddress", "CharField", array('scaffold'=>true));
    $this->define("status", "BooleanField", array('label'=>'Live','scaffold'=>true));
    if(constant("DEALER_MODEL")) $this->define("dealers", "ManyToManyField", array('target_model'=>DEALER_MODEL,'scaffold'=>true, 'group'=>'relationships'));
  }

  public function before_save(){
    if(!$this->webaddress) $this->webaddress = "example.com";
  }
}
?>