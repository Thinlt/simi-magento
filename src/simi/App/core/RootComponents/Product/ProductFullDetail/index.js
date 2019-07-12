import ProductFullDetail from './ProductFullDetail';

import { connect } from 'src/drivers';
import { addItemToCart, getCartDetails } from 'src/actions/cart';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';

const mapDispatchToProps = {
    addItemToCart,
    getCartDetails,
    toggleMessages
};

export default connect(
    null,
    mapDispatchToProps
)(ProductFullDetail);
