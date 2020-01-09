<?php

namespace Simi\Simicustomize\Model\Api;

class Articles extends \Simi\Simiconnector\Model\Api\Apiabstract {

	public $DEFAULT_ORDER = 'id';

	public function setBuilderQuery() {
		$data               = $this->getData();
		$blog_enable = $this->scopeConfig->getValue('aw_blog/general/enabled');

		if ( $blog_enable ) {
			if ( $data['resourceid'] ) {
				$this->builderQuery = $this->simiObjectManager
					->get( 'Aheadworks\Blog\Model\Post' )->load( $data['resourceid'] );
			} else {
				$this->builderQuery = $this->getCollection();
			}

		} else {
			throw new \Exception( __( 'An error occurred while processing your request. Please try again later.' ), 4 );
		}
	}

	public function getCollection() {
		$data           = $this->getData();
		$blogCollection = $this->simiObjectManager
			->get( 'Aheadworks\Blog\Model\Post' )->getCollection()->addFieldToFilter( 'status', 'publication' );

		$blogCollection->setOrder( 'publish_date', 'DESC' )->load();

		$this->builderQuery = $blogCollection;

		return $blogCollection;
	}

	public function getCollectionById( $id ) {

		$collection = $blogCollection = $this->simiObjectManager
			->get( 'Aheadworks\Blog\Model\Post' )->getCollection()->addFieldToFilter( 'status', 'publication' )->setOrder( 'id', 'DESC' );

		$collection->getSelect()->join(
			[ 'post_category' => 'aw_blog_post_category' ],
			'post_category.post_id = main_table.id AND post_category.category_id = ' . $id
		)->limit( 3 );

		return $collection;
	}

	public function index() {
		$result = parent::index();

		$blog_title = $this->scopeConfig->getValue('aw_blog/general/blog_title');
		$blog_desc  = $this->scopeConfig->getValue('aw_blog/seo/meta_description');

		$result['config'] = [
			'meta_title'       => $blog_title,
			'meta_description' => $blog_desc,
		];

		foreach ( $result['articles'] as $index => $post ) {
			$result['articles'][ $index ]['featured_image_file'] = $post['featured_image_file'] ? $this->getMediaUrl( $post['featured_image_file'] ) : null;
		}

		$result['latest_posts']  = $this->getLatestPosts();

		return $result;
	}


	public function show() {
		$entity     = $this->builderQuery;
		$data       = $this->getData();
		$parameters = $data['params'];
		$fields     = [];
		if ( isset( $parameters['fields'] ) && $parameters['fields'] ) {
			$fields = explode( ',', $parameters['fields'] );
		}
		if ( $entity->getFeaturedImageFile() ) {
			$entity->setFeaturedImageFile( $this->getMediaUrl( $entity->getFeaturedImageFile() ) );
		}
		if ( $entity->getShortContent() ) {
			$short_ct = $this->simiObjectManager
				->get( 'Magento\Cms\Model\Template\FilterProvider' )
				->getPageFilter()->filter( $entity->getShortContent() );
			$entity->setShortContent( $short_ct );
		}
		if ( $entity->getContent() ) {
			$ct = $this->simiObjectManager
				->get( 'Magento\Cms\Model\Template\FilterProvider' )
				->getPageFilter()->filter( $entity->getContent() );
			$entity->setContent( $ct );
		}

		$info = $entity->toArray( $fields );

		return $info;
	}

	public function getLatestPosts() {
		$blogCollection = $this->simiObjectManager
			->get( 'Aheadworks\Blog\Model\Post' )->getCollection()->addFieldToFilter( 'status', 'publication' )->setOrder( 'publish_date', 'DESC' )->setPageSize( 20 )->getData();

		return $blogCollection;
	}
}