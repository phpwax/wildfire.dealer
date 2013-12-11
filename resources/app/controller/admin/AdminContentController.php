<?php

class AdminContentController extends CMSAdminContentController {


  public function events() {
    WaxEvent::add("cms.layout.sublinks", function(){
      $obj = WaxEvent::data();
      $obj->quick_links = array();
    });
  }

}