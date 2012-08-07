<?php
class ChildContent extends HasManyField {

	public $default_scope = "live";


	public function get($filters = false) {
    $target = new $this->target_model;
    $target->scope($this->default_scope);
    if($filters["order"]) $target->order($filters["order"]);
    if($filters["filter"]) $target->filter($filters["filter"]);
    if($this->eager_loading) return $this->eager_load($target);
    return $this->lazy_load($target);
  }

  //overwrite the lazy load to also fetch all of the content of the national_content as well
  public function lazy_load($target) {
    $data = parent::lazy_load($target);
    if($this->model->row['dealer_content_id'] ){
      $col = $this->model->get_col("national_content");
      $name = $col->col_name;
      $val = $this->model->{$col->col_name};
      $class = get_class($this->model);
      $model = new $class($this->default_scope);
      $kid_ids = array(0);
      foreach($model->filter("id", $this->model->primval, "!=")->filter("parent_id", $val)->filter("for_dealer", 1)->all() as $pg) $kid_ids[] = $pg->primval;
      $ids = array_merge((array)$data->rowset, (array)$kid_ids);
      $model = new $class("live");
      return $model->filter("id", $ids)->all();
    }
    return $data;
  }


}
