<?php
class AdminDomainController extends AdminComponent{
  public $module_name = "domain";
  public $model_class = 'Domain';
  public $display_name = "Dealer Domains";
  public $dashboard = false;
  public $tree_layout = false;
  public $filter_fields=array(
                            'text' => array('columns'=>array('webaddress'), 'partial'=>'_filters_text', 'fuzzy'=>true)
                          );
}
?>