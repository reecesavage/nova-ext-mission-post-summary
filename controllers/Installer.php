<?php
namespace nova_ext_mission_post_summary;


class Installer {



function __construct() {

  


    $this->ci =& get_instance();
   // $this->install();

  }

 public function install() {



    $this->ci->load->model('menu_model');


    $expectedLink = 'extensions/nova_ext_mission_post_summary/Manage/config';
    $cat = $this->ci->menu_model->get_menu_category( 'manageext' );
   
    if ( $cat === false ) {
      // Add the category and the menu items
      $insertCat = $this->ci->menu_model->add_menu_category( [
        'menucat_menu_cat' => 'manageext',
        'menucat_name' => 'Manage Extensions',
        'menucat_type' => 'adminsub',
        'menucat_order' => 7
      ] );

      }

      // Add item
  

      $query = $this->ci->db->get_where('menu_items', array('menu_name' => 'Ordered Mission Posts Summary'));
    $item = ($query->num_rows() > 0) ? $query->row() : false;   
      if($item==false){
      $insertItem = $this->ci->menu_model->add_menu_item( [
        'menu_name' => 'Ordered Mission Posts Summary',
        'menu_group' => 0,
        'menu_order' => 0,
        'menu_sim_type' => 1,
        'menu_link' => $expectedLink,
        'menu_link_type' => 'onsite',
        'menu_need_login' => 'none',
        'menu_use_access' => 'y',
        'menu_access' => 'site/settings',
        'menu_access_level' => 0,
        'menu_display' => 'y',
        'menu_type' => 'adminsub',
        'menu_cat' => 'manageext',
      ] );
    }
    
  }

}