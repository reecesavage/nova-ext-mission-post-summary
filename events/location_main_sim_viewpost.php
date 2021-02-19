<?php 

$this->event->listen(['location', 'view', 'data', 'main', 'sim_viewpost'], function($event){


  $id = (is_numeric($this->uri->segment(3))) ? $this->uri->segment(3) : false;
  $post = $id ? $this->posts->get_post($id) : null;

    $extensionsConfig = $this->config->item('extensions');

             $extConfigFilePath = dirname(__FILE__).'/../config.json';
         
        if ( file_exists( $extConfigFilePath ) ) { 
            $file = file_get_contents( $extConfigFilePath );
            $json = json_decode( $file, true );
    }
            
          

           $summaryLabel = isset($json['nova_ext_mission_post_summary']['nova_ext_mission_post_summary'])
                        ? $json['nova_ext_mission_post_summary']['nova_ext_mission_post_summary']['value']
                        : 'Summary';




   if(!empty($post->post_mission))
   {
   $query = $this->db->get_where('missions', array('mission_id' => $post->post_mission));

   $model = ($query->num_rows() > 0) ? $query->row() : false;
   if(!empty($model) && $model->mission_ext_mission_post_summary_enable==1)
   {
      

        $event['data']['location'] = "$post->post_location <br> <b>$summaryLabel:</b> $post->nova_ext_mission_post_summary";

   }
   }  
  
});
