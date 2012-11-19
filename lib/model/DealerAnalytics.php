<?php
class DealerAnalytics extends WaxModel{
  public function setup(){
    $this->define("tracker_id","CharField");
    $this->define("analytics_id","CharField");
    $this->define("date","DateTimeField");
    $this->define("visits","IntegerField");
    $this->define("pageviews","IntegerField");
    $this->define("dealer","ForeignKey",array("target_model"=>"IsuzuDealer"));
  }
}
?>