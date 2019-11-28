import React from 'react'
import { Carousel } from "react-responsive-carousel";
import 'react-responsive-carousel/lib/styles/carousel.min.css';
import Identify from "src/simi/Helper/Identify";

class Instagram extends React.Component {

    state = {
        insData: null
    }

    constructor(props) {
        super(props);
    }

    getUserInstagram = async (name) => {
        let response = await fetch(`https://www.instagram.com/${name}/?__a=1`);
        let data = await response.json();
        return data;
    }

    componentWillMount() {
        const checkDT = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'instagram');
        if (checkDT) {
            this.setState({ insData: checkDT });
        }
    }

    componentDidMount() {
        if (this.props.data && !this.state.insData) {
            const propData = this.props.data;
            this.getUserInstagram(propData).then(res => {
                this.setState({ insData: res });
                Identify.storeDataToStoreage(Identify.SESSION_STOREAGE, 'instagram', res);
            });
        }
    }

    renderInsItem = (ins) => {
        const { node } = ins;
        return <div className="ins-item" key={node.id}>
            <a href={`https://www.instagram.com/p/${node.shortcode}`} target="_blank" rel="noopener noreferrer">
                <img src={node.thumbnail_src} alt={node.accessibility_caption} />
            </a>
        </div>
    }

    renderInsView = () => {
        let html = null;
        const { isPhone, insData } = this.state;

        if (insData) {
            const { user } = insData.graphql;
            const containerStyle = isPhone ? { marginTop: 16 } : { marginTop: 32 };
            if (user && !user.is_private) {
                let slideSettings = {
                    autoPlay: false,
                    showArrows: !isPhone,
                    emulateTouch: true,
                    showThumbs: false,
                    showIndicators: false,
                    showStatus: false,
                    infiniteLoop: false,
                    rtl: Identify.isRtl(),
                    lazyLoad: true,
                    dynamicHeight: true,
                    transitionTime: 500,
                };
                if (isPhone) {
                    slideSettings['centerMode'] = true;
                    slideSettings['selectedItem'] = 1;
                    slideSettings['centerSlidePercentage'] = 47.13333333;
                }

                const { edges } = user.edge_owner_to_timeline_media;
                if (edges.length) {
                    let instagramData = [];
                    if (!isPhone) {
                        const totalSlide = Math.ceil(edges.length / 5);

                        for (let i = 0; i < totalSlide; i++) {
                            let start = 5 * i;
                            let end = 5 * (i + 1);
                            let itemSL = edges.slice(start, end).map((item, key) => {
                                return this.renderInsItem(item);
                            });
                            let slide = <div className="ins-data-container" key={Identify.randomString('key')}>
                                {itemSL}
                            </div>;
                            instagramData.push(slide);
                        }
                    } else {
                        instagramData = edges.map(ins => {
                            return this.renderInsItem(ins);
                        });
                    }

                    html = <div className="container" style={containerStyle}>
                        <div className="row">
                            <Carousel {...slideSettings}>
                                {instagramData}
                            </Carousel>
                        </div>
                    </div>
                }
            }
        }

        return html;
    }

    render() {
        const { data } = this.props;
        if (!data) {
            return null;
        }

        return (
            <div className="instagram-block-homepage">
                <div className="text-center">
                    <span className="hash-title">{data}</span>
                </div>
                {this.renderInsView()}
            </div>
        );
    }
}
export default Instagram