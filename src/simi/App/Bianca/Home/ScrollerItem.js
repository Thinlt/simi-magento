import React from 'react'

const ScrollerItem = props => {
    const { history, isPhone, renderItem, item, index } = props;
    const {title, image, image_tablet, url} = item || {};

    const handleClick = (e) => {
        if (url) {
            if(props.onClick){
                props.onClick(e, item, index);
            }
            history.push(url);
        }
        e.preventDefault();
    }

    if (renderItem) {
        return renderItem(item, index);
    }

    let img = '';
    if(isPhone && image_tablet) {
        img = image_tablet;
    } else {
        img = image
    }
    if(!img) return null

    return (
        <div className="item" onClick={handleClick}>
            { title &&  <div className="title">{title}</div>}
            <img className="img-responsive" src={img} alt={title}/>
        </div>
    )
}

export default ScrollerItem;