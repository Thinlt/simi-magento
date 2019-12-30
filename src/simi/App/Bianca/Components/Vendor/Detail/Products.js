import React from 'react';
// import Modal from 'react-responsive-modal';
// import ReactHTMLParse from 'react-html-parser';
import Identify from 'src/simi/Helper/Identify';
import Loading from 'src/simi/BaseComponents/Loading';
import Sortby from 'src/simi/App/Bianca/Components/Vendor/Detail/Sortby';
// import Filter from 'src/simi/App/Bianca/BaseComponents/Products/Filter';
import Gallery from 'src/simi/App/Bianca/BaseComponents/Products/Gallery';
import LoadMore from 'src/simi/App/Bianca/BaseComponents/Products/loadMore';
// import RecentViewed from 'src/simi/App/Bianca/BaseComponents/Products/recentViewed';
import CompareProduct from 'src/simi/App/Bianca/BaseComponents/CompareProducts/index';
require('src/simi/App/Bianca/BaseComponents/Products/products.scss');

const $ = window.$;

class Products extends React.Component {
    constructor(props) {
        super(props)
        this.state = ({
            isPhone: props.isPhone,
            openMobileModel : false,
            openCompareModal: false
        })
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
        const { data, pageSize, history, location, sortByData, currentPage, isPhone } = props;
        const items = data ? data.products.items : null;
        if (!data)
            return <Loading />
        if (!data.products || !data.products.total_count)
            return(<div className="no-product">{Identify.__('No product found')}</div>)
        return (
            <React.Fragment>
                <div className="top-sort-by">
                    <Sortby parent={this} sortByData={sortByData} isPhone={isPhone}/>
                    {this.renderItemCount(data)}
                </div>
                <section className="gallery">
                    <CompareProduct openModal={this.state.openCompareModal} closeModal={this.closeCompareModal}/>
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
        return (
            <article className="products-gallery-root">
                <div className="product-list-container-siminia">
                    {/* {!this.state.isPhone && this.renderLeftNavigation()}
                    {this.state.isPhone && this.renderBottomFilterSort()} */}
                    <div className="listing-product">
                        {this.renderList()}
                    </div>
                </div>
            </article>
        );
    }
};


export default Products;