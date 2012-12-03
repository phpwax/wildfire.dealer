<?php
class CMSAdminDealerController extends AdminComponent{
  public $module_name = "dealer";
  public $model_class = 'Dealer';
  public $display_name = "Dealers";
  public $dashboard = false;
  public $tree_layout = false;
  public $filter_fields=array(
                            'text' => array('columns'=>array('title', 'client_id', 'postal_code', 'email'), 'partial'=>'_filters_text', 'fuzzy'=>true)
                          );
}
?>