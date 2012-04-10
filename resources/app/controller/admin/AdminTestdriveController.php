<?php
class AdminTestdriveController extends AdminComponent {
  public $sort_scope = "live";
  public $model_class = 'TestDriveRequest';
  public $module_name = "testdrive";
  public $exportable = true;
  public $export_group = "dealer_id";
  public $dashboard = false;
}
?>