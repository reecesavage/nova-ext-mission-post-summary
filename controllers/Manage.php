<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH.'core/libraries/Nova_controller_admin.php';

class __extensions__nova_ext_ordered_mission_posts__Manage extends Nova_controller_admin
{
	public function __construct()
	{
		parent::__construct();
       
       
      $this->ci =& get_instance();
       $this->_regions['nav_sub'] = Menu::build('adminsub', 'manageext');
		//$this->_regions['nav_sub'] = Menu::build('sub', 'sim');

		
	}




 

  public function writeEmailCode()
  {   
          
        $extControllerPath = APPPATH.'controllers/Write.php';
        if ( !file_exists( $extControllerPath ) ) { 
        return [];
        }
        $controllerFile = file_get_contents( $extControllerPath );
        $pattern = '/protected\sfunction\s_email\(\$type, \$data\)/';
        if (!preg_match($pattern, $controllerFile)) {
       $writeFilePath = dirname(__FILE__).'/../write.txt';
        if ( !file_exists( $writeFilePath ) ) { 
           return [];
        }
        $file = file_get_contents( $writeFilePath );

       $contents = file($extControllerPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      $size = count($contents);
      $contents[$size-1] = "\n".$file;
      $temp = implode("\n", $contents);

     
      file_put_contents($extControllerPath, $temp);
         
         return true;
        }
      return false;
              


  }


  public function writeFeedCode()
  {   
          
        $extControllerPath = APPPATH.'controllers/Feed.php';
        if ( !file_exists( $extControllerPath ) ) { 
        return [];
        }
        $controllerFile = file_get_contents( $extControllerPath );
        $pattern = '/public\sfunction\sposts/';
        if (!preg_match($pattern, $controllerFile)) {
       $writeFilePath = dirname(__FILE__).'/../feed.txt';
        if ( !file_exists( $writeFilePath ) ) { 
           return [];
        }
        $file = file_get_contents( $writeFilePath );

       $contents = file($extControllerPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      $size = count($contents);
      $contents[$size-1] = "\n".$file;
      $temp = implode("\n", $contents);

     
      file_put_contents($extControllerPath, $temp);
         
         return true;
        }
      return false;
              


  }
    


  public function getQuery($switch) {
      
        $prefix= $this->db->dbprefix;
    switch ($switch)
    {
    case 'nova_ext_ordered_post_day':
        $sql="ALTER TABLE {$prefix}posts ADD COLUMN nova_ext_ordered_post_day INTEGER NOT NULL DEFAULT 1";
      break;

       case 'nova_ext_ordered_post_time':
      $sql="ALTER TABLE {$prefix}posts ADD COLUMN nova_ext_ordered_post_time VARCHAR(4) NOT NULL DEFAULT '0000'";
      break;

       case 'nova_ext_ordered_post_date':
       $sql="ALTER TABLE {$prefix}posts ADD COLUMN nova_ext_ordered_post_date VARCHAR(255) DEFAULT NULL";
      break;
       case 'nova_ext_ordered_post_stardate':
       $sql="ALTER TABLE {$prefix}posts ADD COLUMN nova_ext_ordered_post_stardate VARCHAR(255) DEFAULT NULL";
      break;
       case 'mission_ext_ordered_config_setting':
       $sql="ALTER TABLE {$prefix}missions ADD COLUMN mission_ext_ordered_config_setting VARCHAR(255) DEFAULT NULL";
      break;
       case 'mission_ext_ordered_post_numbering':
       $sql="ALTER TABLE {$prefix}missions ADD COLUMN mission_ext_ordered_post_numbering INTEGER NOT NULL DEFAULT 0";
      break;
       case 'mission_ext_ordered_default_mission_date':
       $sql="ALTER TABLE {$prefix}missions ADD COLUMN mission_ext_ordered_default_mission_date VARCHAR(255) DEFAULT NULL";
      break;

       case 'mission_ext_ordered_default_stardate':
       $sql="ALTER TABLE {$prefix}missions ADD COLUMN mission_ext_ordered_default_stardate VARCHAR(255) DEFAULT NULL";
      break;
       case 'mission_ext_ordered_legacy_mode':
       $sql="ALTER TABLE {$prefix}missions ADD COLUMN mission_ext_ordered_legacy_mode INTEGER NOT NULL DEFAULT 0";
      break;
         
          case 'mission_ext_ordered_is_new_record':
       $sql="ALTER TABLE {$prefix}missions ADD COLUMN mission_ext_ordered_is_new_record int(11) DEFAULT 0";
      break;

      
      
    default:
      break;
   }
      return isset($sql)?$sql:'';
  }


	public function saveColumn($requiredPostFields,$requiredMissionFields)
  {   

        if(isset($_POST['submit']) && $_POST['submit']=='Add')
        {  
          $attr= isset($_POST['attribute'])?$_POST['attribute']:'';
          
          if (in_array($attr, $requiredMissionFields['mission']) == true) 
          {
              $table="nova_missions";
             
          }

           if (in_array($attr, $requiredPostFields['post']) == true) 
          {
              $table="nova_posts";
             
          }
          if(!empty($table))
          {
   
            if (!$this->db->field_exists($attr, $table))
            {   
             $sql = $this->getQuery($attr);
            if(!empty($sql))
            { 
              $query=$this->db->query($sql);

              if (($key = array_search($attr, $requiredPostFields['post'])) !== false) {
            unset($requiredPostFields['post'][$key]);
          }

        if (($key = array_search($attr, $requiredMissionFields['mission'])) !== false) {
                unset($requiredMissionFields['mission'][$key]);
          }
             $list['post']=$requiredPostFields;
             $list['mission']=$requiredMissionFields;
            return $list;
            } 
            }

            
          } 
        }

        return false;
       
  }


   public function config()
  {
        Auth::check_access('site/settings'); 
        $data['write']=true;
      $data['feed']=true;
         $requiredPostFields['post']=
           ['nova_ext_ordered_post_day',
       'nova_ext_ordered_post_time',
       'nova_ext_ordered_post_date',
       'nova_ext_ordered_post_stardate'];
 
        $requiredMissionFields['mission']=
            ['mission_ext_ordered_config_setting',
       'mission_ext_ordered_post_numbering',
       'mission_ext_ordered_default_mission_date',
       'mission_ext_ordered_default_stardate',
       'mission_ext_ordered_legacy_mode','mission_ext_ordered_is_new_record'];




        $extFeedControllerPath = APPPATH.'controllers/Feed.php';
         
        if ( !file_exists( $extFeedControllerPath ) ) { 
        return [];
        }
        $file = file_get_contents( $extFeedControllerPath );
        $pattern = '/public\sfunction\sposts/';
        if (!preg_match($pattern, $file)) {
           $data['feed']=false;



        if(isset($_POST['submit']) && $_POST['submit']=='feed')
        {
             
            if($this->writeFeedCode())
            {
              $data['feed']=true;
                $message = sprintf(
               lang('flash_success'),
          // TODO: i18n...
              'Rss Feed Function',
          lang('actions_added'),
          ''
        );
            }else {
                    $message = sprintf(
               lang('flash_failure'),
          // TODO: i18n...
              'Rss Feed Function',
          lang('actions_added'),
          ''
        );
            }
         

        $flash['status'] = 'success';
        $flash['message'] = text_output($message);

        $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);

        }
        }



       
        $extControllerPath = APPPATH.'controllers/Write.php';
         
        if ( !file_exists( $extControllerPath ) ) { 
        return [];
        }
        $file = file_get_contents( $extControllerPath );
        $pattern = '/protected\sfunction\s_email\(\$type, \$data\)/';
        if (!preg_match($pattern, $file)) {
           $data['write']=false;

        if(isset($_POST['submit']) && $_POST['submit']=='write')
        {
             
            if($this->writeEmailCode())
            {
              $data['write']=true;
                $message = sprintf(
               lang('flash_success'),
          // TODO: i18n...
              'Email Function',
          lang('actions_added'),
          ''
        );
            }else {
                    $message = sprintf(
               lang('flash_failure'),
          // TODO: i18n...
              'Email Function',
          lang('actions_added'),
          ''
        );
            }
         

        $flash['status'] = 'success';
        $flash['message'] = text_output($message);

        $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);

        }
        }


            
        if( $list=$this->saveColumn($requiredPostFields,$requiredMissionFields))
        {  

          
           $requiredPostFields=$list['post'];
           $requiredMissionFields=$list['mission'];
              $message = sprintf(
               lang('flash_success'),
          // TODO: i18n...
              'Column Added successfully',
          '',
          ''
        );


        $flash['status'] = 'success';
        $flash['message'] = text_output($message);

        $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);
        }

        $extConfigFilePath = dirname(__FILE__).'/../config.json';
         
        if ( !file_exists( $extConfigFilePath ) ) { 
        return [];
    }
        $file = file_get_contents( $extConfigFilePath );
        $data['jsons'] = json_decode( $file, true );
        $data['title']='Label Configuration';
        if(isset($_POST['submit']) && $_POST['submit']=='Submit')
        {
          $data['jsons']['nova_ext_ordered_mission_posts']['mission_ext_ordered_config_setting']['value']=$_POST['mission_ext_ordered_config_setting'];
          $data['jsons']['nova_ext_ordered_mission_posts']['mission_ext_ordered_post_numbering']['value']=$_POST['mission_ext_ordered_post_numbering'];
          $data['jsons']['nova_ext_ordered_mission_posts']['mission_ext_ordered_default_date']['value']=$_POST['mission_ext_ordered_default_date'];
          $data['jsons']['nova_ext_ordered_mission_posts']['mission_ext_ordered_default_stardate']['value']=$_POST['mission_ext_ordered_default_stardate'];
         $data['jsons']['nova_ext_ordered_mission_posts']['mission_ext_ordered_legacy_mode']['value']=$_POST['mission_ext_ordered_legacy_mode'];
          $data['jsons']['nova_ext_ordered_mission_posts']['label_edit_day']['value']=$_POST['label_edit_day'];
          $data['jsons']['nova_ext_ordered_mission_posts']['label_edit_date']['value']=$_POST['label_edit_date'];
          $data['jsons']['nova_ext_ordered_mission_posts']['label_edit_startdate']['value']=$_POST['label_edit_startdate'];
          $data['jsons']['nova_ext_ordered_mission_posts']['label_edit_time']['value']=$_POST['label_edit_time'];
          $data['jsons']['nova_ext_ordered_mission_posts']['label_view_concat']['value']=$_POST['label_view_concat'];
          $data['jsons']['nova_ext_ordered_mission_posts']['label_view_suffix']['value']=$_POST['label_view_suffix'];
         $jsonEncode = json_encode( $data['jsons'],JSON_PRETTY_PRINT);

          file_put_contents($extConfigFilePath, $jsonEncode);

            $message = sprintf(
          lang('flash_success'),
          // TODO: i18n...
          'Labeled',
          lang('actions_updated'),
          ''
        );

        $flash['status'] = 'success';
        $flash['message'] = text_output($message);

        $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);


        }



       $missionFields= $this->db->list_fields('nova_missions');
       $postFields= $this->db->list_fields('nova_posts'); 
       $data['checkPostChronological']=false;
      $data['checkLegacy']=false;


       $indexsql="SHOW INDEX FROM nova_posts";
            $postIndex= $this->db->query($indexsql);
             $data['postFlag']=false;
              $data['missionFlag']=false;
             foreach($postIndex->result() as $postResult)
             {
              if($postResult->Key_name=='post_ordered_mission_post')
              {
                 
                $data['postFlag']=true;
                break;
              }
             }


             $indexsql="SHOW INDEX FROM nova_missions";
            $missionIndex= $this->db->query($indexsql);
            
             foreach($missionIndex->result() as $missionResult)
             {
              if($missionResult->Key_name=='post_ordered_mission')
              {
                 
                $data['missionFlag']=true;
                break;
              }
             }

      
       if(isset($_POST['submit']) && $_POST['submit']=='createIndex')
       {
           
           
             $prefix= $this->db->dbprefix;

             
             if(empty($data['postFlag']))
             {
               $sql="CREATE INDEX  post_ordered_mission_post ON {$prefix}posts (`nova_ext_ordered_post_day`,`nova_ext_ordered_post_date`,`nova_ext_ordered_post_stardate`,`nova_ext_ordered_post_time`)";
                $this->db->query($sql);

                $data['postFlag']=true;
             }

              

             if(empty($data['missionFlag']))
             {
          $sql="CREATE INDEX  post_ordered_mission ON {$prefix}missions (`mission_ext_ordered_config_setting`,`mission_ext_ordered_post_numbering`,`mission_ext_ordered_default_mission_date`,`mission_ext_ordered_default_stardate`,`mission_ext_ordered_legacy_mode`,`mission_ext_ordered_is_new_record`)";

            $this->db->query($sql);

            $data['missionFlag']=true;
          }

            $message = sprintf(
          lang('flash_success'),
          // TODO: i18n...
          'Index added successfully',
          '',
          ''
        );

        $flash['status'] = 'success';
        $flash['message'] = text_output($message);

        $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);



       }
      
                 
                 $data['checkLegacy']=true;

         if (!in_array('post_chronological_mission_post_day', $postFields) == false) 
          {
              $data['checkLegacy']=false;
              $data['checkPostChronological']=true;

          }

       

        if(isset($_POST['submit']) && $_POST['submit']=='legacySubmit')
       {

         $legacy_mode=isset($_POST['legacy_mode'])?$_POST['legacy_mode']:0;

         $data['jsons']['setting']['legacy_mode']=$legacy_mode;

         $jsonEncode = json_encode( $data['jsons'],JSON_PRETTY_PRINT);
          file_put_contents($extConfigFilePath, $jsonEncode);
           $data['checkPostChronological']=true;

                   $message = sprintf(
          lang('flash_success'),
          // TODO: i18n...
          'Legacy mode successfully updated',
          '',
          ''
        );

        $flash['status'] = 'success';
        $flash['message'] = text_output($message);

        $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);
       }
       $leftFields=[];
        foreach($requiredPostFields['post'] as $key)
        {
          if (in_array($key, $postFields) == false) 
          {
             $leftFields[]=$key;
          }
        }
         foreach($requiredMissionFields['mission'] as $key)
        {
          if (in_array($key, $missionFields) == false) 
          {
             $leftFields[]=$key;
          }
        }
         $data['fields']=$leftFields;
       
     


    $this->_regions['title'] .= 'Configuration';
    $this->_regions['content'] = $this->extension['nova_ext_ordered_mission_posts']
                                    ->view('config', $this->skin, 'admin', $data);
                               
    Template::assign($this->_regions);
    Template::render();
  }

}
