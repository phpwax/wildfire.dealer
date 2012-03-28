<?
class Domain extends WaxModel{

  public function setup(){
    $this->setup("webaddress", "CharField");
    $this->setup("dealers", "ManyToManyField", array('target_model'=>'Dealer'));
  }

  public function before_save(){
    if(!$this->webaddress) $this->webaddress = "example.com";
  }
}
?>