import React from 'react';
import PropTypes from 'prop-types';
import Identify from 'src/simi/Helper/Identify';
import Loading from 'src/simi/BaseComponents/Loading/ReactLoading'

class Pagination extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            currentPage: this.props.currentPage,
            data: this.props.data,
            itemCount: this.props.itemCount
        }
        this.latestCount = 0
    }

    shouldComponentUpdate(nextProps){
        if (nextProps.items && nextProps.items.length !== this.latestCount) {
            this.latestCount = nextProps.items.length
            return true
        }
        return false
    }

    renderItem = () => {
        return this.props.renderItem()
    };

    handleLoadMore = () =>{
        this.setState({currentPage: this.state.currentPage + 1})
        this.renderItem()
    }

    render() {
        const { currentPage, itemCount } = this.state;
        const { items } = this.props;
        if (itemCount > 0) {
            this.renderItem()
            if(items.length < itemCount) {
                return (
                    <div className="load-more">
                        <div
                            role="presentation" 
                            className="btn-load-more"
                            onClick={()=>this.handleLoadMore()}
                        >
                            {
                                (currentPage !== this.props.currentPage) ?
                                <Loading divStyle={{marginTop: '-25px'}} loadingStyle={{fill: 'white'}}/> :
                                Identify.__('Load More')
                            }
                        </div>
                    </div>
                )
            }
        }
        return <div></div>
    }
}

Pagination.defaultProps = {
    currentPage: 1,
    limit: 5,
    data: [],
    itemCount: 0,
    items: [],
};
Pagination.propTypes = {
    currentPage: PropTypes.number,
    limit: PropTypes.number,
    data: PropTypes.array,
    renderItem: PropTypes.func,
    itemCount: PropTypes.number,
    items: PropTypes.array,
};
export default Pagination;