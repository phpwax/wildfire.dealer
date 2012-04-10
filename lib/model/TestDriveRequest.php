<?php
class TestDriveRequest extends WaxModel{

  public function setup(){
    $this->define("title", "CharField", array("required"=>1, 'scaffold'=>true, 'export'=>true, 'widget'=>'SelectInput', 'choices'=>array('mr'=>'Mr','miss'=>'Miss', 'mrs'=>'Mrs')));
    $this->define("first_name", "CharField", array("required"=>1, 'scaffold'=>true, 'export'=>true));
    $this->define("last_name", "CharField", array("required"=>1, 'scaffold'=>true, 'export'=>true));
    $this->define("postcode", "CharField", array("required"=>1, 'scaffold'=>true, 'export'=>true));
    $this->define("address", "TextField", array('export'=>true, 'required'=>true));
    $this->define("email_address", "CharField", array("required"=>1, 'export'=>true));
    $this->define("telephone_number", "CharField", array("required"=>1, 'export'=>true));

    $this->define("purchase_estimation", "CharField", array("label"=>"I am thinking of purchasing my next vehicle in:", 'export'=>true));
    $this->define("contact", "BooleanField", array("widget"=>"CheckboxInput", "label"=>"I would like you to inform me about its vehicles by email", 'export'=>true));

    $this->define("model", "ForeignKey", array("required"=>1, "widget"=>"HiddenInput", 'target_model'=>'Model', 'scaffold'=>true, 'export'=>true));

    $this->define("dealer", "ForeignKey", array('target_model'=>'Dealer', 'widget'=>'HiddenInput', 'scaffold'=>true, 'export'=>true));
    //analytics tracking of urls
    $this->define("utm_source", "CharField", array('widget'=>'HiddenInput'));
    $this->define("utm_campaign", "CharField", array('widget'=>'HiddenInput'));
    $this->define("utm_medium", "CharField", array('widget'=>'HiddenInput'));
    parent::setup();
  }
}
?>