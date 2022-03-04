 <?php 

$this->event->listen(['parser', 'parse_string', 'output', 'write', 'missionpost'], function($event){

       $extConfigFilePath = dirname(__FILE__).'/../config.json';
         
        if ( file_exists( $extConfigFilePath ) ) { 
            $file = file_get_contents( $extConfigFilePath );
            $json = json_decode( $file, true );
    }
           $summaryLabel = isset($json['nova_ext_mission_post_summary']['nova_ext_mission_post_summary'])
                        ? $json['nova_ext_mission_post_summary']['nova_ext_mission_post_summary']['value']
                        : 'Summary';


 if(!empty($this->input->post('nova_ext_mission_post_summary')) && (isset($json['setting']['summary_mode']) && $json['setting']['summary_mode']==1))
 {

     $event['output'] = preg_replace(
                '/'.preg_quote(lang('email_content_post_location')).'.*\<br \/\>/', 
                lang('email_content_post_location').' '.$this->input->post('location').'<br />
                 '.$summaryLabel.': '.$this->input->post('nova_ext_mission_post_summary').'<br> 
                ', 
                $event['output'], 
                1
          );
 }



});



