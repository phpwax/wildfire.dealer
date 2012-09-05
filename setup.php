<?
define("DEALERS",true);
CMSApplication::register_module("dealer", array("display_name"=>"Dealers", "link"=>"/admin/dealer/"));
CMSApplication::register_module("domain", array("display_name"=>"Dealer Domains", "link"=>"/admin/domain/", 'split'=>true));
CMSApplication::register_module("testdrive", array("display_name"=>"Test Drive Requests", "link"=>"/admin/testdrive/", 'split'=>true));
CMSApplication::register_module("brochure", array("display_name"=>"Brochure Requests", "link"=>"/admin/brochure/", 'split'=>true));
CMSApplication::register_module("tracking", array("display_name"=>"Tracking", "link"=>"/admin/tracking/", 'split'=>true));


if(!defined("CONTENT_MODEL")){
  $con = new ApplicationController(false, false);
  define("CONTENT_MODEL", $con->cms_content_class);
}

if(!defined("DEALER_MODEL")){
  if(!$con) $con = new ApplicationController(false, false);
  define("DEALER_MODEL", $con->dealer_class);
}

WaxEvent::add(CONTENT_MODEL.".setup", function(){
  $model = WaxEvent::data();
  if(!$model->columns['dealers']) $model->define("dealers", "ManyToManyField", array('target_model'=>DEALER_MODEL, 'group'=>'relationships'));
});
//add a link from the users to a dealership
WaxEvent::add("WildfireUser.setup", function(){
  $obj = WaxEvent::data();
  $obj->define("dealer", "ForeignKey", array('target_model'=>"Dealer", 'group'=>'relationships'));
});
//add in this so it will block all views of the branch & join the created user to the dealership
WaxEvent::add(DEALER_MODEL.".user_creation", function(){
  $dealer = WaxEvent::data();
  $dealer->wu->dealer = $dealer;
});

WaxEvent::add("cms.save.after", function(){
  $data = WaxEvent::data();
  if($data->model_class == DEALER_MODEL && ($model = $data->model) && $model->primval){
    if($model->create_site) $model->dealer_creation();
    if($model->create_user) $model->user_creation();
    if($model->columns['create_branch'] && $model->create_branch) $model->branch_creation();
  }
});

?>