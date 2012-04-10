<?php
class AdminBrochureController extends AdminComponent {
  public $sort_scope = "live";
  public $model_class = 'BrochureRequest';
  public $module_name = "brochure";
  public $exportable = true;
  public $export_group = "dealer_id";
  public $dashboard = false;
}
?>