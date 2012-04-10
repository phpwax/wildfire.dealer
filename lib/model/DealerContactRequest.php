<?
class DealerContactRequest extends WaxModel{

  public function setup(){
    $this->define("first_name", "CharField", array('required'=>true));
    $this->define("last_name", "CharField", array('required'=>true));
    $this->define("email_address", "EmailField", array('required'=>true));
    $this->define("telephone", "CharField");
    $this->define("message", "TextField");
    $this->define("dealership", "ForeignKey", array('target_model'=>'Dealer', 'widget'=>'HiddenInput'));
    //analytics tracking of urls
    $this->define("utm_source", "CharField", array('widget'=>'HiddenInput'));
    $this->define("utm_campaign", "CharField", array('widget'=>'HiddenInput'));
    $this->define("utm_medium", "CharField", array('widget'=>'HiddenInput'));
    parent::setup();
  }
}
?>