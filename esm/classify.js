function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import getDisplayName from "./util/getDisplayName";
export const mergeClasses = (...args) => Object.assign({}, ...args);

const classify = defaultClasses => WrappedComponent => {
  var _class, _temp;

  return _temp = _class = class extends Component {
    render() {
      const _this$props = this.props,
            {
        className,
        classes
      } = _this$props,
            restProps = _objectWithoutProperties(_this$props, ["className", "classes"]);

      const classNameAsObject = className ? {
        root: className
      } : null;
      const finalClasses = mergeClasses(defaultClasses, classNameAsObject, classes);
      return React.createElement(WrappedComponent, _extends({}, restProps, {
        classes: finalClasses
      }));
    }

  }, _defineProperty(_class, "displayName", `Classify(${getDisplayName(WrappedComponent)})`), _temp;
};

export default classify;
//# sourceMappingURL=classify.js.map