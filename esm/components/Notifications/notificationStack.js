import React, { Component } from 'react';
import classify from "../../classify";
import defaultClasses from "./notificationStack.css";

class NotificationStack extends Component {
  render() {
    const {
      children,
      classes
    } = this.props;
    return React.createElement("div", {
      className: classes.root
    }, children);
  }

}

export default classify(defaultClasses)(NotificationStack);
//# sourceMappingURL=notificationStack.js.map