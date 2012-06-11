<?php

class LocalDealerController extends CMSApplicationController{
  public $cms_content_class = "DealerContent";
  public $domain_class = "Domain";
  public $dealer_class = "Dealer";
  public $dealer_site_id = "dealer";
  public $dealer_site_layout = "dealer";


  //hook in to the 404 error state of cms content to hunt out dealer based urls
  protected function event_setup(){
    parent::event_setup();
    //look for cms content by calling functions etc
    WaxEvent::clear("cms.content.lookup");
    WaxEvent::add("cms.content.lookup", function(){
      $obj = WaxEvent::data();
      $dealer_lookup = false;
      //check domain name
      $server = $_SERVER['HTTP_HOST'];
      if($server != Config::get("domains/live") && $server != Config::get("domains/dev")){
        $dclass = $obj->domain_class;
        $domain = new $dclass;
        if(
          ($found = $domain->filter("webaddress", $server)->filter("status", 1)->first()) &&
          ($dealer = $found->dealers) &&
          ($dealer = $dealer->first()) &&
          ($page = $dealer->pages) &&
          ($page = $page->scope("live")->first())
        ){
          $obj->dealer = $dealer_lookup = $page;
          $obj->dealer_model = $dealer;
        }
      }
      if(!$dealer_lookup){
        $obj->content_lookup($obj);
        if(in_array("dealers", $obj->cms_stack) && count($obj->cms_stack) > 1){
          $obj->dealer_checked = "/".trim(implode("/", array_slice($obj->cms_stack,0,2)), "/")."/";
          $obj->dealer_check();
        }
      }
      //lookin for dealer, so push the dealer urls on to the stack
      if($dealer_lookup){
        $obj->dealer_checked = $dealer_lookup->permalink;
        $original_stack = $obj->cms_stack;
        foreach(array_reverse(explode("/", trim($dealer_lookup->permalink,"/"))) as $push) array_unshift($obj->cms_stack, $push);
        $obj->content_lookup($obj);
        //this might be one of those magic internal pages then...
        if($obj->cms_throw_missing_content){
          $obj->cms_throw_missing_content = false;
          $obj->cms_stack = $original_stack;
          $obj->content_lookup($obj);
          $obj->canonical_url = "/". trim(implode("/", $original_stack), "/")."/";
        }

      }
      //check for dealer urls /dealer/xx
      if($obj->cms_throw_missing_content) {
        $stack = $obj->cms_stack;
        //if this is a dealer url, then pop off the first 2
        if(array_shift($stack) == "dealers" && (count($stack))){
          $dealer = array_shift($stack);
          $obj->cms_stack = $stack;
          if(!$obj->dealer_checked) $obj->dealer_checked = "/dealers/".$dealer."/";
          $obj->cms_throw_missing_content = false;
          $obj->canonical_url = "/". trim(implode("/", $stack), "/")."/";
          WaxEvent::run("cms.content.lookup", $obj);
        }
      }

      if(($dealer_model = $obj->dealer_model) && defined("UVL")){
        WaxEvent::add("uvl.vehicle.filters", function() use($dealer_model){
          $model = WaxEvent::data();
          $model->filter("dealer_id", $dealer_model->id);
        });
      }
    });
  }

  public function content_lookup($obj){
    //revert to normal
    if(($preview_id = Request::param('preview')) && is_numeric($preview_id) && ($m = new $obj->cms_content_class($preview_id)) && $m && $m->primval){
      $obj->cms_content = $m;
    }elseif($content = $obj->content($obj->cms_stack, $obj->cms_mapping_class, $obj->cms_live_scope, $obj->cms_language_id) ){
      $obj->cms_content = $content;
    }elseif($content = $obj->content($obj->cms_stack, $obj->cms_mapping_class, $obj->cms_live_scope, array_shift(array_keys(CMSApplication::$languages)) )){
      $obj->cms_content = $content;
    }elseif(WaxApplication::is_public_method($obj, "method_missing")){
      return $obj->method_missing();
    }else $obj->cms_throw_missing_content = true;
  }

  //reset the stacks, body ids etc to the dealer
  public function dealer_check(){
    //this is for internals
    if($url = $this->dealer_checked){
      $class = $this->cms_content_class;
      $model = new $class("live");
      if($item = $model->filter("permalink", $url)->first()){
        $this->dealer = $item;
        array_unshift($this->content_object_stack, $item);
        array_unshift($this->content_id_stack, $item->primval);
        $this->body_class = $this->body_id = $this->dealer_site_id;
        $this->use_layout = "dealer";
      }
    }
    //this is for dealer landing page
    foreach($this->content_object_stack as $item){
      if(($dealers = $item->dealers) && $dealers->count()){
        $this->dealer = $item;
        $this->content_object_stack = array($item);
        $this->content_id_stack = array($item->primval);
        $this->body_class = $this->body_id = $this->dealer_site_id;
        $this->use_layout = $this->dealer_site_layout;
        $this->dealer_model = $dealers->first();
      }else if($this->dealer){
        $this->content_id_stack[] = $item->primval;
        $this->content_object_stack[] = $item;
        $css = str_replace("/", "_", trim($item->permalink, "/"));

      }
    }

  }

}

