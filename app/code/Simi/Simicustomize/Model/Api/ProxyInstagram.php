<?php
namespace Simi\Simicustomize\Model\Api;

class ProxyInstagram implements \Simi\Simicustomize\Api\ContactInterface
{
    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    )
    {
        $this->request = $request;
        $this->simiObjectManager = $simiObjectManager;
        return $this;
    }

    /**
     * Save Reserve request
     * @return boolean
     */
    public function index() {
        $limit = 18; //default
        $proxy = $this->simiObjectManager->get('\Simi\Simicustomize\Model\Proxy');
        $path = $this->request->getParam('path');
        $rqLimit = $this->request->getParam('limit');
        if ($rqLimit) {
            $limit = $rqLimit;
        }
        $path = trim($path, '/');
        $instagram = 'https://www.instagram.com';
        $queryHash = 'e769aa130647d2354c40ea6a439bfc08';
        //get userId
        $userInfoApi = "${instagram}/${path}/?__a=1";
        $userInfo = $proxy->query($userInfoApi);
        $user = json_decode($userInfo, true);
        if (isset($user['graphql']['user']['id'])) {
            $userId = $user['graphql']['user']['id'];
            $infoApi = "${instagram}/graphql/query/?query_hash=${queryHash}&variables={\"id\":\"${userId}\",\"first\":\"${limit}\"}";
            $userInfoJson = $proxy->query($infoApi);
            $error = false;
            try{
                $userInfos = json_decode($userInfoJson, true);
            }catch(\Exception $e){
                $error = true;
            }
            if ($error || !isset($userInfos['data']['user']['edge_owner_to_timeline_media'])) {
                //find new query_hash
                $url = $instagram.'/'.$path.'/';
                $request1 = $proxy->query($url);
                preg_match('/\/static\/(([A-z0-9])*?\/){0,3}ProfilePageContainer\.js\/([A-z0-9])*?\.js/', $request1, $matcheds);
                if (isset($matcheds[0])) {
                    $jsUrl = $instagram.$matcheds[0];
                    $request2 = $proxy->query($jsUrl);
                    preg_match('/profilePosts.*?pagination.*?queryId:["\'](.*?)["\'],/', $request2, $matcheds);
                    if(isset($matcheds[1])){
                        $queryHash = $matcheds[1];
                        $infoApi = "${instagram}/graphql/query/?query_hash=${queryHash}&variables={\"id\":\"${userId}\",\"first\":\"${limit}\"}";
                        $userInfoJson = $proxy->query($infoApi);
                    }
                }
            }
            if($userInfoJson){
                die($userInfoJson);
            }
        }
        return false;
    }
}
