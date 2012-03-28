<?

class Dealer extends VehicleBaseModel{


  public function setup(){
    $this->define("brand", "ForeignKey", array('target_model'=>'Brand', 'required'=>true, 'scaffold'=>true) );
    $this->define("client_id", "CharField", array('scaffold'=>true) );

    parent::setup();

    $this->define("address_line_1", "CharField", array('maxlength'=>255, 'required'=>true, 'group'=>'contact') );
    $this->define("address_line_2", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("address_line_3", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("city", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("county", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("postal_code", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("telephone", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("fax", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("email", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("opening_times", "TextField", array('widget'=>"TinymceTextareaInput", 'group'=>'contact'));

    $this->columns['status'][1]['group'] = 'status';
    $this->define("sales", "BooleanField", array('maxlength'=>2,'group'=>'status') );
    $this->define("service", "BooleanField", array('maxlength'=>2,'group'=>'status') );
    $this->define("parts", "BooleanField", array('maxlength'=>2,'group'=>'status') );

    $this->define("lat", "CharField", array('editable'=>false));
    $this->define("lng", "CharField", array('editable'=>false));
    $this->define("website", "CharField", array('editable'=>false));
    $this->define("api_status", "BooleanField", array('editable'=>false));
    $this->define("domains", "ManyToManyField", array('target_model'=>'Domain', 'group'=>'relationships'));

    if(constant("CONTENT_MODEL")) $this->define("pages", "ManyToManyField", array('target_model'=>CONTENT_MODEL, 'group'=>'relationships'));
  }




}

?>