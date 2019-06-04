import React, { Component } from 'react';
import { loadingIndicator } from "../LoadingIndicator";
const messages = new Map().set('loading', loadingIndicator).set('notFound', 'That page could not be found. Please try again.').set('internalError', 'Something went wrong. Please try again.');

class ErrorView extends Component {
  render() {
    const {
      loading,
      notFound
    } = this.props;
    const message = loading ? messages.get('loading') : notFound ? messages.get('notFound') : messages.get('internalError');
    return React.createElement("h1", null, message);
  }

}

export default ErrorView;
//# sourceMappingURL=errorView.js.map