<?php
class AdminContentController extends CMSAdminContentController {
  public $sortable = true;
  public $sort_scope = "live";
  public $model_class = 'MGContent';
  public $file_tags = array('image',
                            'video',
                            'gallery', //for the gallery, obviously
                            'homepage banner', //for the homepage
                            'homepage logo',
                            'brochure',
                            'banner'
                            );
}
?>