<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Validator;
use SafeStartApi\Model\ImageProcessor;

class UploadPlugin extends AbstractPlugin
{
    const THUMBNAIL_FULL = '1024x768';
    const THUMBNAIL_MEDIUM = '320x220';
    const THUMBNAIL_SMALL = '70x70';

    protected $options;


    // PHP File Upload error message codes:
    // http://php.net/manual/en/features.file-upload.errors.php
    protected $error_messages = array(
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload',
        'post_max_size' => 'The uploaded file exceeds the post_max_size directive in php.ini',
        'max_file_size' => 'The uploaded file exceeds the max allowed size',
        'min_file_size' => 'The uploaded file does not reach the min allowed size',
        'accept_file_types' => 'Filetype not allowed',
        'max_number_of_files' => 'Maximum number of files exceeded',
        'max_width' => 'Image exceeds maximum width',
        'min_width' => 'Image requires a minimum width',
        'max_height' => 'Image exceeds maximum height',
        'min_height' => 'Image requires a minimum height'
    );


    /**
     * UploadPlugin::__invoke()
     *
     * @param mixed $options
     * @param integer|string|array|SaveStartApi/Entity/User $options[user_dirs] (if is set!)
     * @param bool $initialize
     * @param mixed $error_messages
     * @return
     */
    public function __invoke($options = null, $initialize = false, $error_messages = null) {

        $moduleConfig = $this->getController()->getServiceLocator()->get('Config');
        $defUsersPath = $moduleConfig['defUsersPath'];
        $defUsersPath = $this->get_filter_path($defUsersPath);

        $options['upload_dir'] = $this->get_root_path() . $defUsersPath;
        $options['upload_url'] = $this->get_full_url() . $defUsersPath;
        $this->setOptions($options);

        if ($error_messages) {
            $this->error_messages = array_merge($this->error_messages, $error_messages);
        }

        if ($initialize) {
            $this->initialize();
        } else {
            return $this;
        }
    }

    /**
     * UploadPlugin::setOptions()
     *
     * @param mixed $options
     * @return void
     */
    protected function setOptions($options = null) {
        $defUsersPath = '/data/users/';
        $this->options = array(
            'script_url' => $this->get_full_url() . '/',
            'upload_dir' => $this->get_root_path() . $defUsersPath,
            'upload_url' => $this->get_full_url() . $defUsersPath,
            'user_dirs' => false,
            'mkdir_mode' => 0777,
            'file_chmod' => 0777,
            'param_name' => 'files',
            'rename_file' => true,
            'overwrite_file' => true,
            // Set the following option to 'POST', if your server does not support
            'access_control_allow_origin' => '*',
            'access_control_allow_credentials' => false,
            'access_control_allow_methods' => array(
                'OPTIONS',
                'HEAD',
                'GET',
                'POST',
                'PUT',
                'PATCH',
            ),
            'access_control_allow_headers' => array(
                'Content-Type',
                'Content-Range',
                'Content-Disposition'
            ),
            // Enable to provide file downloads via GET requests to the PHP script:
            //     1. Set to 1 to download files via readfile method through PHP
            //     2. Set to 2 to send a X-Sendfile header for lighttpd/Apache
            //     3. Set to 3 to send a X-Accel-Redirect header for nginx
            // If set to 2 or 3, adjust the upload_url option to the base path of
            // the redirect parameter, e.g. '/files/'.
            'download_via_php' => false,
            // Read files in chunks to avoid memory limits when download_via_php
            // is enabled, set to 0 to disable chunked reading of files:
            'readfile_chunk_size' => 10 * 1024 * 1024, // 10 MiB
            // Defines which files can be displayed inline when downloaded:
            'inline_file_types' => array('jpg', 'jpeg', 'png'),
            // Defines which files (based on their names) are accepted for upload:
            'accept_file_types' => '/.+$/i',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => 20 * 1024 * 1024,
            'min_file_size' => 1024,
            // The maximum number of files for the upload directory:
            'max_number_of_files' => null,
            // Image resolution restrictions:
            'max_width' => null,
            'max_height' => null,
            'min_width' => 70,
            'min_height' => 70,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads' => true,
            'use_versions_path' => false,
            'versions_delimiter' => '',
            // Set to false to disable rotating images based on EXIF meta data:
            'orient_image' => true,
            // default params
            'image_versions' => array(
                /*'full' => array(
                    'max_width' => 1024,
                    'max_height' => 768,
                    'jpeg_quality' => 95,
                    'png_quality' => 9
                ),*/
                'medium' => array(
                    'max_width' => 320,
                    'max_height' => 220,
                    'jpeg_quality' => 95,
                    'png_quality' => 9
                ),
                'small' => array(
                    'crop' => true,
                    'max_width' => 70,
                    'max_height' => 70,
                    'jpeg_quality' => 95,
                    'png_quality' => 9
                ),
            )
        );


        // initialization thumbnails by constants
        $thumbnails = array(
            'full' => self::THUMBNAIL_FULL,
            'medium' => self::THUMBNAIL_MEDIUM,
            'small' =>self::THUMBNAIL_SMALL,
        );

        // override image versions
        if(!empty($thumbnails) && is_array($thumbnails) && !isset($options['image_versions'])) {
            $this->options['image_versions'] = array();
            foreach($thumbnails as $key => $thumb) {
                list($max_width,$max_height) = explode("x", $thumb);
                $params = array(
                    'max_width' => intval($max_width),
                    'max_height' => intval($max_height),
                    'jpeg_quality' => 95,
                    'png_quality' => 9
                );
                if($max_width == $max_height) {
                    $params['crop'] = true;
                }
                $this->options['image_versions'][$key] = $params;
            }
        }

        if ($options) {
            if(isset($options['upload_dir']))
                $options['upload_dir'] = $this->get_root_path() . $this->get_filter_path($options['upload_dir']);
            if(isset($options['upload_url']))
                $options['upload_url'] = $this->get_full_url() . $this->get_filter_path($options['upload_url']);
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * UploadPlugin::initialize()
     *
     * @return
     */
    public function initialize() {
        switch ($this->get_server_var('REQUEST_METHOD')) {
            case 'OPTIONS':
            case 'HEAD':
                $this->head();
                break;
            case 'GET':
                $this->get();
                break;
            case 'PATCH':
            case 'PUT':
            case 'POST':
                $this->post();
                break;
            default:
                $this->header('HTTP/1.1 405 Method Not Allowed');
        }
    }

    /**
     * UploadPlugin::get_full_url()
     *
     * @return
     */
    protected function get_full_url() {
        $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
        return
            ($https ? 'https://' : 'http://').
            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
                ($https && $_SERVER['SERVER_PORT'] === 443 ||
                $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT'])))
            //.substr($_SERVER['SCRIPT_FILENAME'],0, strrpos($_SERVER['SCRIPT_FILENAME'], '/'))
            ;
    }


    protected function get_root_path() {
        $root = $this->get_server_var('DOCUMENT_ROOT');

        if(!file_exists($root . "/init_autoloader.php")) {
            $root = dirname($root);
        }

        return $root;
    }


    /**
     * UploadPlugin::get_filter_path()
     *
     * @param string $fEndPath
     * @return void
     */
    protected function get_filter_path($fEndPath = '/') {
        $root       = $this->get_root_path();
        $fEndPath   = str_replace("{$root}", '', $fEndPath);
        $fEndPath   = str_replace('\\', '/', $fEndPath);

        if(preg_match('/^(\/|.\/).*/isU', $fEndPath, $match)) {
            $fEndPath = preg_replace('/^(\/|.\/).*/isU', "", $fEndPath);
        } else {
            $fEndPath = preg_replace('/^(.*)$/isU', "$1", $fEndPath);
        }

        $returnFolder = '/' . $fEndPath;
        if(!preg_match('/.*(\/)$/isU', $returnFolder, $match)) {
            $returnFolder .= '/';
        }

        return $returnFolder;
    }

    /**
     * UploadPlugin::get_user_id()
     *
     * @return
     */
    protected function get_user_id() {

        $userDirs = $this->options['user_dirs'];
        $user_folder = '';
        if(is_integer($userDirs)) {
            $user_folder = ($userDirs > 0) ? "{$userDirs}/" : '';
        } elseif (is_string($userDirs)) {
            $user_folder = (strlen($userDirs) > 0) ? "{$userDirs}/" : '';
        }

        return $user_folder;
    }

    /**
     * UploadPlugin::get_user_path()
     *
     * @return
     */
    protected function get_user_path() {
        if ($this->options['user_dirs']) {
            return $this->get_user_id();
        }
        return '';
    }

    /**
     * UploadPlugin::get_upload_path()
     *
     * @param mixed $file_name
     * @param mixed $version
     * @return
     */
    protected function get_upload_path($file_name = null, $version = null) {
        $file_name = $file_name ? $file_name : '';
        if (empty($version)) {
            $version_path = '';
        } else {
            if($this->options['use_versions_path']) {
                $version_dir = @$this->options['image_versions'][$version]['upload_dir'];
                if ($version_dir) {
                    return $version_dir.$this->get_user_path().$file_name;
                }
                $version_path = $version.'/';
            } else {
                if($file_name !== '') {
                    $file_name = $this->get_version_file_name($file_name, $version);
                }
                $version_path = '';
            }
        }

        $return = $this->options['upload_dir'].$this->get_user_path().$version_path.$file_name;
        return $return;
    }

    /**
     * UploadPlugin::get_query_separator()
     *
     * @param mixed $url
     * @return
     */
    protected function get_query_separator($url) {
        return strpos($url, '?') === false ? '?' : '&';
    }

    /**
     * UploadPlugin::get_download_url()
     *
     * @param mixed $file_name
     * @param mixed $version
     * @param bool $direct
     * @return
     */
    protected function get_download_url($file_name, $version = null, $direct = false) {
        if (!$direct && $this->options['download_via_php']) {
            $url = $this->options['script_url']
                .$this->get_query_separator($this->options['script_url'])
                .'file='.rawurlencode($file_name);
            if ($version) {
                $url .= '&version='.rawurlencode($version);
            }
            return $url.'&download=1';
        }
        if (empty($version)) {
            $version_path = '';
        } else {
            if($this->options['use_versions_path']) {
                $version_url = @$this->options['image_versions'][$version]['upload_url'];
                if ($version_url) {
                    return $version_url.$this->get_user_path().rawurlencode($file_name);
                }
                $version_path = rawurlencode($version).'/';
            } else {
                $file_name = $this->get_version_file_name($file_name, $version);
                $version_path = '';
            }
        }
        return $this->options['upload_url'].$this->get_user_path()
        .$version_path.rawurlencode($file_name);
    }

    /**
     * UploadPlugin::set_additional_file_properties()
     *
     * @param mixed $file
     * @return
     */
    protected function set_additional_file_properties($file) {
        if ($this->options['access_control_allow_credentials']) {
            $file->deleteWithCredentials = true;
        }
    }

    // Fix for overflowing signed 32 bit integers,
    // works for sizes up to 2^32-1 bytes (4 GiB - 1):
    /**
     * UploadPlugin::fix_integer_overflow()
     *
     * @param mixed $size
     * @return
     */
    protected function fix_integer_overflow($size) {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }
        return $size;
    }

    /**
     * UploadPlugin::get_file_size()
     *
     * @param mixed $file_path
     * @param bool $clear_stat_cache
     * @return
     */
    protected function get_file_size($file_path, $clear_stat_cache = false) {
        if ($clear_stat_cache) {
            clearstatcache(true, $file_path);
        }
        return $this->fix_integer_overflow(filesize($file_path));

    }

    /**
     * UploadPlugin::is_valid_file_object()
     *
     * @param mixed $file_name
     * @return
     */
    protected function is_valid_file_object($file_name) {
        $file_path = $this->get_upload_path($file_name);
        if (is_file($file_path) && $file_name[0] !== '.') {
            return true;
        }
        return false;
    }

    /**
     * UploadPlugin::get_file_object()
     *
     * @param mixed $file_name
     * @return
     */
    protected function get_file_object($file_name) {
        if ($this->is_valid_file_object($file_name)) {
            $file = new stdClass();
            $file->name = $file_name;
            $file->size = $this->get_file_size(
                $this->get_upload_path($file_name)
            );
            $file->url = $this->get_download_url($file->name);
            foreach($this->options['image_versions'] as $version => $options) {
                if (!empty($version)) {
                    if (is_file($this->get_upload_path($file_name, $version))) {
                        $file->{$version.'Url'} = $this->get_download_url(
                            $file->name,
                            $version
                        );
                    }
                }
            }
            $this->set_additional_file_properties($file);
            return $file;
        }
        return null;
    }

    /**
     * UploadPlugin::get_file_objects()
     *
     * @param string $iteration_method
     * @return
     */
    protected function get_file_objects($iteration_method = 'get_file_object') {
        $upload_dir = $this->get_upload_path();
        if (!is_dir($upload_dir)) {
            return array();
        }
        return array_values(array_filter(array_map(
            array($this, $iteration_method),
            scandir($upload_dir)
        )));
    }

    /**
     * UploadPlugin::count_file_objects()
     *
     * @return
     */
    protected function count_file_objects() {
        return count($this->get_file_objects('is_valid_file_object'));
    }

    /**
     * UploadPlugin::create_scaled_image()
     *
     * @param mixed $file_name
     * @param mixed $version
     * @param mixed $options
     * @return
     */
    protected function create_scaled_image($file_name, $version, $options) {

        $file_path = realpath($this->get_upload_path($file_name));
        if(!$file_path) {
            throw new \Exception("Invalid image path");
        }

        if (!empty($version)) {
            if($this->options['use_versions_path'] === false) {
                $file_name = $this->get_version_file_name($file_name, $version);
            }
            $version_dir = $this->get_upload_path(null, $version);
            if (!is_dir($version_dir)) {
                mkdir($version_dir, $this->options['mkdir_mode'], true);
            }
            $new_file_path = $version_dir.'/'.$file_name;
        } else {
            $new_file_path = $file_path;
        }
        if (!function_exists('getimagesize')) {
            error_log('Function not found: getimagesize');
            return false;
        }
        list($img_width, $img_height) = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            return false;
        }

        /* using ImageProcessor > * /
        $asd = new ImageProcessor($file_path);
        $asd->contain(array('width'=>$options['max_width'], 'height'=>$options['max_height']));
        return $asd->save($new_file_path);
        /* > end. */

        $max_width = $options['max_width'];
        $max_height = $options['max_height'];
        $scale = min(
            $max_width / $img_width,
            $max_height / $img_height
        );
        if ($scale >= 1) {
            if ($file_path !== $new_file_path) {
                return copy($file_path, $new_file_path);
            }
            return true;
        }
        if (!function_exists('imagecreatetruecolor')) {
            error_log('Function not found: imagecreatetruecolor');
            return false;
        }
        if (empty($options['crop'])) {
            $new_width = $img_width * $scale;
            $new_height = $img_height * $scale;
            $dst_x = 0;
            $dst_y = 0;
            $new_img = imagecreatetruecolor($new_width, $new_height);
        } else {
            if (($img_width / $img_height) >= ($max_width / $max_height)) {
                $new_width = $img_width / ($img_height / $max_height);
                $new_height = $max_height;
            } else {
                $new_width = $max_width;
                $new_height = $img_height / ($img_width / $max_width);
            }
            $dst_x = 0 - ($new_width - $max_width) / 2;
            $dst_y = 0 - ($new_height - $max_height) / 2;
            $new_img = imagecreatetruecolor($max_width, $max_height);
        }
        switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img = imagecreatefromjpeg($file_path);
                $write_image = 'imagejpeg';
                $image_quality = isset($options['jpeg_quality']) ?
                    $options['jpeg_quality'] : 95;
                break;
            case 'gif':
                imagecolortransparent($new_img, imagecolorallocate($new_img, 0, 0, 0));
                $src_img = imagecreatefromgif($file_path);
                $write_image = 'imagegif';
                $image_quality = null;
                break;
            case 'png':
                imagecolortransparent($new_img, imagecolorallocate($new_img, 0, 0, 0));
                imagealphablending($new_img, false);
                imagesavealpha($new_img, true);
                $src_img = imagecreatefrompng($file_path);
                $write_image = 'imagepng';
                $image_quality = isset($options['png_quality']) ?
                    $options['png_quality'] : 9;
                break;
            default:
                imagedestroy($new_img);
                return false;
        }
        $success = imagecopyresampled(
                $new_img,
                $src_img,
                $dst_x,
                $dst_y,
                0,
                0,
                $new_width,
                $new_height,
                $img_width,
                $img_height
            ) && $write_image($new_img, $new_file_path, $image_quality);

        // change access
        chmod($new_file_path, $this->options['file_chmod']);

        // Free up memory (imagedestroy does not delete files):
        imagedestroy($src_img);
        imagedestroy($new_img);

        return $success;
    }

    /**
     * UploadPlugin::get_error_message()
     *
     * @param mixed $error
     * @return
     */
    protected function get_error_message($error) {
        return array_key_exists($error, $this->error_messages) ?
            $this->error_messages[$error] : $error;
    }

    /**
     * UploadPlugin::get_config_bytes()
     *
     * @param mixed $val
     * @return
     */
    function get_config_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $this->fix_integer_overflow($val);
    }

    /**
     * UploadPlugin::validate()
     *
     * @param mixed $uploaded_file
     * @param mixed $file
     * @param mixed $error
     * @param mixed $index
     * @return
     */
    protected function validate($uploaded_file, $file, $error, $index) {

        if ($error) {
            $file->error = $this->get_error_message($error);
            return false;
        }
        $content_length = $this->fix_integer_overflow(intval(
            $this->get_server_var('CONTENT_LENGTH')
        ));
        $post_max_size = $this->get_config_bytes(ini_get('post_max_size'));
        if ($post_max_size && ($content_length > $post_max_size)) {
            $file->error = $this->get_error_message('post_max_size');
            return false;
        }

        if (!preg_match($this->options['accept_file_types'], $file->name)) {
            $file->error = $this->get_error_message('accept_file_types');
            return false;
        }

        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            $file_size = $this->get_file_size($uploaded_file);
        } else {
            $file_size = $content_length;
        }

        if ($this->options['max_file_size'] && (
                $file_size > $this->options['max_file_size'] ||
                $file->size > $this->options['max_file_size'])
        ) {
            $file->error = $this->get_error_message('max_file_size');
            return false;
        }

        if ($this->options['min_file_size'] &&
            $file_size < $this->options['min_file_size']) {
            $file->error = $this->get_error_message('min_file_size');
            return false;
        }

        if (is_int($this->options['max_number_of_files']) && (
                $this->count_file_objects() >= $this->options['max_number_of_files'])
        ) {
            $file->error = $this->get_error_message('max_number_of_files');
            return false;
        }

        list($img_width, $img_height) = @getimagesize($uploaded_file);
        if (is_int($img_width)) {
            if ($this->options['max_width'] && $img_width > $this->options['max_width']) {
                $file->error = $this->get_error_message('max_width');
                return false;
            }
            if ($this->options['max_height'] && $img_height > $this->options['max_height']) {
                $file->error = $this->get_error_message('max_height');
                return false;
            }
            if ($this->options['min_width'] && $img_width < $this->options['min_width']) {
                $file->error = $this->get_error_message('min_width');
                return false;
            }
            if ($this->options['min_height'] && $img_height < $this->options['min_height']) {
                $file->error = $this->get_error_message('min_height');
                return false;
            }
        }

        /**/
        $validatorChain = new Validator\ValidatorChain();

        //$validatorChain->attach(new Validator\File\IsImage());
        //$validatorChain->attach(new Validator\File\MimeType(array('image/png', 'image/jpg', 'image/jpeg', 'enableHeaderCheck' => true)));
        //$extensions = is_array($this->options['inline_file_types']) ? $this->options['inline_file_types'] : array();
        //if(!empty($extensions)) {
        //    $validatorChain->attach(new Validator\File\Extension($extensions, true));
        //}

        $fileSizeArr = array();
        $minFSize = $this->options['min_file_size'];
        $maxFSize = $this->options['max_file_size'];
        $chunkSize = $this->options['readfile_chunk_size'];
        if(isset($minFSize) && is_int($minFSize) && $minFSize > 1)
            $fileSizeArr['min'] = $minFSize;
        if(isset($maxFSize) && is_int($maxFSize) && $maxFSize > 1)
            $fileSizeArr['max'] = $maxFSize;
        if(isset($chunkSize) && is_int($chunkSize) && $chunkSize > 1)
            $fileSizeArr['max'] = $chunkSize;

        if(!empty($fileSizeArr)) {
            $validatorChain->attach(new Validator\File\Size($fileSizeArr));
        }

        $imSizeArr = array();
        $minWidth = $this->options['min_width'];
        $minHeight = $this->options['min_height'];
        $maxWidth = $this->options['max_width'];
        $maxHeight = $this->options['max_height'];
        if(isset($minWidth) && is_int($minWidth) && $minWidth > 0)
            $imSizeArr['minWidth'] = $minWidth;
        if(isset($minHeight) && is_int($minHeight) && $minHeight > 0)
            $imSizeArr['minHeight'] = $minHeight;
        if(isset($maxWidth) && is_int($maxWidth) && $maxWidth > 0)
            $imSizeArr['maxWidth'] = $maxWidth;
        if(isset($maxHeight) && is_int($maxHeight) && $maxHeight > 0)
            $imSizeArr['maxHeight'] = $maxHeight;

        if(!empty($imSizeArr)) {
            $validatorChain->attach(new Validator\File\ImageSize($imSizeArr));
        }

        if ($validatorChain->isValid($uploaded_file)) {
            return true;
        } else {
            // username failed validation; print reasons
            $file->error = "";
            foreach ($validatorChain->getMessages() as $message) {
                $file->error .= "$message\n";
            }

            return false;
        }
        /**/


        return true;
    }

    /**
     * UploadPlugin::upcount_name_callback()
     *
     * @param mixed $matches
     * @return
     */
    protected function upcount_name_callback($matches) {
        $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        $ext = isset($matches[2]) ? $matches[2] : '';
        return ' ('.$index.')'.$ext;
    }

    /**
     * UploadPlugin::upcount_name()
     *
     * @param mixed $name
     * @return
     */
    protected function upcount_name($name) {
        return preg_replace_callback(
            '/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/',
            array($this, 'upcount_name_callback'),
            $name,
            1
        );
    }

    /**
     * UploadPlugin::get_unique_filename()
     *
     * @param mixed $name
     * @param mixed $type
     * @param mixed $index
     * @param mixed $content_range
     * @return
     */
    protected function get_unique_filename($name,
                                           $type = null, $index = null, $content_range = null) {
        while(is_dir($this->get_upload_path($name))) {
            $name = $this->upcount_name($name);
        }
        // Keep an existing filename if this is part of a chunked upload:
        $uploaded_bytes = $this->fix_integer_overflow(intval($content_range[1]));
        while(is_file($this->get_upload_path($name))) {
            if ($uploaded_bytes === $this->get_file_size(
                    $this->get_upload_path($name))) {
                break;
            }
            $name = $this->upcount_name($name);
        }
        return $name;
    }

    /**
     * UploadPlugin::trim_file_name()
     *
     * @param mixed $name
     * @param mixed $type
     * @param mixed $index
     * @param mixed $content_range
     * @return
     */
    protected function trim_file_name($name,
                                      $type = null, $index = null, $content_range = null) {
        // Remove path information and dots around the filename, to prevent uploading
        // into different directories or replacing hidden system files.
        // Also remove control characters and spaces (\x00..\x20) around the filename:
        $name = trim(basename(stripslashes($name)), ".\x00..\x20");
        // Use a timestamp for empty filenames:
        if (!$name) {
            $name = str_replace('.', '-', microtime(true));
        }
        // Add missing file extension for known image types:
        if (strpos($name, '.') === false &&
            preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
            $name .= '.'.$matches[1];
        }
        return $name;
    }

    /**
     * UploadPlugin::get_file_name()
     *
     * @param mixed $name
     * @param mixed $type
     * @param mixed $index
     * @param mixed $content_range
     * @return
     */
    protected function get_file_name($name,
                                     $type = null, $index = null, $content_range = null) {
        return $this->get_unique_filename(
            $this->trim_file_name($name, $type, $index, $content_range),
            $type,
            $index,
            $content_range
        );
    }

    /**
     * UploadPlugin::get_version_file_name()
     *
     * @param mixed $file_name
     * @param mixed $version
     * @return
     */
    protected function get_version_file_name($file_name, $version) {

        $versions = $this->options['image_versions'];
        $opts = $versions[$version];
        $version = $opts['max_width'] . 'x' . $opts['max_height'];

        return preg_replace('/(\.[^\.]*)$/isU', $this->options['versions_delimiter'] . "{$version}$1", $file_name);
    }

    /**
     * UploadPlugin::handle_form_data()
     *
     * @param mixed $file
     * @param mixed $index
     * @return
     */
    protected function handle_form_data($file, $index) {
        // Handle form data, e.g. $_REQUEST['description'][$index]
    }

    /**
     * UploadPlugin::imageflip()
     *
     * @param mixed $image
     * @param mixed $mode
     * @return
     */
    protected function imageflip($image, $mode) {
        if (function_exists('imageflip')) {
            return imageflip($image, $mode);
        }
        $new_width = $src_width = imagesx($image);
        $new_height = $src_height = imagesy($image);
        $new_img = imagecreatetruecolor($new_width, $new_height);
        $src_x = 0;
        $src_y = 0;
        switch ($mode) {
            case '1': // flip on the horizontal axis
                $src_y = $new_height - 1;
                $src_height = -$new_height;
                break;
            case '2': // flip on the vertical axis
                $src_x  = $new_width - 1;
                $src_width = -$new_width;
                break;
            case '3': // flip on both axes
                $src_y = $new_height - 1;
                $src_height = -$new_height;
                $src_x  = $new_width - 1;
                $src_width = -$new_width;
                break;
            default:
                return $image;
        }
        imagecopyresampled(
            $new_img,
            $image,
            0,
            0,
            $src_x,
            $src_y,
            $new_width,
            $new_height,
            $src_width,
            $src_height
        );
        // Free up memory (imagedestroy does not delete files):
        imagedestroy($image);
        return $new_img;
    }

    /**
     * UploadPlugin::orient_image()
     *
     * @param mixed $file_path
     * @return
     */
    protected function orient_image($file_path) {
        if (!function_exists('exif_read_data')) {
            return false;
        }
        $exif = @exif_read_data($file_path);
        if ($exif === false) {
            return false;
        }
        $orientation = intval(@$exif['Orientation']);
        if ($orientation < 2 || $orientation > 8) {
            return false;
        }
        $image = imagecreatefromjpeg($file_path);
        switch ($orientation) {
            case 2:
                $image = $this->imageflip(
                    $image,
                    defined('IMG_FLIP_VERTICAL') ? IMG_FLIP_VERTICAL : 2
                );
                break;
            case 3:
                $image = imagerotate($image, 180, 0);
                break;
            case 4:
                $image = $this->imageflip(
                    $image,
                    defined('IMG_FLIP_HORIZONTAL') ? IMG_FLIP_HORIZONTAL : 1
                );
                break;
            case 5:
                $image = $this->imageflip(
                    $image,
                    defined('IMG_FLIP_HORIZONTAL') ? IMG_FLIP_HORIZONTAL : 1
                );
                $image = imagerotate($image, 270, 0);
                break;
            case 6:
                $image = imagerotate($image, 270, 0);
                break;
            case 7:
                $image = $this->imageflip(
                    $image,
                    defined('IMG_FLIP_VERTICAL') ? IMG_FLIP_VERTICAL : 2
                );
                $image = imagerotate($image, 270, 0);
                break;
            case 8:
                $image = imagerotate($image, 90, 0);
                break;
            default:
                return false;
        }
        $success = imagejpeg($image, $file_path);
        // Free up memory (imagedestroy does not delete files):
        imagedestroy($image);
        return $success;
    }

    /**
     * UploadPlugin::handle_image_file()
     *
     * @param mixed $file_path
     * @param mixed $file
     * @return
     */
    protected function handle_image_file($file_path, $file) {
        if ($this->options['orient_image']) {
            $this->orient_image($file_path);
        }
        $failed_versions = array();
        foreach($this->options['image_versions'] as $version => $options) {
            if ($this->create_scaled_image($file->name, $version, $options)) {
                if (!empty($version)) {
                    $file->{$version.'Url'} = $this->get_download_url(
                        $file->name,
                        $version
                    );
                } else {
                    $file->size = $this->get_file_size($file_path, true);
                }
            } else {
                $failed_versions[] = $version;
            }
        }
        switch (count($failed_versions)) {
            case 0:
                break;
            case 1:
                $file->error = 'Failed to create scaled version: '
                    .$failed_versions[0];
                break;
            default:
                $file->error = 'Failed to create scaled versions: '
                    .implode($failed_versions,', ');
        }
    }

    /**
     * UploadPlugin::handle_file_upload()
     *
     * @param mixed $uploaded_file
     * @param mixed $name
     * @param mixed $size
     * @param mixed $type
     * @param mixed $error
     * @param mixed $index
     * @param mixed $content_range
     * @return
     */
    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
                                          $index = null, $content_range = null) {

        $name = $this->trim_file_name($name, $type, $index, $content_range);
        if($this->options['overwrite_file']) {
            if(file_exists($this->get_upload_path($name))) {
                unlink($this->get_upload_path($name));
            }
        }

        $file = new \stdClass();
        $file->name = $this->get_file_name($name, $type, $index, $content_range);
        $file->nameOnly  = preg_replace('/(.*)\.[^\.]*$/is','$1',$file->name);
        $file->ext  = preg_replace('/.*\.([^\.]*)$/is','$1',$file->name);
        $file->size = $this->fix_integer_overflow(intval($size));
        $file->type = $type;
        $file->thumbNames = array_keys($this->options['image_versions']);
        $file->useThumbFolder = $this->options['use_versions_path'];
        $file->thumbDelimiter = $this->options['versions_delimiter'];

        if ($this->validate($uploaded_file, $file, $error, $index)) {
            $this->handle_form_data($file, $index);
            $upload_dir = $this->get_upload_path();
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, $this->options['mkdir_mode'], true);
            }
            $file_path = $this->get_upload_path($file->name);
            $append_file = $content_range && is_file($file_path) &&
                $file->size > $this->get_file_size($file_path);
            if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                // multipart/formdata uploads (POST method uploads)
                if ($append_file) {
                    file_put_contents(
                        $file_path,
                        fopen($uploaded_file, 'r'),
                        FILE_APPEND
                    );
                } else {
                    move_uploaded_file($uploaded_file, $file_path);
                }
            } else {
                // Non-multipart uploads (PUT method support)
                file_put_contents(
                    $file_path,
                    fopen('php://input', 'r'),
                    $append_file ? FILE_APPEND : 0
                );
            }

            // change access
            chmod($file_path, $this->options['file_chmod']);

            $file_size = $this->get_file_size($file_path, $append_file);
            if ($file_size === $file->size) {
                $file->url = $this->get_download_url($file->name);
                list($img_width, $img_height) = @getimagesize($file_path);
                if (is_int($img_width) &&
                    preg_match($this->getPregIinlineFileTypes(), $file->name)) {
                    $this->handle_image_file($file_path, $file);
                }
            } else {
                $file->size = $file_size;
                if (!$content_range && $this->options['discard_aborted_uploads']) {
                    unlink($file_path);
                    $file->error = 'abort';
                }
            }
            $this->set_additional_file_properties($file);
        }

        $newFileInfo = new \stdClass();
        $newFileInfo->hash = $file->nameOnly;
        $newFileInfo->ext = $file->ext;
        $newFileInfo->sizes = array();
        foreach($this->options['image_versions'] as $version => $opts) {
            $f = new \stdClass();
            $f->$version = $opts['max_width'] . 'x' . $opts['max_height'];
            $newFileInfo->sizes[] = $f;
        }
        if(!empty($file->error)) {
            $newFileInfo->error = $file->error;
        }

        return $newFileInfo;
    }

    /**
     * UploadPlugin::readfile()
     *
     * @param mixed $file_path
     * @return
     */
    protected function readfile($file_path) {
        $file_size = $this->get_file_size($file_path);
        $chunk_size = $this->options['readfile_chunk_size'];
        if ($chunk_size && $file_size > $chunk_size) {
            $handle = fopen($file_path, 'rb');
            while (!feof($handle)) {
                echo fread($handle, $chunk_size);
                ob_flush();
                flush();
            }
            fclose($handle);
            return $file_size;
        }
        return readfile($file_path);
    }

    /**
     * UploadPlugin::body()
     *
     * @param mixed $str
     * @return
     */
    protected function body($str) {
        echo $str;
    }

    /**
     * UploadPlugin::header()
     *
     * @param mixed $str
     * @return
     */
    protected function header($str) {
        header($str);
    }

    /**
     * UploadPlugin::get_server_var()
     *
     * @param mixed $id
     * @return
     */
    protected function get_server_var($id) {
        return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }

    /**
     * UploadPlugin::generate_response()
     *
     * @param mixed $content
     * @param bool $print_response
     * @return
     */
    protected function generate_response($content, $print_response = true) {
        if ($print_response) {
            $json = json_encode($content);

            /* show in head > * /
            $redirect = isset($_REQUEST['redirect']) ?
                stripslashes($_REQUEST['redirect']) : null;
            if ($redirect) {
                $this->header('Location: '.sprintf($redirect, rawurlencode($json)));
                return;
            }
            $this->head();

            if ($this->get_server_var('HTTP_CONTENT_RANGE')) {
                $files = isset($content[$this->options['param_name']]) ?
                    $content[$this->options['param_name']] : null;
                if ($files && is_array($files) && is_object($files[0]) && $files[0]->size) {
                    $this->header('Range: 0-'.(
                        $this->fix_integer_overflow(intval($files[0]->size)) - 1
                    ));
                }
            }

            $this->body($json);
            /* > end show in head. */
        }
        return $content;
    }

    /**
     * UploadPlugin::generate_result_array()
     *
     * @param mixed $items
     * @return
     */
    protected function generate_result_array($items = array()) {
        $newArr = array();
        foreach($items as $item) {
            if(is_object($item)) {
                $newArr[] = get_object_vars($item);
            } elseif(is_array($item)) {
                $newArr[] = $item;
            } else {
                $newArr[] = $item;
            }
        }
        return $newArr;
    }

    /**
     * UploadPlugin::get_version_param()
     *
     * @return
     */
    protected function get_version_param() {
        return isset($_GET['version']) ? basename(stripslashes($_GET['version'])) : null;
    }

    /**
     * UploadPlugin::get_singular_param_name()
     *
     * @return
     */
    protected function get_singular_param_name() {
        return substr($this->options['param_name'], 0, -1);
    }

    /**
     * UploadPlugin::get_file_name_param()
     *
     * @return
     */
    protected function get_file_name_param() {
        $name = $this->get_singular_param_name();
        return isset($_GET[$name]) ? basename(stripslashes($_GET[$name])) : null;
    }

    /**
     * UploadPlugin::get_file_names_params()
     *
     * @return
     */
    protected function get_file_names_params() {
        $params = isset($_GET[$this->options['param_name']]) ?
            $_GET[$this->options['param_name']] : array();
        foreach ($params as $key => $value) {
            $params[$key] = basename(stripslashes($value));
        }
        return $params;
    }

    /**
     * UploadPlugin::get_file_type()
     *
     * @param mixed $file_path
     * @return
     */
    protected function get_file_type($file_path) {
        switch (strtolower(pathinfo($file_path, PATHINFO_EXTENSION))) {
            case 'jpeg':
            case 'jpg':
                return 'image/jpeg';
            case 'png':
                return 'image/png';
            case 'gif':
                return 'image/gif';
            default:
                return '';
        }
    }


    /**
     * UploadPlugin::getPregIinlineFileTypes()
     *
     * @return
     */
    protected function getPregIinlineFileTypes() {

        $types = $this->options['inline_file_types'];
        if(is_array($types) && !empty($types)) {
            return '/\.(' . implode('|', $types) . ')$/i';
        }

        return '/\.(jpe?g|png)$/i';
    }


    /**
     * UploadPlugin::download()
     *
     * @return
     */
    protected function download() {
        switch ($this->options['download_via_php']) {
            case 1:
                $redirect_header = null;
                break;
            case 2:
                $redirect_header = 'X-Sendfile';
                break;
            case 3:
                $redirect_header = 'X-Accel-Redirect';
                break;
            default:
                return $this->header('HTTP/1.1 403 Forbidden');
        }
        $file_name = $this->get_file_name_param();
        if (!$this->is_valid_file_object($file_name)) {
            return $this->header('HTTP/1.1 404 Not Found');
        }
        if ($redirect_header) {
            return $this->header(
                $redirect_header.': '.$this->get_download_url(
                    $file_name,
                    $this->get_version_param(),
                    true
                )
            );
        }
        $file_path = $this->get_upload_path($file_name, $this->get_version_param());
        // Prevent browsers from MIME-sniffing the content-type:
        $this->header('X-Content-Type-Options: nosniff');
        if (!preg_match($this->getPregIinlineFileTypes(), $file_name)) {
            $this->header('Content-Type: application/octet-stream');
            $this->header('Content-Disposition: attachment; filename="'.$file_name.'"');
        } else {
            $this->header('Content-Type: '.$this->get_file_type($file_path));
            $this->header('Content-Disposition: inline; filename="'.$file_name.'"');
        }
        $this->header('Content-Length: '.$this->get_file_size($file_path));
        $this->header('Last-Modified: '.gmdate('D, d M Y H:i:s T', filemtime($file_path)));
        $this->readfile($file_path);
    }

    /**
     * UploadPlugin::send_content_type_header()
     *
     * @return
     */
    protected function send_content_type_header() {
        $this->header('Vary: Accept');
        if (strpos($this->get_server_var('HTTP_ACCEPT'), 'application/json') !== false) {
            $this->header('Content-type: application/json');
        } else {
            $this->header('Content-type: text/plain');
        }
    }

    /**
     * UploadPlugin::send_access_control_headers()
     *
     * @return
     */
    protected function send_access_control_headers() {
        $this->header('Access-Control-Allow-Origin: '.$this->options['access_control_allow_origin']);
        $this->header('Access-Control-Allow-Credentials: '
        .($this->options['access_control_allow_credentials'] ? 'true' : 'false'));
        $this->header('Access-Control-Allow-Methods: '
        .implode(', ', $this->options['access_control_allow_methods']));
        $this->header('Access-Control-Allow-Headers: '
        .implode(', ', $this->options['access_control_allow_headers']));
    }

    /**
     * UploadPlugin::head()
     *
     * @return
     */
    public function head() {
        $this->header('Pragma: no-cache');
        $this->header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->header('Content-Disposition: inline; filename="files.json"');
        // Prevent Internet Explorer from MIME-sniffing the content-type:
        $this->header('X-Content-Type-Options: nosniff');
        if ($this->options['access_control_allow_origin']) {
            $this->send_access_control_headers();
        }
        $this->send_content_type_header();
    }

    /**
     * UploadPlugin::get()
     *
     * @param bool $print_response
     * @return
     */
    public function get($print_response = true) {
        if ($print_response && isset($_GET['download'])) {
            return $this->download();
        }
        $file_name = $this->get_file_name_param();
        if ($file_name) {
            $response = array(
                $this->get_singular_param_name() => $this->get_file_object($file_name)
            );
        } else {
            $response = array(
                $this->options['param_name'] => $this->get_file_objects()
            );
        }
        return $this->generate_response($response, $print_response);
    }

    /**
     * UploadPlugin::post()
     *
     * @param bool $print_response
     * @return
     */
    public function post($print_response = true) {
        $upload = isset($_FILES[$this->options['param_name']]) ?
            $_FILES[$this->options['param_name']] : null;

        if($upload === null) {
            throw new \Exception('\'' .$this->options['param_name'] . '\' field is empty');
        }

        // Parse the Content-Disposition header, if available:
        $file_name = $this->get_server_var('HTTP_CONTENT_DISPOSITION') ?
            rawurldecode(preg_replace(
                '/(^[^"]+")|("$)/',
                '',
                $this->get_server_var('HTTP_CONTENT_DISPOSITION')
            )) : null;
        // Parse the Content-Range header, which has the following form:
        // Content-Range: bytes 0-524287/2000000
        $content_range = $this->get_server_var('HTTP_CONTENT_RANGE') ?
            preg_split('/[^0-9]+/', $this->get_server_var('HTTP_CONTENT_RANGE')) : null;
        $size =  $content_range ? $content_range[3] : null;
        $files = null;
        if ($upload && is_array($upload['tmp_name'])) {
            $files = array();
            // param_name is an array identifier like "files[]",
            // $_FILES is a multi-dimensional array:
            foreach ($upload['tmp_name'] as $index => $value) {

                $pathinfo   = pathinfo($upload['name'][$index]);
                $ext        = isset($pathinfo['extension'])
                    ? $pathinfo['extension']
                    : preg_replace('/.*\.([^\.]*)$/is','$1',$upload['name'][$index]);
                $hash       = preg_replace('/\./isU', '', "" .  uniqid()); // md5_file($upload['tmp_name'][$index]);

                $files[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],

                    $this->options['rename_file']
                        ? "{$hash}.{$ext}"
                        : ($file_name
                        ? $file_name
                        : $upload['name'][$index])
                    ,

                    $size ? $size : $upload['size'][$index],
                    $upload['type'][$index],
                    $upload['error'][$index],
                    $index,
                    $content_range
                );
            }
        } else {
            if(isset($upload['tmp_name']) && !empty($upload['tmp_name'])) {
                $pathinfo   = pathinfo($upload['name']);
                $ext        = isset($pathinfo['extension'])
                    ? $pathinfo['extension']
                    : preg_replace('/.*\.([^\.]*)$/is','$1',$upload['name']);
                $hash       = preg_replace('/\./isU', '', "" .  uniqid()); // md5_file($upload['tmp_name']);

                // param_name is a single object identifier like "file",
                // $_FILES is a one-dimensional array:
                //$files[] = $this->handle_file_upload(
                $files = $this->handle_file_upload(
                    isset($upload['tmp_name']) ? $upload['tmp_name'] : null,

                    $this->options['rename_file']
                        ? "{$hash}.{$ext}"
                        : ($file_name
                        ? $file_name
                        : (isset($upload['name'])
                            ? $upload['name']
                            : null))
                    ,

                    $size
                        ? $size
                        : (isset($upload['size'])
                        ? $upload['size']
                        : $this->get_server_var('CONTENT_LENGTH')),
                    isset($upload['type'])
                        ? $upload['type']
                        : $this->get_server_var('CONTENT_TYPE'),
                    isset($upload['error'])
                        ? $upload['error']
                        : null,
                    null,
                    $content_range
                );
            } else {
                if($upload === null) {
                    throw new \Exception('Information about downloadable file(s) is not found.');
                }
            }
        }

        return $this->generate_response(
        //array($this->options['param_name'] => $files),
            $files,
            $print_response
        );
    }
}