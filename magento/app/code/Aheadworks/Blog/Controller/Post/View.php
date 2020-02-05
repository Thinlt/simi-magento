<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Blog\Controller\Post;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class View
 * @package Aheadworks\Blog\Controller\Post
 */
class View extends \Aheadworks\Blog\Controller\Action
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $post = $this->postRepository->get(
                $this->getRequest()->getParam('post_id')
            );
            if (!$this->dataChecker->isPostVisible($post, $this->getStoreId())) {
                /**  @var \Magento\Framework\Controller\Result\Forward $forward */
                $forward = $this->resultForwardFactory->create();
                return $forward
                    ->setModule('cms')
                    ->setController('noroute')
                    ->forward('index');
            }
            $categoryId = $this->getRequest()->getParam('blog_category_id');
            $exclCategoryFromUrl = $this->urlTypeResolver->isCategoryExcl() && $categoryId ? true : false;
            if ($exclCategoryFromUrl || $categoryId && !in_array($categoryId, $post->getCategoryIds())) {
                // Forced redirect to post url without category id
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setUrl($this->url->getPostUrl($post));
                return $resultRedirect;
            }

            $title = $post->getMetaTitle() ? $post->getMetaTitle() : $post->getTitle();
            $resultPage = $this->resultPageFactory->create();
            $pageConfig = $resultPage->getConfig();
            $pageConfig->getTitle()->set($title);
            $pageConfig->setMetadata('description', $post->getMetaDescription());
            $pageConfig->addRemotePageAsset(
                $this->url->getCanonicalUrl($post),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
            return $resultPage;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->goBack();
        }
    }
}
