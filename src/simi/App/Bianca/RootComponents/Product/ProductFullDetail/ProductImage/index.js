import React from 'react';
import {Carousel} from 'react-responsive-carousel';
import Identify from "src/simi/Helper/Identify";
import ImageLightbox from "./ImageLightbox";
import memoize from 'memoize-one';
import isProductConfigurable from 'src/util/isProductConfigurable';
import { resourceUrl } from 'src/simi/Helper/Url'
// import findMatchingVariant from 'src/util/findMatchingProductVariant';
import { transparentPlaceholder } from 'src/shared/images';
// import PlayCircle from 'src/simi/App/Bianca/BaseComponents/Icon/PlayCircle';
require('./style.scss')


const $ = window.$;

class ProductImage extends React.Component {

    constructor(props) {
        super(props);
        this.title = this.props.title || 'Alt';
        this.showThumbs = this.props.showThumbs || true;
        this.showArrows = this.props.showArrows || false;
        this.showIndicators = this.props.showIndicators || false;
        this.autoPlay = this.props.autoPlay || false;
        this.showStatus = this.props.showStatus || false;
        this.itemClick = this.props.itemClick || function (e) {
        };
        this.onChange = this.props.onChange || function (e) {
        };
        this.onClickThumb = this.props.onClickThumb || function (e) {
        };
        this.defaultStatusFormatter = function defaultStatusFormatter(current, total) {
            return Identify.__('%c of %t').replace('%c', current).replace('%t', total);
        };
        this.statusFormatter = this.props.statusFormatter || this.defaultStatusFormatter;
        this.infiniteLoop = this.props.infiniteLoop || false;

    }

    openImageLightbox = (index) => {
        this.lightbox.showLightbox(index);
    }

    convertEmberVideo = (url) => {
        const vimeoPattern = /(?:http?s?:\/\/)?(?:www\.)?(?:vimeo\.com)\/?(.+)/g;
        const youtubePattern = /(?:http?s?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(.+)/g;
        if (vimeoPattern.test(url)){
            const replacement = "//player.vimeo.com/video/$1";
            return url.replace(vimeoPattern, replacement);
        }
        if (youtubePattern.test(url)){
            const replacementY = "https://www.youtube.com/embed/$1";
            return url.replace(youtubePattern, replacementY);
        }
    }

    renderImageLighboxBlock = () => {
        let images = this.images
        images = images.map((item) => {
            return (item.video_content && item.video_content instanceof Object )? { url : this.convertEmberVideo(item.video_content.video_url), type: 'video', altTag: item.video_content.video_title} : (item.file
            ? {url: window.location.origin+resourceUrl(item.file, { type: 'image-product', width: 640 }), type: 'photo', altTag: item.label}
            : {url: window.location.origin+transparentPlaceholder, type: 'photo', altTag: 'no image'})
        });
        return (
            <ImageLightbox ref={(lightbox) => {
                this.lightbox = lightbox
            }} images={images}/>
        );
    }

    renderImage() {
        const width = $('.left-layout.product-media').width();
        const {product} = this.props;
        return this.images.map(function (item) {
            const src = item.file
                ? resourceUrl(item.file, { type: 'image-product', width: 640 })
                : transparentPlaceholder
            const noImage = item.file ? 'no-image' : null;
            return (
                <div key={Identify.randomString(5)} style={{cursor: 'pointer', backgroundColor: '#ffffff'}} className="carousel-image-container">
                    {
                        item.video_content ? 
                        <img className={`video-thumb ${noImage}`} width={width} src={src} height={width} alt={product.name}
                                style={{objectFit: 'scale-down'}}
                        /> :
                        <img width={width} src={src} height={width} alt={product.name}
                            style={{objectFit: 'scale-down'}}
                        />
                    }
                </div>
            );
        })
    }

    onChangeItemDefault = () => {
        
    }

    onClickThumbDefault = () => {
        
    }

    sortAndFilterImages = memoize(items =>
        items
            .filter(i => !i.disabled)
            .sort((a, b) => {
                const aPos = isNaN(a.position) ? 9999 : a.position;
                const bPos = isNaN(b.position) ? 9999 : b.position;
                return aPos - bPos;
            })
    );

    findMatchingVariant = ({ variants, optionCodes, optionSelections }) => {
        const findCode = 'color';
        let codeValue = '';
        for (const [id, value] of optionSelections) {
            const code = optionCodes.get(id);
            if (code === findCode) { //fix bug change option is size and change image
                codeValue = value;
                break;
            }
        }
        let productItem;
        if(codeValue){
            variants.forEach((item) => {
                const { attributes, product } = item;
                const attrCodeValues = (attributes || []).reduce(
                    (map, { code, value_index }) => new Map(map).set(code, value_index),
                    new Map()
                );
                if(attrCodeValues && attrCodeValues.size && attrCodeValues.get(findCode) === codeValue){
                    productItem = item;
                    return false;
                }
            });
        }
        return productItem;
    };

    mediaGalleryEntries = () => {
        const { props } = this;
        const { optionCodes, optionSelections, product } = props;
        const { variants } = product;
        const isConfigurable = isProductConfigurable(product);

        const media_gallery_entries = product.media_gallery_entries ? 
                product.media_gallery_entries :  product.small_image ? 
                    [{file: product.small_image, disabled: false, label: '', position: 1}] : []
                    
        if (
            !isConfigurable ||
            (isConfigurable && optionSelections.size === 0)
        ) {
            return media_gallery_entries;
        }

        const item = this.findMatchingVariant({
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


    sortedImages() {
        const images= this.mediaGalleryEntries();
        return this.sortAndFilterImages(images);
    }

    renderJs = () => {
        $(document).ready(function () {
            const carousel = $('.carousel.carousel-slider');
            const mediaWidth = carousel.width();
            carousel.height(mediaWidth);
            $('.carousel.carousel-slider img').height(mediaWidth);
        });
    }

    render() {
        this.images = this.sortedImages()
        if (!this.images) {
            return null
        }
        let selectedItem = 0;
        let renderImages = this.renderImage()
        if (Identify.isRtl()) {
            renderImages.reverse();
            selectedItem = (renderImages.length - 1)
        }
        const {images} = this
        return (
            <React.Fragment>
                <Carousel className="product-carousel"
                        selectedItem={selectedItem}
                        key={(images && images[0] && images[0].file) ? images[0].file : Identify.randomString(5)}
                        showArrows={this.showArrows}  
                        showThumbs={this.showThumbs}
                        showIndicators={this.showIndicators}
                        showStatus={this.showStatus}
                        onClickItem={(e) => this.openImageLightbox(e)}
                        onClickThumb={(e) => this.onClickThumbDefault(e)}
                        onChange={(e) => this.onChangeItemDefault(e)}
                        infiniteLoop={true}
                        autoPlay={this.autoPlay}
                        thumbWidth={82}
                        statusFormatter={this.statusFormatter}
                    >
                    {renderImages}
                </Carousel>
                {this.renderImageLighboxBlock()}
            </React.Fragment>
        );
    }
}

export default ProductImage;