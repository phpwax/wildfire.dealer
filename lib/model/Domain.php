<?
class Domain extends WaxModel{

  public function setup(){
    $this->define("webaddress", "CharField", array('scaffold'=>true, 'group'=>'content', 'primary_group'=>1));
    $this->define("status", "BooleanField", array('label'=>'Live','scaffold'=>true, 'group'=>'content', 'primary_group'=>1));
    if(constant("DEALER_MODEL")) $this->define("dealers", "ManyToManyField", array('target_model'=>DEALER_MODEL,'scaffold'=>true, 'group'=>'relationships'));
    $this->define("is_primary", "BooleanField", array('scaffold'=>true, 'group'=>'content', 'primary_group'=>1));
  }

  public function before_save(){
    if(!$this->webaddress) $this->webaddress = "example.com";
  }
}
?>
