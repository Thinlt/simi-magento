import React from 'react';
import PropTypes from 'prop-types';
import Identify from 'src/simi/Helper/Identify';
import Loading from 'src/simi/BaseComponents/Loading/ReactLoading'

class Pagination extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
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
                        onClick={()=>this.props.updateSetPage(items.length)}
                    >
                        <div
                            className="btn-load-more"
                        >
                            {
                                (this.props.loading) ?
                                <Loading /> :
                                Identify.__('See More')
                            }
                        </div>
                    </div>
                )
            }
            return (
                <div 
                    className="load-more"
                    role="presentation" 
                    onClick={()=>this.props.updateSetPage(0)}
                >
                    <div
                        className="btn-load-more"
                    >
                        {
                            (this.props.loading) ?
                            <Loading /> :
                            Identify.__('Show less')
                        }
                    </div>
                </div>
            )
        }
        return null
    }
}

Pagination.defaultProps = {
    currentPage: 1,
    itemCount: 0,
    items: [],
};
Pagination.propTypes = {
    currentPage: PropTypes.number,
    updateSetPage: PropTypes.func,
    itemCount: PropTypes.number,
    items: PropTypes.array,
};
export default Pagination;