<?php 

$this->require_extension('jquery');




$this->event->listen(['template', 'render', 'data'], function($event){

	
    $event['data']['javascript'] 
        .= $this->extension['nova_ext_mission_post_summary']->inline_js('custom', 'admin');
});


require_once dirname(__FILE__).'/events/db.php';
require_once dirname(__FILE__).'/events/location_admin_write_missionpost.php';
require_once dirname(__FILE__).'/events/location_admin_manage_posts_edit.php';
require_once dirname(__FILE__).'/events/location_admin_add_mission.php';
require_once dirname(__FILE__).'/events/parser_parse_string_nova_missionpost.php';
require_once dirname(__FILE__).'/events/location_main_sim_viewpost.php';


require_once dirname(__FILE__) . '/controllers/Installer.php';
$manager = ( new \nova_ext_mission_post_summary\Installer() )->install();
