function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { shape, string } from 'prop-types';
import over from 'lodash.over';
import uuid from 'uuid/v4';
import classify from "../../classify";
import defaultClasses from "./toolTip.css";
/**
 * NOTE: The Tooltip component, as created, needs to be a parent of the
 * component it is a tooltip for. This is necessary for several reasons:
 *  - the Tooltip generates a uuid which is used for the aria-describedby
 *  - the Tooltip currently acts like a controller for event handlers.
 *
 * Once we move this component to a more general "Tooltip" component within
 * Peregrine we may change the parent-child relationship pattern. Please be
 * aware of this if you chose to reuse this component. -srugh
 */

class Tooltip extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "state", {
      isShowing: false
    });

    _defineProperty(this, "onBlur", () => {
      this.timeoutId = setTimeout(() => {
        this.setState({
          isShowing: false
        });
      }, 0);
    });

    _defineProperty(this, "onFocus", () => {
      // If a child receives focus, do not close the tooltip yet.
      clearTimeout(this.timeoutId);
      this.setState({
        isShowing: true
      });
    });

    _defineProperty(this, "onKeyDown", event => {
      if (event.key === 'Escape') {
        this.setState({
          isShowing: false
        });
      }
    });

    _defineProperty(this, "onMouseOver", () => {
      this.setState({
        isShowing: true
      });
    });

    _defineProperty(this, "onMouseLeave", () => {
      this.setState({
        isShowing: false
      });
    });

    this.timeoutId = null;
    this.uniqueId = uuid();
  }

  render() {
    const {
      text,
      children,
      classes
    } = this.props;
    const {
      isShowing
    } = this.state;
    const ariaEnhancedChildren = React.Children.map(children, child => {
      if (child.type !== 'button') {
        return child;
      } // The button triggers the tooltip.
      // Update its accessibility accordingly.


      const ariaEnhancedButton = React.cloneElement(child, {
        'aria-describedby': this.uniqueId,
        // We want to tack our event functionality onto whatever already exists.
        // lodash.over returns a function that calls each function
        // provided to it with the args it receives -
        // in this case, a React SyntheticEvent.
        onBlur: over([child.props.onBlur, this.onBlur]),
        onFocus: over([child.props.onFocus, this.onFocus]),
        onKeyDown: over([child.props.onKeyDown, this.onKeyDown]),
        onMouseOver: over([child.props.onMouseOver, this.onMouseOver]),
        onMouseLeave: over([child.props.onMouseLeave, this.onMouseLeave])
      });
      return ariaEnhancedButton;
    });
    return React.createElement("div", {
      className: classes.root
    }, isShowing && React.createElement("div", {
      className: classes.tooltip,
      id: this.uniqueId,
      role: "tooltip"
    }, text), ariaEnhancedChildren);
  }

}

_defineProperty(Tooltip, "propTypes", {
  classes: shape({
    root: string,
    tooltip: string
  }),
  text: string
});

export default classify(defaultClasses)(Tooltip);
//# sourceMappingURL=toolTip.js.map