import React from 'react';
import Gallery from './Gallery';
import Identify from 'src/simi/Helper/Identify'
import Sortby from './Sortby'
import Filter from './Filter'
import LoadMore from './loadMore'
import Loading from 'src/simi/BaseComponents/Loading'
import ReactHTMLParse from 'react-html-parser'
import RecentViewed from './recentViewed'
import Modal from 'react-responsive-modal'
import CompareProduct from '../CompareProducts/index'
require('./products.scss')

const $ = window.$;

class Products extends React.Component {
    constructor(props) {
        super(props)
        const isPhone = window.innerWidth < 1024 
        this.state = ({
            isPhone: isPhone,
            openMobileModel : false,
            openCompareModal: false
        })
        this.setIsPhone()
    }

    setIsPhone(){
        const obj = this;
        $(window).resize(function () {
            const width = window.innerWidth;
            const isPhone = width < 1024;
            if(obj.state.isPhone !== isPhone){
                obj.setState({isPhone})
            }
        })
    }

    renderFilter() {
        const {props} = this
        const { data, filterData } = props;
        if (data && data.products &&
            data.products.filters) {
            return (
                <div>
                    <span className="shopping-option">SHOPPING OPTION</span>
                    <Filter data={data.products.filters} filterData={filterData}/>
                </div>
            );
        }
    }

    showModalCompare = () => {
        this.setState({
            openCompareModal : true
        })
    }

    closeCompareModal = () =>{
        this.setState({
            openCompareModal : false
        })
    }

    renderLeftNavigation = () => {
        const shopby = [];
        const filter = this.renderFilter();
        if (filter) {
            shopby.push(
                <div 
                    key="siminia-left-navigation-filter" 
                    className="left-navigation" >
                    {filter}
                </div>
            );
        }
        return shopby;
    }

    renderItemCount = (data) => {
        if(data && data.products && data.products.total_count){
            const text = data.products.total_count > 1 ? Identify.__('%t products') : Identify.__('%t product');
            return (
                <div className="items-count">
                    {text
                        .replace('%t', data.products.total_count)}
                </div>
            )
        }
    }
    
    updateSetPage = (newPage)=>{
        const { pageSize, data, currentPage} = this.props
        if (newPage !== currentPage) {
            if (this.props.setCurrentPage && ((newPage-1)*pageSize < data.products.total_count))
                this.props.setCurrentPage(newPage)
        }
    };

    
    renderBottomFilterSort() {
        const {props} = this
        const { data, filterData, sortByData } = props;
        return (
            <React.Fragment>
                <Modal open={this.state.openMobileModel !== false} onClose={this.closeModalFilter} 
                    classNames={{overlay: Identify.isRtl()?"rtl-root":"", modal: "products-mobile-sort-filter-modal"}}>
                    <div className="modal-mobile-filter-view" style={{display: this.state.openMobileModel === 'filter' ? 'block' : 'none'}}>
                        <Filter data={data.products.filters} filterData={filterData}/>
                    </div>
                    <div className="modal-mobile-sort-view" style={{display: this.state.openMobileModel !== 'filter' ? 'block' : 'none'}}>
                        <div className="top-sort-by">
                            <Sortby  parent={this} data={data} sortByData={sortByData} />
                        </div>
                    </div>
                </Modal>
                <div className="mobile-bottom-filter">
                    <div className="mobile-bottom-filter-subitem" role="presentation" onClick={this.showModalFilter}>
                        <span className="mobile-bottom-btn icon-funnel">
                        </span>
                        <div className="mobile-bottom-btn-title">
                            {Identify.__('Filter')}
                        </div>
                    </div>
                    <div className="mobile-bottom-filter-subitem" role="presentation" onClick={this.showModalSortby}>
                        <div className="mobile-bottom-btn-title">
                            {Identify.__('Sort')}
                        </div>
                        <span className="mobile-bottom-btn icon-sort-amount-asc" >
                        </span>
                    </div>
                </div>
            </React.Fragment>
        )
    }

    componentDidMount(){
        if (this.state.isPhone) {
            $('.footer-app').addClass('has-bottom-filter');
        }
    }

    componentWillUnmount(){
        $('.footer-app').removeClass('has-bottom-filter');
    }

    showModalSortby = () => {
        this.setState({
            openMobileModel : 'sortby'
        })
    }

    showModalFilter = () => {
        this.setState({
            openMobileModel : 'filter'
        })
    }
    closeModalFilter = () =>{
        this.setState({
            openMobileModel : false
        })
    }

    renderList = () => {
        const {props} = this
        const { data, pageSize, history, location, sortByData, currentPage } = props;
        const items = data ? data.products.items : null;
        if (!data)
            return <Loading />
        if (!data.products || !data.products.total_count)
            return(<div className="no-product">{Identify.__('No product found')}</div>)
        return (
            <React.Fragment>
                {!this.state.isPhone ? 
                    <div className="top-sort-by">
                        <Sortby 
                            parent={this}
                            sortByData={sortByData}
                            />
                        {this.renderItemCount(data)}
                    </div> :
                    <div className="mobile-item-count">
                        {this.renderItemCount(data)}
                    </div>
                }
                <section className="gallery">
                    <CompareProduct history={history} openModal={this.state.openCompareModal} closeModal={this.closeCompareModal}/>
                    <Gallery openCompareModal={this.showModalCompare} data={items} pageSize={pageSize} history={history} location={location} />
                </section>
                <div className="product-grid-pagination" style={{marginBottom: 22}}>
                    <LoadMore 
                        updateSetPage={this.updateSetPage.bind(this)}
                        itemCount={data.products.total_count}
                        items={data.products.items}
                        limit={pageSize}
                        currentPage={currentPage}
                        loading={this.props.loading}
                        />
                </div>
            </React.Fragment>
        )
    }

    render() {
        const {props} = this
        const { data, title, cateEmpty } = props;
        let description = props.description
        if(!description && data&& data.category && data.category.description){
            description = data.category.description ? Identify.__('%t') : Identify.__('%t')
        }
        let descriptionArea = ''
        if (description)
            descriptionArea = <div className="description"> {ReactHTMLParse(description.replace('%t', data.category.description))} </div>;
                
        return (
            <article className="products-gallery-root">
                <h1 className="title">
                    <div className="categoryTitle">{title}</div>
                </h1>
                <h2 className="description-area">
                    {descriptionArea}
                </h2>
                {props.underHeader}
                {
                    !cateEmpty &&
                    <React.Fragment>
                        <div className="product-list-container-siminia">
                            {!this.state.isPhone && this.renderLeftNavigation()}
                            {this.state.isPhone && this.renderBottomFilterSort()}
                            <div className="listing-product">
                                {this.renderList()}
                            </div>
                        </div>
                        <div className="recent-viewed-product">
                            <RecentViewed />
                        </div>
                    </React.Fragment>
                }
            </article>
        );
    }
};


export default Products;

