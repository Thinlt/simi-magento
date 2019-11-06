import React from 'react';
// import Lightbox from 'react-image-lightbox';
import ReactImageVideoLightbox from 'react-image-video-lightbox';

class ImageLightbox extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            photoIndex: 0,
            isOpen: false,
        };
    }

    showLightbox = (index = 0) => {
        this.setState({
            photoIndex: index,
            isOpen: true,
        })
        ;
    }

    render() {
        const {photoIndex, isOpen} = this.state;
        if (!this.props || !this.props.images) {
            return (<div></div>);
        }
        const {images} = this.props;
        return (
            <div>
                {isOpen &&
                    <div className="simi-react-image-video-container">
                    <ReactImageVideoLightbox
                        data={images}
                        startIndex={photoIndex}
                        showResourceCount={true}
                        onCloseCallback={() => this.setState({isOpen: false})} />
                        </div>
                }
            </div>
        );
    }
}
export default ImageLightbox;