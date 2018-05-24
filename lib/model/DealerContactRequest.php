<?
class DealerContactRequest extends WaxModel{

  public function setup(){
    $this->define("first_name", "CharField", array('required'=>true));
    $this->define("last_name", "CharField", array('required'=>true));
    $this->define("email_address", "EmailField", array('required'=>true));
    $this->define("telephone", "CharField");
    $this->define("message", "TextField");
    $this->define("dealer", "ForeignKey", array('target_model'=>'Dealer', 'widget'=>'HiddenInput'));
    //analytics tracking of urls
    $this->define("utm_source", "CharField", array('widget'=>'HiddenInput'));
    $this->define("utm_campaign", "CharField", array('widget'=>'HiddenInput'));
    $this->define("utm_medium", "CharField", array('widget'=>'HiddenInput'));

    $this->define("page_completed_on", "CharField", array("widget"=>'HiddenInput'));
    $this->define("date_created", "DateTimeField", array("widget"=>'HiddenInput','export'=>true));
    parent::setup();
  }

  public function before_save()
  {
    if(!$this->date_created) $this->date_created = date("Y-m-d H:i:s");
  }
}
?>