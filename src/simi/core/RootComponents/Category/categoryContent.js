import React from 'react';
import { mergeClasses } from 'src/classify';
import Gallery from './Gallery';
import Pagination from 'src/components/Pagination';
import defaultClasses from './category.css';
import Identify from '/src/simi/Helper/Identify'
import LoadingSpiner from '/src/simi/BaseComponents/Loading/LoadingSpiner'
import Sortby from './Sortby'
import Filter from './Filter'

class CategoryContent extends React.Component {

    renderFilter() {
        const {props} = this
        const { data, filterData } = props;
        if (data && data.products &&
            data.products.filters) {
            return (
                <div>
                    <Filter data={data.products.filters} filterData={filterData}/>
                </div>
            );
        }
    }

    renderLeftNavigation = (classes) => {
        const shopby = [];
        const filter = this.renderFilter();
        if (filter) {
            shopby.push(
                <div 
                    key="siminia-left-navigation-filter" 
                    className={classes["left-navigation"]} >
                    {filter}
                </div>
            );
        }
        return shopby;
    }

    renderList = (classes) => {
        const {props} = this
        const { pageControl, data, pageSize, history, location, sortByData } = props;
        const items = data ? data.products.items : null;
        const title = data ? data.category.description : null;
        const pagination = (
            <div className={classes.pagination}>
                <Pagination pageControl={pageControl} />
            </div>
        )        
        if (data && data.products && !data.products.total_count)
            return(<div className={classes['no-product']}>{Identify.__('No product found')}</div>)

        return (
            <React.Fragment>
                <Sortby classes={classes} 
                    parent={this}
                    data={data}
                    sortByData={sortByData}
                    />
                <section className={classes.gallery}>
                    <Gallery data={items} title={title} pageSize={pageSize} history={history} location={location} />
                </section>
                {pagination}
            </React.Fragment>
        )
    }

    render() {
        const {props} = this
        const { data } = props;
        const classes = mergeClasses(defaultClasses, props.classes);
        const categoryTitle = data ? data.category.name : null;
        const title = data ? data.category.description : null;
        let itemCount = ''
        if(data && data.products && data.products.total_count){
            const text = data.products.total_count > 1 ? Identify.__('%t items') : Identify.__('%t item');
            itemCount = <div className={classes["items-count"]}>
                    {text
                        .replace('%t', data.products.total_count)}
                </div>;
        }
                
        return (
            <article className={classes.root}>
                <h1 className={classes.title}>
                    {/* TODO: Switch to RichContent component from Peregrine when merged */}
                    <div
                        dangerouslySetInnerHTML={{
                            __html: title
                        }}
                    />
                    <div className={classes.categoryTitle}>{categoryTitle}</div>
                </h1>
                {itemCount}
                <div className={classes["product-list-container-siminia"]}>
                    {this.renderLeftNavigation(classes)}
                    <div style={{display: 'inline-block', width: '100%'}}>
                        {this.renderList(classes)}
                    </div>
                </div>
            </article>
        );
    }
};

export default CategoryContent;
