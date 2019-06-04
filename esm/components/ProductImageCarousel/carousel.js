function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import memoize from 'memoize-one';
import { resourceUrl } from "@magento/venia-drivers";
import Icon from "../Icon";
import ChevronLeftIcon from 'react-feather/dist/icons/chevron-left';
import ChevronRightIcon from 'react-feather/dist/icons/chevron-right';
import classify from "../../classify";
import ThumbnailList from "./thumbnailList";
import defaultClasses from "./carousel.css";
import { transparentPlaceholder } from "../../shared/images";
const ChevronIcons = {
  left: ChevronLeftIcon,
  right: ChevronRightIcon
};

class Carousel extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "state", {
      activeItemIndex: 0,
      currentImageLoaded: false
    });

    _defineProperty(this, "updateActiveItemIndex", index => {
      this.setState({
        activeItemIndex: index
      });
    });

    _defineProperty(this, "sortAndFilterImages", memoize(items => items.filter(i => !i.disabled).sort((a, b) => {
      const aPos = isNaN(a.position) ? 9999 : a.position;
      const bPos = isNaN(b.position) ? 9999 : b.position;
      return aPos - bPos;
    })));

    _defineProperty(this, "leftChevronHandler", () => {
      const sortedImages = this.sortedImages;
      const {
        activeItemIndex
      } = this.state;
      activeItemIndex > 0 ? this.updateActiveItemIndex(activeItemIndex - 1) : this.updateActiveItemIndex(sortedImages.length - 1);
    });

    _defineProperty(this, "rightChevronHandler", () => {
      const sortedImages = this.sortedImages;
      const {
        activeItemIndex
      } = this.state;
      this.updateActiveItemIndex((activeItemIndex + 1) % sortedImages.length);
    });

    _defineProperty(this, "getChevron", direction => React.createElement("button", {
      onClick: this[`${direction}ChevronHandler`],
      className: this.props.classes[`chevron-${direction}`]
    }, React.createElement(Icon, {
      src: ChevronIcons[direction],
      size: 40
    })));

    _defineProperty(this, "setCurrentImageLoaded", () => {
      this.setState({
        currentImageLoaded: true
      });
    });
  }

  get sortedImages() {
    const {
      images
    } = this.props;
    return this.sortAndFilterImages(images);
  }

  render() {
    const {
      classes
    } = this.props;
    const sortedImages = this.sortedImages;
    const mainImage = sortedImages[this.state.activeItemIndex] || {};
    const src = mainImage.file ? resourceUrl(mainImage.file, {
      type: 'image-product',
      width: 640
    }) : transparentPlaceholder;
    const alt = mainImage.label || 'image-product';
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("div", {
      className: classes.imageContainer
    }, this.getChevron('left'), React.createElement("img", {
      onLoad: this.setCurrentImageLoaded,
      className: classes.currentImage,
      src: src,
      alt: alt
    }), !this.state.currentImageLoaded ? React.createElement("img", {
      className: classes.currentImage,
      src: transparentPlaceholder,
      alt: alt
    }) : null, this.getChevron('right')), React.createElement(ThumbnailList, {
      items: sortedImages,
      activeItemIndex: this.state.activeItemIndex,
      updateActiveItemIndex: this.updateActiveItemIndex
    }));
  }

}

_defineProperty(Carousel, "propTypes", {
  classes: PropTypes.shape({
    root: PropTypes.string,
    currentImage: PropTypes.string,
    imageContainer: PropTypes.string,
    'chevron-left': PropTypes.string,
    'chevron-right': PropTypes.string
  }),
  images: PropTypes.arrayOf(PropTypes.shape({
    label: PropTypes.string,
    position: PropTypes.number,
    disabled: PropTypes.bool,
    file: PropTypes.string.isRequired
  })).isRequired
});

export default classify(defaultClasses)(Carousel);
//# sourceMappingURL=carousel.js.map