<?php


namespace App\Server;


use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class Qiniu
{
    protected $accessKey;
    protected $secretKey;
    protected $bucket;
    protected $token;

    function __construct()
    {
        $this->accessKey = env('QINIU_ACCESS_KEY');
        $this->secretKey = env('QINIU_SECRET_KEY');
        $this->bucket = env('QINIU_BUCKET');
        $this->createToken();
    }


    /**
     * 生成上传Token
     */
    protected function createToken()
    {
        $token = cache('qiniu_token');
        if (!$token) {
            $auth = new Auth($this->accessKey, $this->secretKey);
            $token = $auth->uploadToken($this->bucket);
            cache('qiniu_token', $token, 60);
        }
        $this->token = $token;
    }


    /**
     * 远程图片上传到七牛云
     * @param $url
     * @return mixed|string
     */
    public function fetch($url)
    {
        try {
            $image = 'quora/' . date('YmdHis') . uniqid();
            $uploadMgr = new UploadManager();
            list($ret, $error) = $uploadMgr->put($this->token, $image, file_get_contents($url));
            if ($error) {
                return $error;
            }
            return env('QINIU_DOMAIN') . '/' . $image;
        } catch (\Exception $e) {
            return $url;
        }
    }

}
