<?php

namespace Simi\Simicustomize\Plugin;


class ResolverEntityUrl
{
    private $simiObjectManager;
    private $request;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->simiObjectManager = $simiObjectManager;
        $this->request = $request;
    }


    public function afterResolve($entityUrl, $result)
    {
        if (!$result) {
            $contents            = $this->request->getContent();
            $contents_array      = [];
            if ($contents && ($contents != '')) {
                $contents_parser = urldecode($contents);
                $contents_array = json_decode($contents_parser, true);
            }
            if ($contents_array && isset($contents_array['variables']['urlKey'])) {
                $requestPath = $contents_array['variables']['urlKey'];

                $aw_blog = null;
                if ($requestPath[0] === '/') {
                    $requestPath = substr($requestPath, 1);
                }
                $path_rq = explode('/',$requestPath);
                if (count($path_rq) >= 2 && $path_rq[0] == 'blog'){
                    $aw_blog = $this->simiObjectManager
                        ->get('Aheadworks\Blog\Model\Post')
                        ->getCollection()
                        ->addFieldToFilter('url_key', $path_rq[1])->getFirstItem();
                    return array(
                        'id' => $aw_blog->getId(),
                        'canonical_url' => $aw_blog->getData('url_key'),
                        'relative_url' => 'simi_blog_page',
                        'type' => 'CMS_PAGE'
                    );
                }
            }
        }
        return $result;
    }
}
