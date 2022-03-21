<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH . 'core/libraries/Nova_controller_admin.php';

class __extensions__nova_ext_mission_post_summary__Manage extends Nova_controller_admin
{
    public function __construct()
    {
        parent::__construct();

        $this->ci = & get_instance();
        $this->_regions['nav_sub'] = Menu::build('adminsub', 'manageext');
        //$this->_regions['nav_sub'] = Menu::build('sub', 'sim');
        

        
    }



  public function writeFeedCode()
  {   
          
        $extControllerPath = APPPATH.'controllers/feed.php';
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

    public function getQuery($switch)
    {   

        $prefix= $this->db->dbprefix;

        switch ($switch)
        {
            case 'nova_ext_mission_post_summary':
                $sql = "ALTER TABLE {$prefix}posts ADD COLUMN nova_ext_mission_post_summary TEXT NULL DEFAULT NULL";
            break;

            case 'mission_ext_mission_post_summary_enable':
                $sql = "ALTER TABLE {$prefix}missions ADD COLUMN mission_ext_mission_post_summary_enable int(11) DEFAULT 0";
            break;

            default:
            break;
        }
        return isset($sql) ? $sql : '';
    }

    public function saveColumn($requiredPostFields, $requiredMissionFields)
    {

        if (isset($_POST['submit']) && $_POST['submit'] == 'Add')
        {
            $attr = isset($_POST['attribute']) ? $_POST['attribute'] : '';

            if (in_array($attr, $requiredMissionFields['mission']) == true)
            {
                $table = "nova_missions";

            }

            if (in_array($attr, $requiredPostFields['post']) == true)
            {
                $table = "nova_posts";

            }
            if (!empty($table))
            {

                if (!$this
                    ->db
                    ->field_exists($attr, $table))
                {
                    $sql = $this->getQuery($attr);
                    if (!empty($sql))
                    {
                        $query = $this
                            ->db
                            ->query($sql);

                        if (($key = array_search($attr, $requiredPostFields['post'])) !== false)
                        {
                            unset($requiredPostFields['post'][$key]);
                        }

                        if (($key = array_search($attr, $requiredMissionFields['mission'])) !== false)
                        {
                            unset($requiredMissionFields['mission'][$key]);
                        }
                        $list['post'] = $requiredPostFields;
                        $list['mission'] = $requiredMissionFields;
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
        $data['feed']=true;
        $data['title'] = 'Summary Configuration';
        $requiredPostFields['post'] = ['nova_ext_mission_post_summary'];
        $requiredMissionFields['mission'] = ['mission_ext_mission_post_summary_enable'];


        $extControllerPath = APPPATH.'controllers/feed.php';
         
        if ( !file_exists( $extControllerPath ) ) { 
        return [];
        }
        $file = file_get_contents( $extControllerPath );
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
        if ($list = $this->saveColumn($requiredPostFields, $requiredMissionFields))
        {
            $requiredPostFields = $list['post'];
            $requiredMissionFields = $list['mission'];
            $message = sprintf(lang('flash_success') ,
            // TODO: i18n...
            'Column Added successfully', '', '');

            $flash['status'] = 'success';
            $flash['message'] = text_output($message);

            $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);
        }

        $extConfigFilePath = dirname(__FILE__) . '/../config.json';

        if (!file_exists($extConfigFilePath))
        {
            return [];
        }
        $file = file_get_contents($extConfigFilePath);
        $data['jsons'] = json_decode($file, true);

        if (isset($_POST['submit']) && $_POST['submit'] == 'Submit')
        {

            $data['jsons']['nova_ext_mission_post_summary']['nova_ext_mission_post_summary']['value'] = $_POST['nova_ext_mission_post_summary'];

            $data['jsons']['nova_ext_mission_post_summary']['mission_ext_mission_post_summary_enable']['value'] = $_POST['mission_ext_mission_post_summary_enable'];
            $data['jsons']['setting']['summary_mode'] = isset($_POST['summary_mode']) ? $_POST['summary_mode'] : 0;
            $data['jsons']['setting']['rows'] = $_POST['rows'];

            $jsonEncode = json_encode($data['jsons'], JSON_PRETTY_PRINT);

            file_put_contents($extConfigFilePath, $jsonEncode);

            $message = sprintf(lang('flash_success') ,
            // TODO: i18n...
            'Configuration', lang('actions_updated') , '');

            $flash['status'] = 'success';
            $flash['message'] = text_output($message);

            $this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);

        }

       

    

        $missionFields = $this
            ->db
            ->list_fields('nova_missions');
        $postFields = $this
            ->db
            ->list_fields('nova_posts');

        $leftFields = [];
        foreach ($requiredPostFields['post'] as $key)
        {
            if (in_array($key, $postFields) == false)
            {
                $leftFields[] = $key;
            }
        }
        foreach ($requiredMissionFields['mission'] as $key)
        {
            if (in_array($key, $missionFields) == false)
            {
                $leftFields[] = $key;
            }
        }
        $data['fields'] = $leftFields;
        $this->_regions['title'] .= 'Configuration';
        $this->_regions['content'] = $this->extension['nova_ext_mission_post_summary']
            ->view('config', $this->skin, 'admin', $data);

        Template::assign($this->_regions);
        Template::render();
    }

}