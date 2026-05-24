<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH.'core/libraries/Nova_controller_admin.php';

class __extensions__nova_ext_mission_post_summary__Manage extends Nova_controller_admin
{
	private $required_post_columns = array(
		'nova_ext_mission_post_summary',
	);

	private $required_mission_columns = array(
		'mission_ext_mission_post_summary_enable',
	);

	public function __construct()
	{
		parent::__construct();

		$this->ci =& get_instance();
		$this->_regions['nav_sub'] = Menu::build('adminsub', 'manageext');
	}

	public function config()
	{
		Auth::check_access('site/settings');

		$configPath = dirname(__FILE__).'/../config.json';

		$action = isset($_POST['action']) ? $_POST['action'] : '';

		if ($action === 'setup_database') {
			$this->_flash($this->_setupDatabase());
		} elseif ($action === 'install_feed') {
			$this->_flash($this->_writeControllerBlock('feed'));
		} elseif ($action === 'save_config') {
			$this->_flash($this->_saveConfig($configPath));
		}

		$data = array();
		$data['title'] = 'Mission Post Summary - Configuration';
		$data['jsons'] = json_decode(file_get_contents($configPath), true);

		$data['missing_columns'] = $this->_missingColumns();
		$data['db_ready'] = empty($data['missing_columns']['posts'])
			&& empty($data['missing_columns']['missions']);

		$data['feed_state'] = $this->_controllerBlockState('feed');

		$this->_regions['title'] .= 'Mission Post Summary';
		$this->_regions['content'] = $this->extension['nova_ext_mission_post_summary']
			->view('config', $this->skin, 'admin', $data);

		Template::assign($this->_regions);
		Template::render();
	}

	// ---------- helpers ----------

	private function _flash($result)
	{
		$flash = array(
			'status'  => ($result[0] === 'error') ? 'error' : 'success',
			'message' => text_output($result[1]),
		);
		$this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);
	}

	private function _missingColumns()
	{
		$prefix = $this->db->dbprefix;
		$missing = array('posts' => array(), 'missions' => array());

		$postFields = $this->db->list_fields($prefix.'posts');
		foreach ($this->required_post_columns as $col) {
			if ( ! in_array($col, $postFields)) {
				$missing['posts'][] = $col;
			}
		}

		$missionFields = $this->db->list_fields($prefix.'missions');
		foreach ($this->required_mission_columns as $col) {
			if ( ! in_array($col, $missionFields)) {
				$missing['missions'][] = $col;
			}
		}

		return $missing;
	}

	private function _columnSql($table, $column)
	{
		$defs = array(
			'nova_ext_mission_post_summary'           => 'TEXT NULL DEFAULT NULL',
			'mission_ext_mission_post_summary_enable' => 'INTEGER DEFAULT 0',
		);

		if ( ! isset($defs[$column])) {
			return '';
		}

		return 'ALTER TABLE `'.$this->db->dbprefix.$table.'` ADD COLUMN `'.$column.'` '.$defs[$column];
	}

	private function _setupDatabase()
	{
		$missing = $this->_missingColumns();
		$columnsAdded = 0;

		foreach (array('posts', 'missions') as $table) {
			foreach ($missing[$table] as $column) {
				$sql = $this->_columnSql($table, $column);
				if ($sql !== '') {
					$this->db->query($sql);
					$columnsAdded++;
				}
			}
		}

		if ($columnsAdded === 0) {
			return array('success', 'Database is already fully set up - nothing to add.');
		}
		return array('success', 'Database setup complete. Added '.$columnsAdded.' column(s).');
	}

	private function _saveConfig($configPath)
	{
		$json = json_decode(file_get_contents($configPath), true);

		foreach ($json['nova_ext_mission_post_summary'] as $key => $field) {
			if (isset($_POST[$key])) {
				$json['nova_ext_mission_post_summary'][$key]['value'] = $_POST[$key];
			}
		}

		if (isset($_POST['rows'])) {
			$json['setting']['rows'] = (int) $_POST['rows'];
		}
		$json['setting']['summary_mode'] = isset($_POST['summary_mode']) ? $_POST['summary_mode'] : 0;

		file_put_contents($configPath, json_encode($json, JSON_PRETTY_PRINT));

		return array('success', 'Configuration saved.');
	}

	// ---------- feed-block writer ----------

	private function _blockMap()
	{
		return array(
			'feed' => array(
				'file'   => APPPATH.'controllers/Feed.php',
				'txt'    => dirname(__FILE__).'/../feed.txt',
				'tag'    => 'feed',
				'method' => 'posts',
				'label'  => 'Feed code',
			),
		);
	}

	private function _controllerBlockState($which)
	{
		$map = $this->_blockMap();
		if ( ! isset($map[$which])) {
			return 'unknown';
		}
		$m = $map[$which];

		if ( ! file_exists($m['file'])) {
			return 'missing_file';
		}

		$file = file_get_contents($m['file']);

		// Deferral: if nova_ext_ordered_mission_posts already owns the feed,
		// we don't try to install our own shim - that extension's library
		// already handles the summary integration internally.
		if ($which === 'feed' && preg_match('/nova_ext_ordered_mission_posts:feed v\d+ START/', $file)) {
			return 'deferred';
		}

		$txt = file_exists($m['txt']) ? file_get_contents($m['txt']) : '';

		$installedVersion = $this->_blockVersion($file, $m['tag']);
		$currentVersion   = $this->_blockVersion($txt,  $m['tag']);

		if ($installedVersion !== null) {
			return ($installedVersion === $currentVersion) ? 'current' : 'outdated';
		}

		if (preg_match('/function\s+'.preg_quote($m['method'], '/').'\s*\(/', $file)) {
			return 'legacy';
		}

		return 'missing';
	}

	private function _blockVersion($content, $tag)
	{
		if (preg_match('/nova_ext_mission_post_summary:'.preg_quote($tag, '/').' v(\d+) START/', $content, $match)) {
			return (int) $match[1];
		}
		return null;
	}

	private function _writeControllerBlock($which)
	{
		$map = $this->_blockMap();
		if ( ! isset($map[$which])) {
			return array('error', 'Unknown block.');
		}
		$m = $map[$which];

		$state = $this->_controllerBlockState($which);

		if ($state === 'current') {
			return array('success', $m['label'].' is already up to date.');
		}
		if ($state === 'deferred') {
			return array('success', 'Nova Ext Ordered Mission Posts owns the feed - nothing to do here.');
		}
		if ($state === 'missing_file') {
			return array('error', 'Could not find '.$m['file'].'.');
		}

		$file = file_get_contents($m['file']);
		if ( ! file_exists($m['txt'])) {
			return array('error', 'Cannot find '.basename($m['txt']).' in the extension.');
		}
		$block = rtrim(file_get_contents($m['txt']), "\r\n");

		if ($state === 'outdated') {
			$pattern = '/[ \t]*\/\*\s*nova_ext_mission_post_summary:'.preg_quote($m['tag'], '/')
				.' v\d+ START.*?nova_ext_mission_post_summary:'.preg_quote($m['tag'], '/').' END\s*\*\//s';
			$new = preg_replace($pattern, $block, $file, 1, $count);
			if ($count !== 1) {
				return array('error', 'Could not locate the managed block in '.basename($m['file']).'.');
			}
			$file = $new;
		} elseif ($state === 'legacy') {
			$span = $this->_findUnmarkedMethodSpan($file, $m['method']);
			if ($span === null) {
				return array('error', 'Could not parse the existing '.$m['method'].'() method in '.basename($m['file']).'.');
			}
			$file = substr($file, 0, $span[0]).$block."\n".substr($file, $span[1]);
		} else {
			$pos = strrpos($file, '}');
			if ($pos === false) {
				return array('error', basename($m['file']).' is not in the expected format.');
			}
			$file = rtrim(substr($file, 0, $pos))."\n\n".$block."\n}\n";
		}

		file_put_contents($m['file'], $file);

		return array('success', $m['label'].' updated successfully.');
	}

	/**
	 * Locate the byte span of an unmarked $methodName declaration in $content.
	 * Returns array($start, $end) (end exclusive, includes the trailing newline
	 * if present), or null if the method can't be cleanly located. A minimal
	 * lexer is used so braces, comments, and string literals don't fool the
	 * counter.
	 */
	private function _findUnmarkedMethodSpan($content, $methodName)
	{
		$len = strlen($content);
		$state = 'normal';
		$functionPositions = array();
		$i = 0;

		while ($i < $len) {
			$c = $content[$i];
			$next = ($i + 1 < $len) ? $content[$i + 1] : '';

			if ($state === 'normal') {
				if ($c === "'") { $state = 'single'; $i++; continue; }
				if ($c === '"') { $state = 'double'; $i++; continue; }
				if ($c === '/' && $next === '/') { $state = 'line_comment'; $i += 2; continue; }
				if ($c === '/' && $next === '*') { $state = 'block_comment'; $i += 2; continue; }
				if ($c === 'f'
					&& substr($content, $i, 8) === 'function'
					&& ($i === 0 || ! self::_isIdentChar($content[$i - 1]))
					&& ($i + 8 >= $len || ! self::_isIdentChar($content[$i + 8]))) {
					$functionPositions[] = $i;
					$i += 8;
					continue;
				}
			} elseif ($state === 'single') {
				if ($c === '\\') { $i += 2; continue; }
				if ($c === "'") $state = 'normal';
			} elseif ($state === 'double') {
				if ($c === '\\') { $i += 2; continue; }
				if ($c === '"') $state = 'normal';
			} elseif ($state === 'line_comment') {
				if ($c === "\n") $state = 'normal';
			} elseif ($state === 'block_comment') {
				if ($c === '*' && $next === '/') { $state = 'normal'; $i += 2; continue; }
			}
			$i++;
		}

		foreach ($functionPositions as $fnPos) {
			$p = $fnPos + 8;
			while ($p < $len && ctype_space($content[$p])) {
				$p++;
			}
			$nameLen = strlen($methodName);
			if ($p + $nameLen > $len) continue;
			if (substr($content, $p, $nameLen) !== $methodName) continue;
			if ($p + $nameLen < $len && self::_isIdentChar($content[$p + $nameLen])) continue;

			$k = $fnPos - 1;
			while ($k >= 0 && ($content[$k] === ' ' || $content[$k] === "\t")) {
				$k--;
			}
			foreach (array('static', 'final', 'abstract', 'protected', 'public', 'private') as $kw) {
				$klen = strlen($kw);
				if ($k - $klen + 1 >= 0
					&& substr($content, $k - $klen + 1, $klen) === $kw
					&& ($k - $klen < 0 || ! self::_isIdentChar($content[$k - $klen]))) {
					$k -= $klen;
					while ($k >= 0 && ($content[$k] === ' ' || $content[$k] === "\t")) {
						$k--;
					}
				}
			}
			$start = $k + 1;

			$q = $p + $nameLen;
			$bs = 'normal';
			$depth = 0;
			$started = false;
			while ($q < $len) {
				$c = $content[$q];
				$next = ($q + 1 < $len) ? $content[$q + 1] : '';
				if ($bs === 'normal') {
					if ($c === '{') {
						$depth++;
						$started = true;
					} elseif ($c === '}') {
						$depth--;
						if ($started && $depth === 0) {
							$end = $q + 1;
							if ($end < $len && $content[$end] === "\n") $end++;
							return array($start, $end);
						}
					} elseif ($c === "'") { $bs = 'single'; $q++; continue; }
					elseif ($c === '"') { $bs = 'double'; $q++; continue; }
					elseif ($c === '/' && $next === '/') { $bs = 'line_comment'; $q += 2; continue; }
					elseif ($c === '/' && $next === '*') { $bs = 'block_comment'; $q += 2; continue; }
				} elseif ($bs === 'single') {
					if ($c === '\\') { $q += 2; continue; }
					if ($c === "'") $bs = 'normal';
				} elseif ($bs === 'double') {
					if ($c === '\\') { $q += 2; continue; }
					if ($c === '"') $bs = 'normal';
				} elseif ($bs === 'line_comment') {
					if ($c === "\n") $bs = 'normal';
				} elseif ($bs === 'block_comment') {
					if ($c === '*' && $next === '/') { $bs = 'normal'; $q += 2; continue; }
				}
				$q++;
			}
			return null;
		}

		return null;
	}

	private static function _isIdentChar($ch)
	{
		return ctype_alnum($ch) || $ch === '_';
	}
}
