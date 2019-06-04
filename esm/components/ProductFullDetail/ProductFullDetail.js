import React, { Suspense, useCallback, useState } from 'react';
import { arrayOf, bool, func, number, shape, string } from 'prop-types';
import { Form } from 'informed';
import { Price } from '@magento/peregrine';
import defaultClasses from "./productFullDetail.css";
import { mergeClasses } from "../../classify";
import Button from "../Button";
import { loadingIndicator } from "../LoadingIndicator";
import Carousel from "../ProductImageCarousel";
import Quantity from "../ProductQuantity";
import RichText from "../RichText";
import appendOptionsToPayload from "../../util/appendOptionsToPayload";
import findMatchingVariant from "../../util/findMatchingProductVariant";
import isProductConfigurable from "../../util/isProductConfigurable";
const Options = React.lazy(() => import("../ProductOptions"));
const INITIAL_OPTION_CODES = new Map();
const INITIAL_OPTION_SELECTIONS = new Map();
const INITIAL_QUANTITY = 1;

const deriveOptionCodesFromProduct = product => {
  // If this is a simple product it has no option codes.
  if (!isProductConfigurable(product)) {
    return INITIAL_OPTION_CODES;
  } // Initialize optionCodes based on the options of the product.


  const initialOptionCodes = new Map();

  for (const _ref of product.configurable_options) {
    const {
      attribute_id,
      attribute_code
    } = _ref;
    initialOptionCodes.set(attribute_id, attribute_code);
  }

  return initialOptionCodes;
};

const getIsMissingOptions = (product, optionSelections) => {
  // Non-configurable products can't be missing options.
  if (!isProductConfigurable(product)) {
    return false;
  } // Configurable products are missing options if we have fewer
  // option selections than the product has options.


  const {
    configurable_options
  } = product;
  const numProductOptions = configurable_options.length;
  const numProductSelections = optionSelections.size;
  return numProductSelections < numProductOptions;
};

const getMediaGalleryEntries = (product, optionCodes, optionSelections) => {
  let value = [];
  const {
    media_gallery_entries,
    variants
  } = product;
  const isConfigurable = isProductConfigurable(product);
  const optionsSelected = optionSelections.size > 0;

  if (!isConfigurable || !optionsSelected) {
    value = media_gallery_entries;
  } else {
    const item = findMatchingVariant({
      optionCodes,
      optionSelections,
      variants
    });
    value = item ? [...item.product.media_gallery_entries, ...media_gallery_entries] : media_gallery_entries;
  }

  const key = value.reduce((fullKey, entry) => {
    return `${fullKey},${entry.file}`;
  }, '');
  return {
    key,
    value
  };
};

const ProductFullDetail = props => {
  // Props.
  const {
    addToCart,
    isAddingItem,
    product
  } = props; // State.

  const [quantity, setQuantity] = useState(INITIAL_QUANTITY);
  const [optionSelections, setOptionSelections] = useState(INITIAL_OPTION_SELECTIONS);
  const derivedOptionCodes = deriveOptionCodesFromProduct(product);
  const [optionCodes] = useState(derivedOptionCodes); // Members.

  const {
    amount: productPrice
  } = product.price.regularPrice;
  const classes = mergeClasses(defaultClasses, props.classes);
  const isMissingOptions = getIsMissingOptions(product, optionSelections);
  const mediaGalleryEntries = getMediaGalleryEntries(product, optionCodes, optionSelections); // Event handlers.

  const handleAddToCart = useCallback(() => {
    const payload = {
      item: product,
      productType: product.__typename,
      quantity
    };

    if (isProductConfigurable(product)) {
      appendOptionsToPayload(payload, optionSelections, optionCodes);
    }

    addToCart(payload);
  }, [addToCart, optionCodes, optionSelections, product, quantity]);
  const handleSelectionChange = useCallback((optionId, selection) => {
    // We must create a new Map here so that React knows that the value
    // of optionSelections has changed.
    const newOptionSelections = new Map([...optionSelections]);
    newOptionSelections.set(optionId, Array.from(selection).pop());
    setOptionSelections(newOptionSelections);
  }, [optionSelections]);
  return React.createElement(Form, {
    className: classes.root
  }, React.createElement("section", {
    className: classes.title
  }, React.createElement("h1", {
    className: classes.productName
  }, product.name), React.createElement("p", {
    className: classes.productPrice
  }, React.createElement(Price, {
    currencyCode: productPrice.currency,
    value: productPrice.value
  }))), React.createElement("section", {
    className: classes.imageCarousel
  }, React.createElement(Carousel, {
    images: mediaGalleryEntries.value,
    key: mediaGalleryEntries.key
  })), React.createElement("section", {
    className: classes.options
  }, React.createElement(Suspense, {
    fallback: loadingIndicator
  }, React.createElement(Options, {
    onSelectionChange: handleSelectionChange,
    product: product
  }))), React.createElement("section", {
    className: classes.quantity
  }, React.createElement("h2", {
    className: classes.quantityTitle
  }, "Quantity"), React.createElement(Quantity, {
    initialValue: quantity,
    onValueChange: setQuantity
  })), React.createElement("section", {
    className: classes.cartActions
  }, React.createElement(Button, {
    priority: "high",
    onClick: handleAddToCart,
    disabled: isAddingItem || isMissingOptions
  }, "Add to Cart")), React.createElement("section", {
    className: classes.description
  }, React.createElement("h2", {
    className: classes.descriptionTitle
  }, "Product Description"), React.createElement(RichText, {
    content: product.description
  })), React.createElement("section", {
    className: classes.details
  }, React.createElement("h2", {
    className: classes.detailsTitle
  }, "SKU"), React.createElement("strong", null, product.sku)));
};

ProductFullDetail.propTypes = {
  addToCart: func.isRequired,
  classes: shape({
    cartActions: string,
    description: string,
    descriptionTitle: string,
    details: string,
    detailsTitle: string,
    imageCarousel: string,
    options: string,
    productName: string,
    productPrice: string,
    quantity: string,
    quantityTitle: string,
    root: string,
    title: string
  }),
  isAddingItem: bool,
  product: shape({
    __typename: string,
    id: number,
    sku: string.isRequired,
    price: shape({
      regularPrice: shape({
        amount: shape({
          currency: string.isRequired,
          value: number.isRequired
        })
      }).isRequired
    }).isRequired,
    media_gallery_entries: arrayOf(shape({
      label: string,
      position: number,
      disabled: bool,
      file: string.isRequired
    })),
    description: string
  }).isRequired
};
export default ProductFullDetail;
//# sourceMappingURL=ProductFullDetail.js.map