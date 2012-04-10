<?php
class BrochureRequest extends TestDriveRequest{

  public function setup(){
    parent::setup();
    $this->columns['postcode'][1]['required'] = false;
    $this->columns['address'][1]['required'] = false;
    $this->columns['telephone_number'][1]['required'] = false;
    $this->define("delivery", "CharField", array('scaffold'=>true,"required"=>1, "widget"=>"RadioInput", "choices"=>array('download'=>"Download", 'postal'=>"Send in the post"), 'export'=>true));
  }

  public function before_save(){
    if($this->delivery == "postal" && !$this->address) $this->add_error("address", "Address is a required field for postal brochures");
    if($this->delivery == "postal" && !$this->postcode) $this->add_error("postcode", "Postcode is a required field for postal brochures");
  }
}