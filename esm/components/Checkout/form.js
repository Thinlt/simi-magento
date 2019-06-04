function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { array, bool, func, object, oneOf, shape, string } from 'prop-types';
import { Price } from '@magento/peregrine';
import AddressForm from "./addressForm";
import PaymentsForm from "./paymentsForm";
import Section from "./section";
import ShippingForm from "./shippingForm";
import SubmitButton from "./submitButton";
import classify from "../../classify";
import Button from "../Button";
import defaultClasses from "./form.css";

class Form extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "dismissCheckout", () => {
      this.props.cancelCheckout();
    });

    _defineProperty(this, "editAddress", () => {
      this.props.editOrder('address');
    });

    _defineProperty(this, "editPaymentMethod", () => {
      this.props.editOrder('paymentMethod');
    });

    _defineProperty(this, "editShippingMethod", () => {
      this.props.editOrder('shippingMethod');
    });

    _defineProperty(this, "stopEditing", () => {
      this.props.editOrder(null);
    });

    _defineProperty(this, "submitShippingAddress", formValues => {
      this.props.submitShippingAddress({
        type: 'shippingAddress',
        formValues
      });
    });

    _defineProperty(this, "submitPaymentMethodAndBillingAddress", formValues => {
      this.props.submitPaymentMethodAndBillingAddress({
        formValues
      });
    });

    _defineProperty(this, "submitShippingMethod", formValues => {
      this.props.submitShippingMethod({
        type: 'shippingMethod',
        formValues
      });
    });
  }

  /*
   *  Class Properties.
   */
  get editableForm() {
    const {
      editing,
      submitting,
      isAddressIncorrect,
      incorrectAddressMessage,
      directory
    } = this.props;
    const {
      countries
    } = directory;

    switch (editing) {
      case 'address':
        {
          const {
            shippingAddress
          } = this.props;
          return React.createElement(AddressForm, {
            initialValues: shippingAddress,
            submitting: submitting,
            countries: countries,
            cancel: this.stopEditing,
            submit: this.submitShippingAddress,
            isAddressIncorrect: isAddressIncorrect,
            incorrectAddressMessage: incorrectAddressMessage
          });
        }

      case 'paymentMethod':
        {
          const {
            billingAddress
          } = this.props;
          return React.createElement(PaymentsForm, {
            cancel: this.stopEditing,
            initialValues: billingAddress,
            submit: this.submitPaymentMethodAndBillingAddress,
            submitting: submitting,
            countries: countries
          });
        }

      case 'shippingMethod':
        {
          const {
            availableShippingMethods,
            shippingMethod
          } = this.props;
          return React.createElement(ShippingForm, {
            availableShippingMethods: availableShippingMethods,
            cancel: this.stopEditing,
            shippingMethod: shippingMethod,
            submit: this.submitShippingMethod,
            submitting: submitting
          });
        }

      default:
        {
          return null;
        }
    }
  }

  get overview() {
    const {
      cart,
      classes,
      hasPaymentMethod,
      hasShippingAddress,
      hasShippingMethod,
      ready,
      submitOrder,
      submitting
    } = this.props;
    return React.createElement(Fragment, null, React.createElement("div", {
      className: classes.body
    }, React.createElement(Section, {
      label: "Ship To",
      onClick: this.editAddress,
      showEditIcon: hasShippingAddress
    }, this.shippingAddressSummary), React.createElement(Section, {
      label: "Pay With",
      onClick: this.editPaymentMethod,
      showEditIcon: hasPaymentMethod
    }, this.paymentMethodSummary), React.createElement(Section, {
      label: "Use",
      onClick: this.editShippingMethod,
      showEditIcon: hasShippingMethod
    }, this.shippingMethodSummary), React.createElement(Section, {
      label: "TOTAL"
    }, React.createElement(Price, {
      currencyCode: cart.totals.quote_currency_code,
      value: cart.totals.subtotal || 0
    }), React.createElement("br", null), React.createElement("span", null, cart.details.items_qty, " Items"))), React.createElement("div", {
      className: classes.footer
    }, React.createElement(Button, {
      onClick: this.dismissCheckout
    }, "Back to Cart"), React.createElement(SubmitButton, {
      submitting: submitting,
      valid: ready,
      submitOrder: submitOrder
    })));
  }

  get paymentMethodSummary() {
    const {
      classes,
      hasPaymentMethod,
      paymentData
    } = this.props;

    if (!hasPaymentMethod) {
      return React.createElement("span", {
        className: classes.informationPrompt
      }, "Add Billing Information");
    }

    let primaryDisplay = '';
    let secondaryDisplay = '';

    if (paymentData) {
      primaryDisplay = paymentData.details.cardType;
      secondaryDisplay = paymentData.description;
    }

    return React.createElement(Fragment, null, React.createElement("strong", {
      className: classes.paymentDisplayPrimary
    }, primaryDisplay), React.createElement("br", null), React.createElement("span", {
      className: classes.paymentDisplaySecondary
    }, secondaryDisplay));
  }

  get shippingAddressSummary() {
    const {
      classes,
      hasShippingAddress,
      shippingAddress
    } = this.props;

    if (!hasShippingAddress) {
      return React.createElement("span", {
        className: classes.informationPrompt
      }, "Add Shipping Information");
    }

    const name = `${shippingAddress.firstname} ${shippingAddress.lastname}`;
    const street = `${shippingAddress.street.join(' ')}`;
    return React.createElement(Fragment, null, React.createElement("strong", null, name), React.createElement("br", null), React.createElement("span", null, street));
  }

  get shippingMethodSummary() {
    const {
      classes,
      hasShippingMethod,
      shippingTitle
    } = this.props;

    if (!hasShippingMethod) {
      return React.createElement("span", {
        className: classes.informationPrompt
      }, "Specify Shipping Method");
    }

    return React.createElement(Fragment, null, React.createElement("strong", null, shippingTitle));
  }
  /*
   *  Component Lifecycle Methods.
   */


  render() {
    const {
      classes,
      editing
    } = this.props;
    const children = editing ? this.editableForm : this.overview;
    return React.createElement("div", {
      className: classes.root
    }, children);
  }
  /*
   *  Event Handlers.
   */


}

_defineProperty(Form, "propTypes", {
  availableShippingMethods: array,
  billingAddress: shape({
    city: string,
    country_id: string,
    email: string,
    firstname: string,
    lastname: string,
    postcode: string,
    region_id: string,
    region_code: string,
    region: string,
    street: array,
    telephone: string
  }),
  cancelCheckout: func.isRequired,
  cart: shape({
    details: object,
    cartId: string,
    totals: object
  }).isRequired,
  directory: shape({
    countries: array
  }).isRequired,
  classes: shape({
    body: string,
    footer: string,
    informationPrompt: string,
    'informationPrompt--disabled': string,
    paymentDisplayPrimary: string,
    paymentDisplaySecondary: string,
    root: string
  }),
  editing: oneOf(['address', 'paymentMethod', 'shippingMethod']),
  editOrder: func.isRequired,
  hasPaymentMethod: bool,
  hasShippingAddress: bool,
  hasShippingMethod: bool,
  incorrectAddressMessage: string,
  isAddressIncorrect: bool,
  paymentData: shape({
    description: string,
    details: shape({
      cardType: string
    }),
    nonce: string
  }),
  ready: bool,
  shippingAddress: shape({
    city: string,
    country_id: string,
    email: string,
    firstname: string,
    lastname: string,
    postcode: string,
    region_id: string,
    region_code: string,
    region: string,
    street: array,
    telephone: string
  }),
  shippingMethod: string,
  shippingTitle: string,
  submitShippingAddress: func.isRequired,
  submitOrder: func.isRequired,
  submitPaymentMethodAndBillingAddress: func.isRequired,
  submitShippingMethod: func.isRequired,
  submitting: bool.isRequired
});

export default classify(defaultClasses)(Form);
//# sourceMappingURL=form.js.map