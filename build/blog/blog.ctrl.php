<?php
/**
 * blog controller
 *
 * @package blog
 * @author The myTravelbook Team <http://www.sourceforge.net/projects/mytravelbook>
 * @copyright Copyright (c) 2005-2006, myTravelbook Team
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 * @version $Id: blog.ctrl.php 56 2006-06-21 13:53:57Z roland $
 */
class BlogController extends PAppController {
    private $_model;
    private $_view;
    
    public function __construct() {
        parent::__construct();
        $this->_model = new Blog();
        $this->_view =  new BlogView($this->_model);
    }
    
    public function __destruct() {
        unset($this->_model);
        unset($this->_view);
    }
    
    public function index() {
        // index is called when http request = ./blog
        $request = PRequest::get()->request;
        $User = APP_User::login();
        if (!isset($request[1]))
            $request[1] = '';
        // user bar
        if ($User && $request[1] != 'tags') {
            ob_start();
            $this->_view->userbar();
            $str = ob_get_contents();
            ob_end_clean();
            $P = PVars::getObj('page');
            $P->newBar .= $str;
        }
        switch ($request[1]) {
            case 'create':
                if (!$User)
                    PRequest::home();
                ob_start();
                if (isset($request[2]) && $request[2] == 'finish' && isset($request[3]) && $this->_model->isPostId($request[3])) {
					$this->singlePost($request[3]);
                } else {
                    $callbackId = $this->createProcess();
                    $this->_view->createForm($callbackId);
                    PPostHandler::clearVars($callbackId);
                }
                $str = ob_get_contents();
                ob_end_clean();
                $P = PVars::getObj('page');
                $P->content .= $str;
                break;
            
            case 'del':
                if (!$User)
                    PRequest::home();
                if (!isset($request[2]) || !$this->_model->isPostId($request[2]) || !$this->_model->isUserPost($User->userId, $request[2]))
                    PRequest::home();
                $post = $this->_model->getPost($request[2]);
                $cbId = $this->deleteProcess();
                PPostHandler::clearVars($cbId);
                ob_start();
                // content here
                $this->_view->delete($cbId, $post);
                $this->singlePost($request[2], false);
                $str = ob_get_contents();
                ob_end_clean();
                $P = PVars::getObj('page');
                $P->content .= $str;
                break;

            case 'edit':
                if (!$User)
                    PRequest::home();
                if (!isset($request[2]) || !$this->_model->isPostId($request[2]) || !$this->_model->isUserPost($User->userId, $request[2]))
                    PRequest::home();
                ob_start();
            	if (isset($request[3]) && $request[3] == 'finish') {
            		$this->singlePost($request[2]);
            	} else {
					$callbackId = $this->editProcess((int)$request[2]);
                    $vars =& PPostHandler::getVars($callbackId);
                    if (!isset($vars['errors']) || !is_array($vars['errors'])) {
                        $vars['errors'] = array();
                    }
                    $this->_editFill($request[2], $vars);
					$this->_view->editForm((int)$request[2], $callbackId);
                    PPostHandler::clearVars();
				}
                $str = ob_get_contents();
                ob_end_clean();
                $P = PVars::getObj('page');
                $P->content .= $str;
                break;
                
            case 'settings':
                ob_start();
                $this->_view->settingsForm();
                $str = ob_get_contents();
                ob_end_clean();
                $P = PVars::getObj('page');
                $P->content .= $str;
                break;

            case 'tags':
                ob_start();
                $this->_view->tags((isset($request[2])?$request[2]:false));
                $str = ob_get_contents();
                ob_end_clean();
                $P = PVars::getObj('page');
                $P->content .= $str;
                break;

            case 'cat':
                ob_start();
                $this->_view->categories();
                $str = ob_get_contents();
                ob_end_clean();
                $P = PVars::getObj('page');
                $P->content .= $str;
                break;

            case 'suggestTags':
                // ignore current request, so we can use the last request
                PRequest::ignoreCurrentRequest();
                if (!isset($request[2])) {
                    PPHP::PExit();
                }
                $new_tags = $this->_model->suggestTags($request[2]);
                echo $this->_view->generateClickableTagSuggestions($new_tags);
                PPHP::PExit();
                break;

            case 'suggestLocation':
                // ignore current request, so we can use the last request
                PRequest::ignoreCurrentRequest();
                if (!isset($request[2])) {
                    PPHP::PExit();
                }
                $locations = $this->_model->suggestLocation($request[2]);
                echo $this->_view->generateLocationOverview($locations);
                PPHP::PExit();
                break;
            
            default:
                $requestStr = implode('/', $request);
                $matches = array();
                if (preg_match('%/page(\d+)%', $requestStr, $matches)) {
                    $page = $matches[1];
                } else {
                    $page = 1;
                }
                $User = new User;
                // display blogs of user $request[1]
                if (preg_match(User::HANDLE_PREGEXP, $request[1]) && $User->handleInUse($request[1])) {
                    ob_start();
                    if (isset($request[2]) && $this->_model->isPostId($request[2])) {
                    	$this->singlePost($request[2]);
                    } else {
                        $this->_view->userPosts($request[1], $page);
                    }
                    $str = ob_get_contents();
                    ob_end_clean();
                    $P = PVars::getObj('page');
                    $P->content .= $str;
                } else {
                    ob_start();
                    $this->_view->allBlogs($page);
                    $str = ob_get_contents();
                    ob_end_clean();
                    $P = PVars::getObj('page');
                    $P->content .= $str;
                }
                break;
        }
    }

    // 2006-11-23 19:13:59 rs Copied to Message class :o
    private function _cleanupText($txt) 
    {
        $str = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body>'.$txt.'</body></html>'; 
        $doc = @DOMDocument::loadHTML($str);
        if ($doc) {
            $sanitize = new PSafeHTML($doc);
            $sanitize->allow('html');
            $sanitize->allow('body');
            $sanitize->allow('p');
            $sanitize->allow('div');
            $sanitize->allow('b');
            $sanitize->allow('i');
            $sanitize->allow('u');
            $sanitize->allow('a');
            $sanitize->allow('em');
            $sanitize->allow('strong');
            $sanitize->allow('hr');
            $sanitize->allow('span');
            $sanitize->allow('ul');
            $sanitize->allow('il');
            $sanitize->allow('font');
            $sanitize->allow('strike');
            $sanitize->allow('br');
            $sanitize->allow('blockquote');
        
            $sanitize->allowAttribute('color'); 
            $sanitize->allowAttribute('bgcolor');           
            $sanitize->allowAttribute('href');
            $sanitize->allowAttribute('style');
            $sanitize->allowAttribute('class');
            $sanitize->allowAttribute('width');
            $sanitize->allowAttribute('height');
            $sanitize->allowAttribute('src');
            $sanitize->allowAttribute('alt');
            $sanitize->allowAttribute('title');
            $sanitize->clean();
            $doc = $sanitize->getDoc();
            $nodes = $doc->x->query('/html/body/node()');
            $ret = '';
            foreach ($nodes as $node) {
                $ret .= $doc->saveXML($node);
            }
            return $ret;
        } else {
            // invalid HTML
            return '';
        }
        
    }

    /**
     * Fills the posthandler vars with the blog from $blogId.
     *
     * @return false if no blog could be found with id $blogId, otherwise true.
     */
    private function _editFill($blogId, &$vars)
    {
        if (!$b = $this->_model->getEditData($blogId))
            return false;
        $vars['id']          = $blogId;
        $vars['t']           = $b->blog_title;
        $vars['txt']         = $b->blog_text;
        $vars['tr']          = $b->trip_id_foreign;
        $vars['flag-sticky'] = $b->is_sticky;
        $vars['trip_id_foreign'] = $b->trip_id_foreign;
        $vars['vis'] = 'pub';
        if ($b->is_private)
            $vars['vis'] = 'pri';
        if ($b->is_protected)
            $vars['vis'] = 'prt';
        if ($b->blog_start === null) {
            $vars['sty'] = '';
            $vars['stm'] = '';
            $vars['std'] = '';
        } else {
            $vars['sty'] = date('Y', strtotime($b->blog_start));
            $vars['stm'] = idate('m', strtotime($b->blog_start));
            $vars['std'] = date('d', strtotime($b->blog_start));
        }
        if ($b->latitude) {
            $vars['latitude'] = $b->latitude;
        } else {
            $vars['latitude'] = '';
        }
        if ($b->longitude) {
            $vars['longitude'] = $b->longitude;
        } else {
            $vars['longitude'] = '';
        }
        if ($b->blog_geonameid) {
            $vars['geonameid'] = $b->blog_geonameid;
        } else {
            $vars['geonameid'] = '';
        }
        if ($b->geonamesname) {
            $vars['geonamename'] = $b->geonamesname;
        } else {
            $vars['geonamename'] = '';
        }
        if ($b->fk_countrycode) {
            $vars['geonamecountrycode'] = $b->fk_countrycode;
        } else {
        	$vars['geonamecountrycode'] = '';
        }
        if ($b->geonamecountry) {
            $vars['geonamecountry'] = $b->geonamecountry;
        } else {
            $vars['geonamecountry'] = '';
        }
        if ($b->fk_admincode) {
            $vars['admincode'] = $b->fk_admincode;
        } else {
        	$vars['admincode'] = '';
        }

        $tagIt = $this->_model->getTags($blogId);
        $tags = array();
        if ($tagIt) {
            foreach ($tagIt as $row) {
                $tags[] = $row->name;
            }
            $vars['tags'] = implode(', ', $tags);
        }
        return true;
    }

    private function _validateVars(&$vars) 
    {
        $errors = array();
        // check title
        if (!isset($vars['t']) || empty($vars['t'])) {
            $errors[] = 'title';
        }
        // check text
        if ((!isset($vars['txt']) || empty($vars['txt'])) && (!isset($vars['tr']) || !strcmp($vars['tr'],'')!=0 || !$this->_model->isUserTrip(APP_User::get()->getId(), $vars['tr']))) {
            $errors[] = 'text';
        }
        // check category
        if (!isset($vars['cat']) || strcmp($vars['cat'],'')==0) {
            $vars['cat'] = false; // no category selected.
        } elseif (!$this->isUserBlogCategory(APP_User::get()->getId(), $vars['cat'])) {
            $errors[] = 'category';
        }
        if (isset($vars['tr']) && strcmp($vars['tr'],'')!=0 && !$this->_model->isUserTrip(APP_User::get()->getId(), $vars['tr'])) {
            $errors[] = 'trip';
        }
        // geonames
        if (!isset($vars['latitude']) || $vars['latitude'] == '') {
            $vars['latitude'] = false;
        }
        if (!isset($vars['longitude']) || $vars['longitude'] == '') {
            $vars['longitude'] = false;
        }
        if (!isset($vars['geonameid']) || $vars['geonameid'] == '') {
            $vars['geonameid'] = false;
        }
        if (!isset($vars['geonamename']) || $vars['geonamename'] == '') {
            $vars['geonamename'] = false;
        }
        if (!isset($vars['geonamecountrycode']) || $vars['geonamecountrycode'] == '') {
            $vars['geonamecountrycode'] = false;
        }

        if (count($errors) > 0) {
            $vars['errors'] = $errors;
            return false;
        }
        return true;
    }
    
    /**
     * Processing creation of a blog.
     *
     * This is a POST callback function.
     *
     * Sets following errors in POST vars:
     * title        - invalid(empty) title.
     * text         - invalid(empty) text.
     * startdate    - wrongly formatted start date.
     * enddate      - wrongly formatted end date.
     * duration     - empty enddate and invalid duration.
     * category     - category is not belonging to user.
     * trip         - trip is not belonging to user.
     * inserror     - error performing db insertion.
     * tagerror     - error while updating tags.
     */
    public function createProcess() 
    {
        if (PPostHandler::isHandling()) {
            if (!$User = APP_User::login())
                return false;
            $vars =& PPostHandler::getVars();

            if (isset($vars['txt'])) {
                $vars['txt'] = $this->_cleanupText($vars['txt']);
            }

            if (!$this->_validateVars($vars)) {
                return false;
            }

            if (!$userId = APP_User::get()->getId()) {
                $vars['errors'] = array('inserror');
                return false;
            }

            $flags = 0;
            if (isset($vars['flag-sticky']) && $User->hasRight('write_sticky@blog')) {
                $flags = ($flags | Blog::FLAG_STICKY);
            }
            if (!isset($vars['vis']))
                $vars['vis'] = 'pub'; // Default (if none set: public)
            switch($vars['vis']) {
                case 'pub':
                    break;
                    
                case 'prt':
                    $flags = ($flags | Blog::FLAG_VIEW_PROTECTED);
                    break;
                    
                default:
                    $flags = ($flags | Blog::FLAG_VIEW_PRIVATE);
                    break;
            }
            $trip = (isset($vars['tr']) && strcmp($vars['tr'],'')!=0) ? (int)$vars['tr'] : false;
            $blogId = $this->_model->createEntry($flags, $userId, $trip);

            // to sql datetime format.
            if ((isset($vars['sty']) && (int)$vars['sty'] != 0) || (isset($vars['stm']) && (int)$vars['stm'] != 0) || (isset($vars['std']) && (int)$vars['std'] != 0)) {
                $start = mktime(0, 0, 0, (int)$vars['stm'], (int)$vars['std'], (int)$vars['sty']);
                $start = date('YmdHis', $start);
            } else {
                $start = null;
            }

            // Check if the location already exists in our DB and add it if necessary
            if ($vars['geonameid'] && $vars['latitude'] && $vars['longitude'] && $vars['geonamename'] && $vars['geonamecountrycode'] && $vars['admincode']) {
                $geoname_ok = $this->_model->checkGeonamesCache($vars['geonameid'], $vars['latitude'], $vars['longitude'], $vars['geonamename'], $vars['geonamecountrycode'], $vars['admincode']);
            } else {
                $geoname_ok = false;
            }

            $start = is_null($start) ? false : $start;
            $geonameId = $geoname_ok ? $vars['geonameid'] : false;
            try {
                $this->_model->createData($blogId, $vars['t'], $vars['txt'], $start, $geonameId);
            } catch (PException $e) {
                if (PVars::get()->debug) {
                    throw $e;
                } else {
                    error_log($e->__toString());
                }
                // rollback!
                $this->_model->deleteEntry($blogId);
                $vars['errors'] = array('inserror');
                return false;
            }

			if ($trip) {
				$this->_model->setTripPosition($trip, $blogId);
			}
			
            if (!$this->_model->updateTags($blogId, explode(',', $vars['tags']))) {
                $vars['errors'] = array('tagerror');
                return false;
            }
            
            PPostHandler::clearVars();
            return PVars::getObj('env')->baseuri.'blog/create/finish/'.$blogId;
        } else {
            $callbackId = PFunctions::hex2base64(sha1(__METHOD__));
            PPostHandler::setCallback($callbackId, __CLASS__, __FUNCTION__);
            return $callbackId;
        }
    }
    
    public function deleteProcess()
    {
        if (PPostHandler::isHandling()) {
            if (!$User = APP_User::login())
                return false;
            $ret = PVars::getObj('env')->baseuri.'blog/'.$User->userHandle;
            $vars =& PPostHandler::getVars();
            $vars['errors'] = array();
            $vars['messages'] = array();
            if (!isset($vars['id']))
                return $ret;
            if (!$this->_model->isPostId($vars['id']) || !$this->_model->isUserPost($User->userId, $vars['id']))
                return $ret;
            if (isset($vars['n']) && $vars['n']) {
                $vars['messages'][] = 'not_deleted';
                return $ret;
            }
            if (isset($vars['y']) && $vars['y']) {
                $this->_model->deleteEntry($vars['id']);
                $this->_model->deleteData($vars['id']);
                $vars['messages'][] = 'deleted';
            }
            return $ret;
        } else {
            $callbackId = PFunctions::hex2base64(sha1(__METHOD__));
            PPostHandler::setCallback($callbackId, __CLASS__, __FUNCTION__);
            return $callbackId;
        }
    }
    
    /**
     * Processing creation of a blog.
     *
     * This is a POST callback function.
     *
     * Sets following errors in POST vars:
     * title        - invalid(empty) title.
     * startdate    - wrongly formatted start date.
     * enddate      - wrongly formatted end date.
     * duration     - empty enddate and invalid duration.
     * category     - category is not belonging to user.
     * trip         - trip is not belonging to user.
     * upderror     - error performing db update.
     * tagerror     - error while updating tags.
     */
    public function editProcess()
    {
        if (PPostHandler::isHandling()) {
            if (!$User = APP_User::login())
                return false;
            $userId = $User->userId;
            $vars =& PPostHandler::getVars();

            if (!isset($vars['id']) || !$this->_model->isPostId($vars['id']))
                return false;
            if (!$this->_model->isUserPost($userId, $vars['id']))
                return false;
            if (isset($vars['txt'])) {
                $vars['txt'] = $this->_cleanupText($vars['txt']);
            }
            if (!$this->_validateVars($vars)) {
                return false;
            }

            $post = $this->_model->getPost($vars['id']);
            if (!$post)
                return false;
            $flags = $post->flags;
            if (isset($vars['flag-sticky']) && $User->hasRight('write_sticky@blog')) {
                $flags = ($flags | (int)Blog::FLAG_STICKY);
            } else {
                $flags = ($flags & ~(int)Blog::FLAG_STICKY);
            }
            if (!isset($vars['vis']))
                $vars['vis'] = 'pri';
            switch($vars['vis']) {
                case 'pub':
                    $flags = ($flags & ~(int)Blog::FLAG_VIEW_PROTECTED & ~(int)Blog::FLAG_VIEW_PRIVATE);
                    break;
                    
                case 'prt':
                    $flags = ($flags & ~(int)Blog::FLAG_VIEW_PRIVATE | (int)Blog::FLAG_VIEW_PROTECTED);
                    break;
                    
                default:
                    $flags = ($flags & ~(int)Blog::FLAG_VIEW_PROTECTED | (int)Blog::FLAG_VIEW_PRIVATE);
                    break;
            }
            $tripId = (isset($vars['tr']) && strcmp($vars['tr'],'')!=0) ? (int)$vars['tr'] : false;
            
            $this->_model->updatePost($post->blog_id, $flags, $tripId);

            // to sql datetime format.
            if ((isset($vars['sty']) && (int)$vars['sty'] != 0) || (isset($vars['stm']) && (int)$vars['stm'] != 0) || (isset($vars['std']) && (int)$vars['std'] != 0)) {
                $start = mktime(0, 0, 0, (int)$vars['stm'], (int)$vars['std'], (int)$vars['sty']);
                $start = date('YmdHis', $start);
            } else {
                $start = false;
            }

            // Check if the location already exists in our DB and add it if necessary
            if ($vars['geonameid'] && $vars['latitude'] && $vars['longitude'] && $vars['geonamename'] && $vars['geonamecountrycode'] && $vars['admincode']) {
                $geoname_ok = $this->_model->checkGeonamesCache($vars['geonameid'], $vars['latitude'], $vars['longitude'], $vars['geonamename'], $vars['geonamecountrycode'], $vars['admincode']);
            } else {
                $geoname_ok = false;
            }
            
			$geonameId = $geoname_ok ? $vars['geonameid'] : false;
			
            $this->_model->updatePostData($post->blog_id, $vars['t'], $vars['txt'], $start, $geonameId);

            if (!$this->_model->updateTags($post->blog_id, explode(',', $vars['tags']))) {
                $vars['errors'] = array('tagerror');
                return false;
            }

            PPostHandler::clearVars();
            return PVars::getObj('env')->baseuri.'blog/edit/'.$post->blog_id.'/finish';
        } else {
            $callbackId = PFunctions::hex2base64(sha1(__METHOD__));
            PPostHandler::setCallback($callbackId, __CLASS__, __FUNCTION__);
            return $callbackId;
        }
    }
    
    public function singlePost($postId, $showComments = true) 
    {
        $blog = $this->_model->getPost($postId);
        $this->_view->singlePost($blog, $showComments);
    }

    public function userPosts($userHandle) {
    	$this->_view->userPosts($userHandle);
    }

    public function userSettingsForm()
    {
    	if (!$User = APP_User::login())
            return false;
        $this->_view->userSettingsForm();
    }

    public function stickyPosts() {
    	$this->_view->stickyPosts();
    }
}
?>