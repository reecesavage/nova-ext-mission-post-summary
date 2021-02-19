<?php 

$this->event->listen(['location', 'view', 'data', 'admin', 'manage_posts_edit'], function($event){
  
$id = (is_numeric($this->uri->segment(4))) ? $this->uri->segment(4) : false;
  $post = $id ? $this->posts->get_post($id) : null;
    
   

    $extConfigFilePath = dirname(__FILE__).'/../config.json';
         
        if ( file_exists( $extConfigFilePath ) ) { 
            $file = file_get_contents( $extConfigFilePath );
            $json = json_decode( $file, true );
    }
    
  $summaryLabel = isset($json['nova_ext_mission_post_summary']['nova_ext_mission_post_summary'])
                        ? $json['nova_ext_mission_post_summary']['nova_ext_mission_post_summary']['value']
                        : 'Summary';


       $event['data']['label']['nova_ext_mission_post_summary'] = $summaryLabel;
      $event['data']['inputs']['nova_ext_mission_post_summary'] = array(
        'name' => 'nova_ext_mission_post_summary',
        'id' => 'nova_ext_mission_post_summary',
        'rows'=>isset($json['setting']['rows'])
                        ? $json['setting']['rows']
                        : '5',
        'value' => $post ? $post->nova_ext_mission_post_summary : ''
      );


});

$this->event->listen(['location', 'view', 'output', 'admin', 'manage_posts_edit'], function($event){

    $event['output'] .= $this->extension['jquery']['generator']
                      ->select('#content-textarea')->closest('p')
                      ->before(
                        $this->extension['nova_ext_mission_post_summary']
                             ->view('form', $this->skin, 'admin', $event['data'])
                      );

});