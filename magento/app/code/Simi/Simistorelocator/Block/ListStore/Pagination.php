<?php

namespace Simi\Simistorelocator\Block\ListStore;

class Pagination extends \Simi\Simistorelocator\Block\AbstractBlock {

    const FIRST_PAGE = 1;

    /**
     * template.
     *
     * @var string
     */
    protected $_template = 'Simi_Simistorelocator::liststore/pagination.phtml';

    /**
     * @var int
     */
    public $minPage;

    /**
     * @var int
     */
    public $maxPage;

    /**
     * @var \Magento\Framework\Data\Collection
     */
    public $collection;

    /**
     * Block constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Simi\Simistorelocator\Block\Context $context,
        \Magento\Framework\Data\Collection $collection = null, array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collection = $collection;
    }

    /**
     * get collection.
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection() {
        return $this->collection;
    }

    /**
     * set collection for pagination.
     *
     * @param \Magento\Framework\Data\Collection $collection
     */
    public function setCollection(\Magento\Framework\Data\Collection $collection) {
        $this->collection = $collection;
    }

    /**
     * Internal constructor, that is called from real constructor.
     */
    protected function _construct() {
        parent::_construct();

        if (!$this->hasData('range')) {
            $this->setData('range', 5);
        }
    }

    /**
     * @return mixed
     */
    public function getMinPage() {
        return $this->minPage;
    }

    /**
     * @param mixed $minPage
     */
    public function setMinPage($minPage) {
        $this->minPage = $minPage;
    }

    /**
     * @return mixed
     */
    public function getMaxPage() {
        return $this->maxPage;
    }

    /**
     * @param mixed $maxPage
     */
    public function setMaxPage($maxPage) {
        $this->maxPage = $maxPage;
    }

    /**
      /**
     * @return mixed
     */
    public function getPageSize() {
        return $this->getCollection()->getPageSize();
    }

    /**
     * @return mixed
     */
    public function getCurPage() {
        return $this->getCollection()->getCurPage();
    }

    /**
     * check has next page.
     *
     * @return bool
     */
    public function hasNextPage() {
        return $this->getCurPage() < $this->getTotalPage();
    }

    /**
     * check has previous page.
     *
     * @return bool
     */
    public function hasPrevPage() {
        return $this->getCurPage() > self::FIRST_PAGE;
    }

    /**
     * @return mixed
     */
    public function getNextPage() {
        return $this->hasNextPage() ? $this->getCurPage() + 1 : $this->getTotalPage();
    }

    public function getPrevPage() {
        return $this->hasPrevPage() ? $this->getCurPage() - 1 : $this->getTotalPage();
    }

    /**
     * @return mixed
     */
    public function getTotalPage() {
        return $this->getCollection()->getLastPageNumber();
    }

    /**
     * check current page is the first page.
     *
     * @param $page
     *
     * @return bool
     */
    public function currentIsFirstPage() {
        return $this->getCurPage() == self::FIRST_PAGE;
    }

    /**
     * check current page is last page.
     *
     * @param $page
     *
     * @return bool
     */
    public function currentIsLastPage() {
        return $this->getCurPage() == $this->getTotalPage();
    }

    /**
     * @return $this
     */
    protected function _preparePagination() {
        $middle = ceil($this->getRange() / 2);
        $totalPage = $this->getTotalPage();

        if ($totalPage < $this->getRange()) {
            $this->setMinPage(self::FIRST_PAGE);
            $this->setMaxPage($totalPage);
        } else {
            $this->setMinPage($this->getCurPage() - $middle + 1);
            $this->setMaxPage($this->getCurPage() + $middle - 1);

            if ($this->getMinPage() < self::FIRST_PAGE) {
                $this->setMinPage(self::FIRST_PAGE);
                $this->setMaxPage($this->getRange());
            } elseif ($this->getMaxPage() > $totalPage) {
                $this->setMinPage($totalPage - $this->getRange() + 1);
                $this->setMaxPage($totalPage);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout() {
        $this->_preparePagination();

        return $this;
    }

    /**
     * Set collection page size.
     *
     * @param int $size
     *
     * @return $this
     */
    public function setPageSize($size) {
        $this->getCollection()->setPageSize($size);

        return $this;
    }

    /**
     * Set current page.
     *
     * @param int $page
     *
     * @return $this
     */
    public function setCurPage($page) {
        $this->getCollection()->setCurPage($page);

        return $this;
    }

}
