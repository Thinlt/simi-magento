import React, { Component } from 'react';
import PropTypes from 'prop-types';
import memoize from 'memoize-one';

import { resourceUrl } from 'src/simi/Helper/Url'
import Icon from 'src/components/Icon';
import ChevronLeftIcon from 'react-feather/dist/icons/chevron-left';
import ChevronRightIcon from 'react-feather/dist/icons/chevron-right';
import classify from 'src/classify';
import ThumbnailList from './thumbnailList';
import defaultClasses from './carousel.css';
import { transparentPlaceholder } from 'src/shared/images';
import isProductConfigurable from 'src/util/isProductConfigurable';
import findMatchingVariant from 'src/util/findMatchingProductVariant';

const ChevronIcons = {
    left: ChevronLeftIcon,
    right: ChevronRightIcon
};

class Carousel extends Component {
    static propTypes = {
        classes: PropTypes.shape({
            root: PropTypes.string,
            currentImage: PropTypes.string,
            imageContainer: PropTypes.string,
            'chevron-left': PropTypes.string,
            'chevron-right': PropTypes.string
        }),
    };

    state = {
        activeItemIndex: 0,
        currentImageLoaded: false
    };

    updateActiveItemIndex = index => {
        this.setState({ activeItemIndex: index });
    };

    sortAndFilterImages = memoize(items =>
        items
            .filter(i => !i.disabled)
            .sort((a, b) => {
                const aPos = isNaN(a.position) ? 9999 : a.position;
                const bPos = isNaN(b.position) ? 9999 : b.position;
                return aPos - bPos;
            })
    );


    mediaGalleryEntries = () => {
        const { props } = this;
        const { optionCodes, optionSelections, product } = props;
        const { media_gallery_entries, variants } = product;
        const isConfigurable = isProductConfigurable(product);

        if (
            !isConfigurable ||
            (isConfigurable && optionSelections.size === 0)
        ) {
            return media_gallery_entries;
        }

        const item = findMatchingVariant({
            optionCodes,
            optionSelections,
            variants
        });

        if (!item) {
            return media_gallery_entries;
        }

        const images = [
            ...item.product.media_gallery_entries,
            ...media_gallery_entries
        ];
        const returnedImages = []
        var obj = {};
        images.forEach(image=> {
            if (!obj[image.file]) {
                obj[image.file] = true
                returnedImages.push(image)
            }
        })

        return returnedImages
    }

    get sortedImages() {
        const images= this.mediaGalleryEntries();
        return this.sortAndFilterImages(images);
    }

    leftChevronHandler = () => {
        const sortedImages = this.sortedImages;
        const { activeItemIndex } = this.state;
        activeItemIndex > 0
            ? this.updateActiveItemIndex(activeItemIndex - 1)
            : this.updateActiveItemIndex(sortedImages.length - 1);
    };

    rightChevronHandler = () => {
        const sortedImages = this.sortedImages;
        const { activeItemIndex } = this.state;
        this.updateActiveItemIndex((activeItemIndex + 1) % sortedImages.length);
    };

    getChevron = direction => (
        <button
            onClick={this[`${direction}ChevronHandler`]}
            className={this.props.classes[`chevron-${direction}`]}
        >
            <Icon src={ChevronIcons[direction]} size={40} />
        </button>
    );

    setCurrentImageLoaded = () => {
        this.setState({
            currentImageLoaded: true
        });
    };

    render() {
        const { classes } = this.props;
        const sortedImages = this.sortedImages;

        const mainImage = sortedImages[this.state.activeItemIndex] || {};
        const src = mainImage.file
            ? resourceUrl(mainImage.file, { type: 'image-product', width: 640 })
            : transparentPlaceholder;
        const alt = mainImage.label || 'image-product';
        return (
            <div className={classes.root}>
                <div className={classes.imageContainer}>
                    {this.getChevron('left')}
                    <img
                        onLoad={this.setCurrentImageLoaded}
                        className={classes.currentImage}
                        src={src}
                        alt={alt}
                    />
                    {!this.state.currentImageLoaded ? (
                        <img
                            className={classes.currentImage}
                            src={transparentPlaceholder}
                            alt={alt}
                        />
                    ) : null}
                    {this.getChevron('right')}
                </div>
                <ThumbnailList
                    items={sortedImages}
                    activeItemIndex={this.state.activeItemIndex}
                    updateActiveItemIndex={this.updateActiveItemIndex}
                />
            </div>
        );
    }
}

export default classify(defaultClasses)(Carousel);
