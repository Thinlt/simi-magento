import React, { Component } from 'react';
import { string, func } from 'prop-types';

import { connect } from 'src/drivers';
import { Simiquery } from 'src/simi/Network/Query'
import { addItemToCart, getCartDetails } from 'src/actions/cart';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import Loading from 'src/simi/BaseComponents/Loading'
import ProductFullDetail from 'src/simi/App/core/RootComponents/Product/ProductFullDetail'
import Identify from 'src/simi/Helper/Identify'
import getProductDetailBySku from 'src/simi/queries/getProductDetailBySku.graphql'
import connectorGetProductDetailBySku from 'src/simi/queries/simiconnector/getProductDetailBySku.graphql'

/**
 * As of this writing, there is no single Product query type in the M2.3 schema.
 * The recommended solution is to use filter criteria on a Products query.
 * However, the `id` argument is not supported. See
 * https://github.com/magento/graphql-ce/issues/86
 * TODO: Replace with a single product query when possible.
 */
class Product extends Component {
    static propTypes = {
        addItemToCart: func.isRequired,
        cartId: string
    };

    addToCart = async (item, quantity) => {
        const { addItemToCart, cartId } = this.props;
        await addItemToCart({ cartId, item, quantity });
    };

    componentDidMount() {
        window.scrollTo(0, 0);
    }

    // map Magento 2.3.1 schema changes to Venia 2.0.0 proptype shape to maintain backwards compatibility
    mapProduct(product) {
        const { description } = product;
        return {
            ...product,
            description:
                typeof description === 'object' ? description.html : description
        };
    }

    render() {
        const sku = Identify.findGetParameter('sku')
        if (sku) {
            return (
                <Simiquery
                    query={Identify.hasConnector()?connectorGetProductDetailBySku:getProductDetailBySku}
                    variables={{ sku: sku, onServer: false }}
                >
                    {({ loading, error, data }) => {
                        if (error) return <div>Data Fetch Error</div>;
                        if (loading) return <Loading />;

                        const product = data.productDetail.items[0];
                        let simiExtraField = data.simiProductDetaileExtraField
                        simiExtraField = simiExtraField?JSON.parse(simiExtraField):null

                        return (
                            <ProductFullDetail
                                product={this.mapProduct(product)}
                                addToCart={this.props.addItemToCart}
                                getCartDetails={this.props.getCartDetails}
                                simiExtraField={simiExtraField}
                                toggleMessages={this.props.toggleMessages}
                            />
                        );
                    }}
                </Simiquery>
            )
        }
        return 'No SKU found'
    }
}

const mapDispatchToProps = {
    addItemToCart,
    getCartDetails,
    toggleMessages
};

export default connect(
    null,
    mapDispatchToProps
)(Product);
