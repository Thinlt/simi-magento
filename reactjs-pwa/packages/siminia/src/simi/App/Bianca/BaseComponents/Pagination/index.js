import React from 'react';
import PropTypes from 'prop-types';
import Identify from 'src/simi/Helper/Identify';
import simicntrCategoryQuery from 'src/simi/queries/catalog/getCategory.graphql'

class Pagination extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            currentPage: this.props.currentPage,
            limit: this.props.limit,
            data: this.props.data,
            itemCount: this.props.itemCount,
        }
        this.startPage = 1;
    }

    renderItem = (item, index) => {
        return this.props.renderItem(item, index)
    };

    handleLoadMore = (totalProduct) =>{
        if( totalProduct - this.state.limit >= 9 ){
            this.setState(
                {   
                    limit: this.state.limit + 9,
                }
            )
        }
        else if( (totalProduct - this.state.limit > 0) && (totalProduct - this.state.limit < 9)){
            this.setState(
                {
                    limit: this.state.limit + 9,
                   
                }
            )
        }
    }

    renderLoadMore = (totalProduct) => {
        // Logic for displaying page numbers
        if (!this.props.showPageNumber) return null;
        return (
            <div className="btn-load-more"
                onClick={()=>this.handleLoadMore(totalProduct)}
            >
                {Identify.__('Load More')}
            </div>
        )
    };

    renderPagination = () => {
        const { data, currentPage, limit, itemCount, showLoadMore } = this.state;
        if (itemCount > 0) {
            this.renderItem()
            if(limit <= itemCount) {
                return (
                    <div className="load-more"
                    >
                        {this.renderLoadMore(itemCount)}
                    </div>
                )
            }
            
        }
        return <div></div>
    }

    render() {
        return this.renderPagination();
    }
}
/*
data OR itemCount is required to calculate pages count
 */

Pagination.defaultProps = {
    currentPage: 1,
    limit: 5,
    data: [],
    itemCount: 0,
    itemsPerPageOptions: [5, 10, 15, 20],
    table: false,
    showPageNumber: true,
    showInfoItem: true,
    classes: {},
};
Pagination.propTypes = {
    currentPage: PropTypes.number,
    limit: PropTypes.number,
    data: PropTypes.array,
    renderItem: PropTypes.func,
    itemCount: PropTypes.number,
    itemsPerPageOptions: PropTypes.array,
    classes: PropTypes.object,
    changedPage: PropTypes.func,
    changeLimit: PropTypes.func,
};
export default Pagination;