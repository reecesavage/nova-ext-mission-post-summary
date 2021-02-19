<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH.'core/libraries/Nova_controller_main.php';

class __extensions__nova_ext_mission_post_summary__Ajax extends Nova_controller_main
{
	public function __construct()
	{
		parent::__construct();
	}
  
  public function mission()
  { 
	  $data['status']='NOK';
    $id= $this->input->get('mission', TRUE);
   if(!empty($id))
   {
    $query = $this->db->get_where('missions', array('mission_id' => $id));
    $post = ($query->num_rows() > 0) ? $query->row() : false;
    $data['status']='OK';
    $data['post']=$post;
   }
   echo json_encode($data);exit;
   
  }
}
