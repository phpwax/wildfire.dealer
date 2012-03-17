<?
CMSApplication::register_module("dealer", array("display_name"=>"Dealers", "link"=>"/admin/dealer/", 'split'=>true));

if(!defined("CONTENT_MODEL")){
  $con = new ApplicationController(false, false);
  define("CONTENT_MODEL", $con->cms_content_class);
}

WaxEvent::add(CONTENT_MODEL.".setup", function(){
  $model = WaxEvent::data();
  if(!$model->columns['dealers']) $model->define("dealers", "ManyToManyField", array('target_model'=>'Dealer', 'group'=>'relationships'));
});

?>