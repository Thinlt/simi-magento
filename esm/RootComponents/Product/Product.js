function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { string, func } from 'prop-types';
import { connect, Query } from "@magento/venia-drivers";
import { addItemToCart } from "../../actions/cart";
import { loadingIndicator } from "../../components/LoadingIndicator";
import ProductFullDetail from "../../components/ProductFullDetail";
import getUrlKey from "../../util/getUrlKey";
import productQuery from "../../queries/getProductDetail.graphql";
/**
 * As of this writing, there is no single Product query type in the M2.3 schema.
 * The recommended solution is to use filter criteria on a Products query.
 * However, the `id` argument is not supported. See
 * https://github.com/magento/graphql-ce/issues/86
 * TODO: Replace with a single product query when possible.
 */

class Product extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "addToCart", async (item, quantity) => {
      const {
        addItemToCart,
        cartId
      } = this.props;
      await addItemToCart({
        cartId,
        item,
        quantity
      });
    });
  }

  componentDidMount() {
    window.scrollTo(0, 0);
  } // map Magento 2.3.1 schema changes to Venia 2.0.0 proptype shape to maintain backwards compatibility


  mapProduct(product) {
    const {
      description
    } = product;
    return _objectSpread({}, product, {
      description: typeof description === 'object' ? description.html : description
    });
  }

  render() {
    return React.createElement(Query, {
      query: productQuery,
      variables: {
        urlKey: getUrlKey(),
        onServer: false
      }
    }, ({
      loading,
      error,
      data
    }) => {
      if (error) return React.createElement("div", null, "Data Fetch Error");
      if (loading) return loadingIndicator;
      const product = data.productDetail.items[0];
      return React.createElement(ProductFullDetail, {
        product: this.mapProduct(product),
        addToCart: this.props.addItemToCart
      });
    });
  }

}

_defineProperty(Product, "propTypes", {
  addItemToCart: func.isRequired,
  cartId: string
});

const mapDispatchToProps = {
  addItemToCart
};
export default connect(null, mapDispatchToProps)(Product);
//# sourceMappingURL=Product.js.map