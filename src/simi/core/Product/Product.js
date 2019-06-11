import React, { Component } from 'react';
import { string, func } from 'prop-types';

import { connect, Query } from 'src/drivers';
import { addItemToCart } from 'src/actions/cart';
import { loadingIndicator } from 'src/components/LoadingIndicator';
import ProductFullDetail from 'src/components/ProductFullDetail'
import Identify from 'src/simi/Helper/Identify'
import getProductDetailBySku from 'src/simi/queries/getProductDetailBySku.graphql'

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
                <Query
                    query={getProductDetailBySku}
                    variables={{ sku: sku, onServer: false }}
                >
                    {({ loading, error, data }) => {
                        if (error) return <div>Data Fetch Error</div>;
                        if (loading) return loadingIndicator;

                        const product = data.productDetail.items[0];

                        return (
                            <ProductFullDetail
                                product={this.mapProduct(product)}
                                addToCart={this.props.addItemToCart}
                            />
                        );
                    }}
                </Query>
            )
        }
        return 'No SKU found'
    }
}

const mapDispatchToProps = {
    addItemToCart
};

export default connect(
    null,
    mapDispatchToProps
)(Product);
