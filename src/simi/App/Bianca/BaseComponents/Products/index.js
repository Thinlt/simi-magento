import React from 'react';
import Gallery from './Gallery';
import Identify from 'src/simi/Helper/Identify'
import Sortby from './Sortby'
import Filter from './Filter'
import Pagination from 'src/simi/App/Bianca/BaseComponents/Pagination'
import Loading from 'src/simi/BaseComponents/Loading'
import {Carousel} from 'react-responsive-carousel';
import ReactHTMLParse from 'react-html-parser'
require('./products.scss')

class Products extends React.Component {

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

    renderLeftNavigation = () => {
        const shopby = [];
        const filter = this.renderFilter();
        if (filter) {
            shopby.push(
                <div 
                    key="siminia-left-navigation-filter" 
                    className="left-navigation" >
                    {filter}
                    <div className="left-nav-pcompare">
                        <div className="left-nav-pcompare-title">
                            {Identify.__('Compare product')}
                        </div>   
                        <div className="left-nav-pcompare-content">
                            {Identify.__('You have  no products to compare')}
                        </div>    
                    </div>
                </div>
            );
        }
        return shopby;
    }

    renderItem = ()=>{
        const {pagination} = this
        const {history, location, currentPage, pageSize} = this.props
        if (
            pagination && 
            pagination.state && 
            pagination.state.limit && 
            pagination.state.currentPage &&
            (pagination.state.limit!==pageSize||
            pagination.state.currentPage!==currentPage)) {
                const { search } = location;
                const queryParams = new URLSearchParams(search);
                queryParams.set('product_list_limit', pagination.state.limit);
                queryParams.set('page', pagination.state.currentPage);
                history.push({ search: queryParams.toString() });
        }
    };

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
                <Sortby 
                    parent={this}
                    data={data}
                    sortByData={sortByData}
                    />
                <section className="gallery">
                    <Gallery data={items} pageSize={pageSize} history={history} location={location} />
                </section>
                <div className="product-grid-pagination" style={{marginBottom: 20}}>
                    <Pagination 
                        renderItem={this.renderItem.bind(this)}
                        itemCount={data.products.total_count}
                        limit={pageSize}
                        currentPage={currentPage}
                        itemsPerPageOptions={[9, 18, 27, 36, 45]}
                        showInfoItem={false}
                        ref={(page) => {this.pagination = page}}/>
                </div>
            </React.Fragment>
        )
    }

    renderRecentViewedProduct = () => {
        return(
            <div>

            </div>
        )
    }

    openProductDetail = (item) => {
        console.log('click');
    }

    render() {
        const {props} = this
        const { data, title } = props;
        let descriptionArea = ''
        console.log(data)
        if(data&& data.category && data.category.description){
            const description = data.category.description ? Identify.__('%t') : Identify.__('%t')
            descriptionArea = <div className="description">
                                {ReactHTMLParse(description.replace('%t', data.category.description))}                 
                            </div>;
        }
                
        return (
            <article className="products-gallery-root">
                <h1 className="title">
                    <div className="categoryTitle">{title}</div>
                </h1>
                <h2 className="description-area">
                    {descriptionArea}
                </h2>
                {props.underHeader}
                <div className="product-list-container-siminia">
                    {this.renderLeftNavigation()}
                    <div className="listing-product">
                        {this.renderList()}
                    </div>
                </div>
                <div className="recent-viewed-product">
                    <div className="recent-viewed-title">
                        {Identify.__('Recently Viewed Products')}
                    </div>
                    <div className="recent-viewed-slide">
                        <Carousel 
                            key={Identify.randomString(5)}
                            showArrows={true}  
                            showThumbs={false}
                            showIndicators={true}
                            onClickItem={(e) => this.openProductDetail(e)}
                            infiniteLoop={true}
                            autoPlay={true}
                        >
                            {this.renderRecentViewedProduct()}
                        </Carousel>
                    </div>
                </div>
            </article>
        );
    }
};


export default Products;

