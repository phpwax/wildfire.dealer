<?php
class CMSAdminAnalyticsController extends AdminComponent{
  public $module_name = "analytics";
  public $model_class = 'Dealer';
  public $dashboard = false;


  public function index(){
    //print_r($this);exit;
    $this->use_view = "analytics";
    $order_by = "ga:visits";
    $start_date = date("Y-m")."-01";

    if($_POST){
      if(($order = $_POST["filter"]["order"]) && in_array($order,array("ga:visits","ga:pageviews"))){
        $order_by = $order;
      }
      if($date = $_POST["filter"]["date"]){
        $start_date = date("Y-m-d",mktime(1,0,0,date("n",strtotime($date)),1,date("Y",strtotime($date))));
        $end_date = date('Y-m-d',mktime(1,0,0,date("n",strtotime($date))+1,-1,date("Y",strtotime($date))));
        $this->date = $date;
      }
    }
    
    $cache_file = CACHE_DIR."analytics";
    $analytics = Config::get("analytics");
    $api = new GoogleAnalytics();
    if(count($analytics) && ($login = $api->login($analytics['email'], $analytics['password']))){
      $dealer_model = new $this->model_class;
      $dealers = $dealer_model->filter("status",1)->all();
      $visit_data = array();

      foreach($dealers as $d){
        if($d->analytics_id){
          $analytics = new DealerAnalytics;
          if($tmp_analytics = $analytics->filter(array("analytics_id"=>$d->analytics_id,"date"=>date("Y-m-d H:i:s",strtotime($start_date))))->first()){

            $visit_data[$d->client_id]["dealer"] = $d;
            $visit_data[$d->client_id]["data"][date("m",strtotime($start_date))]["ga:visits"] = $tmp_analytics->visits;
            $visit_data[$d->client_id]["data"][date("m",strtotime($start_date))]["ga:pageviews"] = $tmp_analytics->pageviews;

          }elseif($tmp = $api->data($d->analytics_id, 'ga:month', 'ga:visits,ga:pageviews', '-ga:month', $start_date, $end_date, 1)){

            //if not current month cache the results in db
            if($start_date != date("Y-m")."-01"){
              $analytics = new DealerAnalytics;

              $analytics->analytics_id = $d->analytics_id;
              $analytics->tracker_id = $d->analytics_tracker_id;
              $analytics->date = date("Y-m-d H:i:s",strtotime($start_date));
              $analytics->visits = $tmp[date("m",strtotime($start_date))]["ga:visits"];
              $analytics->pageviews = $tmp[date("m",strtotime($start_date))]["ga:pageviews"];
              $analytics->dealer = $d;
            }

            $visit_data[$d->client_id]["dealer"] = $d;
            $visit_data[$d->client_id]["data"] = $tmp;
          }
        }
      }
      uasort($visit_data,function($a,$b) use ($order_by,$start_date){
        $date = date("m",strtotime($start_date));
        if($a["data"][$date][$order_by]<$b["data"][$date][$order_by]) return 1;
        elseif($a["data"][$date][$order_by]>$b["data"][$date][$order_by]) return -1;
        else return 0;
      });
      $this->visit_data = $visit_data;
    }
  }

}
?>