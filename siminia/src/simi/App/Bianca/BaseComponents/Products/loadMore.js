import React from 'react';
import PropTypes from 'prop-types';
import Identify from 'src/simi/Helper/Identify';
import Loading from 'src/simi/BaseComponents/Loading/ReactLoading'

class Pagination extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: this.props.data,
            itemCount: this.props.itemCount
        }
        this.latestCount = 0
    }
    
    render() {
        const { items, itemCount } = this.props;
        if (itemCount > 0) {
            if(items.length < itemCount) {
                return (
                    <div 
                        className="load-more"
                        role="presentation" 
                        onClick={()=>this.props.updateSetPage(this.props.currentPage + 1)}
                    >
                        <div
                            className="btn-load-more"
                        >
                            {
                                (this.props.loading) ?
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
    updateSetPage: PropTypes.func,
    itemCount: PropTypes.number,
    items: PropTypes.array,
};
export default Pagination;