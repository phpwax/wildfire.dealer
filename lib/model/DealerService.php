<?php

class DealerService extends WaxModel {
  
  
  public function setup() {
    $this->define("title", "CharField", array("required"=>"true"));
    $this->define("key", "CharField", array("required"=>"true"));
    $this->define("cost", "CharField", array("default"=>0));
    $this->define("dealer", "ForeignKey", array("target_model"=>"IsuzuDealer", "editable"=>false));
    $this->define("valid_from", "DateTimeField");    
    $this->define("valid_to", "DateTimeField");    
	  $this->define("status", "IntegerField", array("widget"=>"SelectInput", "choices"=>array(0=>"Not Active",1=>"Active")));
    
  }
  
  
  
  

  

}
