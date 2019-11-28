import React from 'react'

const ScrollerItem = props => {
    const { history, isPhone, renderItem, item } = props;
    const {title, image, image_tablet, url} = item || {};

    const handleClick = (e) => {
        if (url) {
            history.push(url);
        }
        e.preventDefault();
    }

    let img = '';
    if(isPhone && image_tablet) {
        img = image_tablet;
    } else {
        img = image
    }
    if(!img) return null

    if (renderItem) {
        <renderItem {...props}/>
    }

    return (
        <div className="item" onClick={handleClick}>
            { title &&  <div className="title">{title}</div>}
            <img className="img-responsive" src={img} alt={title}/>
        </div>
    )
}

export default ScrollerItem;