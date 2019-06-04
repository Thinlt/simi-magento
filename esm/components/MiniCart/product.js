function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { arrayOf, func, number, shape, string } from 'prop-types';
import { Price } from '@magento/peregrine';
import { resourceUrl } from "@magento/venia-drivers";
import Kebab from "./kebab";
import Section from "./section";
import classify from "../../classify";
import defaultClasses from "./product.css";
const imageWidth = 80;
const imageHeight = 100;

class Product extends Component {
  // TODO: Manage favorite items using GraphQL/REST when it is ready
  constructor() {
    super();

    _defineProperty(this, "favoriteItem", () => {
      this.setState({
        isFavorite: true
      });
    });

    _defineProperty(this, "editItem", () => {
      this.props.openOptionsDrawer(this.props.item);
    });

    _defineProperty(this, "removeItem", () => {
      this.setState({
        isLoading: true
      }); // TODO: prompt user to confirm this action

      this.props.removeItemFromCart({
        item: this.props.item
      });
    });

    this.state = {
      isLoading: false,
      isFavorite: false
    };
  }

  get options() {
    const {
      classes,
      item
    } = this.props;
    const options = item.options;
    return options && options.length > 0 ? React.createElement("dl", {
      className: classes.options
    }, options.map(({
      label,
      value
    }) => React.createElement(Fragment, {
      key: `${label}${value}`
    }, React.createElement("dt", {
      className: classes.optionLabel
    }, label, " : ", value)))) : null;
  }

  get mask() {
    const {
      classes
    } = this.props;
    return this.state.isLoading ? React.createElement("div", {
      className: classes.mask
    }) : null;
  }

  styleImage(image) {
    return {
      minHeight: imageHeight,
      // min-height instead of height so image will always align with grid bottom
      width: imageWidth,
      backgroundImage: `url(${resourceUrl(image.file, {
        type: 'image-product',
        width: imageWidth
      })})`
    };
  }

  render() {
    const {
      options,
      props,
      mask
    } = this;
    const {
      classes,
      item,
      currencyCode
    } = props;
    const favoritesFill = {
      fill: 'rgb(var(--venia-teal))'
    };
    return React.createElement("li", {
      className: classes.root
    }, React.createElement("div", {
      className: classes.image,
      style: this.styleImage(item.image)
    }), React.createElement("div", {
      className: classes.name
    }, item.name), options, React.createElement("div", {
      className: classes.quantity
    }, React.createElement("div", {
      className: classes.quantityRow
    }, React.createElement("span", null, item.qty), React.createElement("span", {
      className: classes.quantityOperator
    }, '×'), React.createElement("span", {
      className: classes.price
    }, React.createElement(Price, {
      currencyCode: currencyCode,
      value: item.price
    })))), mask, React.createElement(Kebab, null, React.createElement(Section, {
      text: "Add to favorites",
      onClick: this.favoriteItem,
      icon: "Heart",
      iconAttributes: this.state.isFavorite ? favoritesFill : {}
    }), React.createElement(Section, {
      text: "Edit item",
      onClick: this.editItem,
      icon: "Edit2"
    }), React.createElement(Section, {
      text: "Remove item",
      onClick: this.removeItem,
      icon: "Trash"
    })));
  }

}

_defineProperty(Product, "propTypes", {
  classes: shape({
    image: string,
    modal: string,
    name: string,
    optionLabel: string,
    options: string,
    price: string,
    quantity: string,
    quantityOperator: string,
    quantityRow: string,
    quantitySelect: string,
    root: string
  }),
  item: shape({
    item_id: number.isRequired,
    name: string.isRequired,
    options: arrayOf(shape({
      label: string,
      value: string
    })),
    price: number.isRequired,
    product_type: string,
    qty: number.isRequired,
    quote_id: string,
    sku: string.isRequired
  }).isRequired,
  currencyCode: string.isRequired,
  openOptionsDrawer: func.isRequired
});

export default classify(defaultClasses)(Product);
//# sourceMappingURL=product.js.map