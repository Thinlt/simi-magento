import ProductFullDetail from './ProductFullDetail';

import { connect } from 'src/drivers';
import { addItemToCart, getCartDetails } from 'src/actions/cart';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { withRouter } from 'react-router-dom';
import { compose } from 'redux';

const mapDispatchToProps = {
    addItemToCart,
    getCartDetails,
    toggleMessages
};

const mapStateToProps = ({ user }) => { 
    const { isSignedIn } = user;
    return {
        isSignedIn
    }; 
};
export default compose(
    withRouter,
    connect(
        mapStateToProps,
        mapDispatchToProps
    )
)(ProductFullDetail);
