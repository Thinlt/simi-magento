import React from 'react';
import "./style.css";

class Header extends React.Component {
  constructor(props) {
    super(props);
    const isPhone = window.innerWidth < 768;
    this.state = {
      isPhone
    };
  }

  setIsPhone() {
    const obj = this;
    const $ = window.$;
    $(window).resize(function () {
      const width = window.innerWidth;
      const isPhone = width < 1024;

      if (obj.state.isPhone !== isPhone) {
        obj.setState({
          isPhone
        });
      }
    });
  }

  componentDidMount() {
    this.setIsPhone();
  }

  render() {
    return React.createElement(React.Fragment, null, "header");
  }

}

export default Header;
//# sourceMappingURL=index.js.map