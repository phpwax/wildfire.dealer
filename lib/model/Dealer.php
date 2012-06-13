<?

class Dealer extends VehicleBaseModel{
  public static $allowed_modules = array('home'=>array('index'=>array()),'content'=>array('index'=>array(), 'edit'=>array('details', 'media', 'google map')));
  public static $dealer_homepage_partial = "__dealer_home";
  public static $dealer_contactpage_partial = "__dealer_contact";
  public static $dealer_top_pages = array('/vehicles/', '/news/', '/offers/');
  public static $dealer_extra_pages = array(
    array('title'=>'Contact Us', 'map'=>'large','page_type'=>'__dealer_contact')
  );
  public function setup(){
    $this->define("brand", "ForeignKey", array('target_model'=>'Brand', 'scaffold'=>true) );
    $this->define("client_id", "CharField", array('scaffold'=>true) );

    parent::setup();

    $this->define("address_line_1", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("address_line_2", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("address_line_3", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("city", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("county", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("postal_code", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("telephone", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("fax", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("email", "CharField", array('maxlength'=>255, 'group'=>'contact') );
    $this->define("opening_times", "TextField", array('widget'=>"TinymceTextareaInput", 'group'=>'contact', 'label'=>'Sales opening times'));
    $this->define("service_opening_times", "TextField", array('widget'=>"TinymceTextareaInput", 'group'=>'contact'));
    $this->define("parts_opening_times", "TextField", array('widget'=>"TinymceTextareaInput", 'group'=>'contact'));

    $this->columns['status'][1]['group'] = 'status';
    $this->columns['status'][1]['editable'] = true;
    $this->define("sales", "BooleanField", array('maxlength'=>2,'group'=>'status') );
    $this->define("service", "BooleanField", array('maxlength'=>2,'group'=>'status') );
    $this->define("parts", "BooleanField", array('maxlength'=>2,'group'=>'status') );

    $this->define("lat", "CharField", array('editable'=>false));
    $this->define("lng", "CharField", array('editable'=>false));
    $this->define("website", "CharField", array('editable'=>false));
    $this->define("api_status", "BooleanField", array('editable'=>false));
    $this->define("domains", "ManyToManyField", array('target_model'=>'Domain', 'group'=>'relationships'));

    if(constant("CONTENT_MODEL")) $this->define("pages", "ManyToManyField", array('target_model'=>CONTENT_MODEL, 'group'=>'relationships'));
    $this->define("create_user", "BooleanField", array('group'=>'advanced'));
    $this->define("create_site", "BooleanField", array('group'=>'advanced'));
    $this->define("analytics_tracker_id", "CharField", array('group'=>'advanced'));
    $this->define("analytics_id", "CharField", array('group'=>'advanced'));

    $this->define("youtube_user", "CharField", array('group'=>'advanced'));
    $this->define("twitter_user", "CharField", array('group'=>'advanced'));
    $this->define("facebook_user", "CharField", array('group'=>'advanced'));
  }

  public function before_save(){
    parent::before_save();
    if($this->create_site) $this->dealer_creation();
    if($this->create_user) $this->user_creation();
  }

  //make a new cms user for the dealership
  public function user_creation(){
    $user = new WildfireUser;
    if($this->client_id && (!$found = $user->filter("username", $this->client_id)->first())){
      $user_attrs = array('username'=>$this->client_id, 'firstname'=>$this->title, 'password'=>$this->client_id.date("Y"));
      $user = $user->update_attributes($user_attrs);

      $allowed_modules = Dealer::$allowed_modules;
      foreach(CMSApplication::get_modules() as $name=>$info){
        //if the module isnt listed at all, then block access to it
        if(!$allowed_modules[$name]){
          $block = new WildfirePermissionBlacklist;
          $block->update_attributes(array($user->table."_id"=>$user->primval, 'class'=>$name, 'operation'=>"index"));
        }else{

          $class = "Admin".Inflections::camelize($name,true)."Controller";
          $obj = new $class(false, false);
          $operations = array_merge($obj->operation_actions, array('index'));
          $mods = $allowed_modules[$name];
          $section_class = $obj->model_class;
          $section_model = new $section_class;
          //find all possible tabs for the model
          $tabs = array('details');
          foreach($section_model->columns as $col=>$info) if($info[1]['group']) $tabs[] = strtolower($info[1]['group']);
          $tabs = array_unique($tabs);

          //block operations or tabs
          foreach($operations as $op){
            //if its not set, block it
            if(!isset($mods[$op])){
              $block = new WildfirePermissionBlacklist;
              $block->update_attributes(array($user->table."_id"=>$user->primval, 'class'=>$name, 'operation'=>$op));
            }else{
              //if it is, block tabs that havent been listed
              foreach($tabs as $tab){
                if(!in_array($tab, $mods[$op])){
                  $block = new WildfirePermissionBlacklist;
                  $block->update_attributes(array($user->table."_id"=>$user->primval, 'class'=>$name, 'operation'=>"tab-".$tab));
                }
              }
            }
          }
        }

      }
    }
  }

  //create the dealer section in the cms
  public function dealer_creation(){
    $class = CONTENT_MODEL;
    $model = new $class("live");
    $url = "/dealers/".Inflections::to_url($this->title)."/";
    if(($pages = $this->pages) && $pages->count() || $model->filter("permalink", $url)->first()) return true;
    else{
      //find dealers section

      if($dealers = $model->filter("permalink", "/dealers/")->first()){
        $model = $model->clear();
        //create the first level of this dealer
        $dealer_data = array(
          'title'=>$this->title,
          'layout'=>'dealer',
          'page_type'=>Dealer::$dealer_homepage_partial,
          'content'=>$this->content,
          'parent_id'=>$dealers->primval
        );
        //create the dealer skel
        if($saved = $model->update_attributes($dealer_data)){

          $saved = $saved->generate_permalink()->map_live()->children_move()->show()->save();
          $saved->dealers = $this;
          $this->pages = $saved;
          $subs = array();
          //copy the national main pages
          $i=0;
          foreach(Dealer::$dealer_top_pages as $title=>$skel){
            $look = new $class("live");
            if($found = $look->filter("permalink", $skel)->first()){
              $info = $found->row;
              unset($info['id'], $info['permalink']);
              $info['parent_id'] = $saved->primval;
              $info['sort'] = $i;
              //custom names
              if(!is_numeric($title)) $info['title'] = $title;
              else $info['title'] = str_replace("Latest ", "", $info['title']);
              $info['dealer_content_id'] = $found->primval;
              $subs[] = $page = $look->update_attributes($info)->generate_permalink()->map_live()->children_move()->show()->save();

              //manytomany copy
              foreach($found->columns as $name=>$info) if($info[0] == "ManyToManyField") foreach($found->$name as $assoc) $page->$name = $assoc;

              $i++;
            }
          }
          foreach(Dealer::$dealer_extra_pages as $info){
            $pg = new $class;
            $info['parent_id'] = $saved->primval;
            $info['date_start'] = date("Y-m-d", strtotime("now-1 day"));
            $info['sort'] = $i;
            $pg->update_attributes($info)->generate_permalink()->map_live()->children_move()->show()->save();
            $i++;
          }
        }

      }
    }
    return $this;
  }



}

?>